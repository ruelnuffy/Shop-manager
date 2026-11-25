<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_navbar_search_filters_products_by_search_and_q_params(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        app(CurrentTenant::class)->set($tenant);

        $category = Category::create([
            'tenant_id' => $tenant->id,
            'name' => 'Accessories',
            'slug' => 'accessories-'.$tenant->id,
        ]);

        Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Leather Tote Bag',
            'slug' => 'leather-tote-'.$tenant->id,
            'base_price' => 25000,
            'status' => 'active',
        ]);

        Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Silk Headscarf',
            'slug' => 'silk-headscarf-'.$tenant->id,
            'base_price' => 8000,
            'status' => 'active',
        ]);

        $this->withSession(['active_tenant_id' => $tenant->id])
            ->get(route('products.index', ['search' => 'Leather']))
            ->assertOk()
            ->assertSee('Leather Tote Bag')
            ->assertDontSee('Silk Headscarf');

        $this->withSession(['active_tenant_id' => $tenant->id])
            ->get(route('products.index', ['q' => 'Silk']))
            ->assertOk()
            ->assertSee('Silk Headscarf')
            ->assertDontSee('Leather Tote Bag');
    }
}
