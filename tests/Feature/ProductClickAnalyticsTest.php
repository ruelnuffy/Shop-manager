<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductClickAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewing_product_increments_link_clicks_and_admin_analytics_ranks_best_sellers(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $admin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);
        app(CurrentTenant::class)->set($tenant);

        $category = Category::create([
            'tenant_id' => $tenant->id,
            'name' => 'Bags',
            'slug' => 'bags-analytics-'.$tenant->id,
        ]);

        $topProduct = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Signature Tote',
            'slug' => 'signature-tote-'.$tenant->id,
            'base_price' => 25000,
            'status' => 'active',
        ]);
        $topVariant = ProductVariant::create([
            'tenant_id' => $tenant->id,
            'product_id' => $topProduct->id,
            'sku' => 'SIG-1',
            'size' => 'Standard',
            'color' => 'Black',
            'price_override' => 25000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $otherProduct = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Mini Clutch',
            'slug' => 'mini-clutch-'.$tenant->id,
            'base_price' => 12000,
            'status' => 'active',
        ]);

        // Simulate 2 link clicks on Signature Tote
        $this->withSession(['active_tenant_id' => $tenant->id])
            ->get(route('products.show', $topProduct->slug))
            ->assertOk();
        $this->withSession(['active_tenant_id' => $tenant->id])
            ->get(route('products.show', $topProduct->slug))
            ->assertOk();

        $this->assertSame(2, $topProduct->fresh()->link_clicks);
        $this->assertSame(0, $otherProduct->fresh()->link_clicks);

        // Simulate 90 seconds active time on Signature Tote
        $this->withSession(['active_tenant_id' => $tenant->id])
            ->postJson(route('products.engagement', $topProduct->slug), ['seconds' => 90])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertSame(90, $topProduct->fresh()->total_time_seconds);

        // Simulate 3 units sold for Signature Tote
        $order = Order::create([
            'tenant_id' => $tenant->id,
            'order_number' => 'SM-ANA-1001',
            'status' => 'completed',
            'subtotal' => 75000,
            'shipping_fee' => 3500,
            'tax' => 0,
            'total_amount' => 78500,
            'shipping_name' => 'Ada Buyer',
            'shipping_email' => 'ada@example.com',
            'shipping_phone' => '08012345678',
            'shipping_address' => '12 Main St',
            'shipping_city' => 'Lagos',
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $topVariant->id,
            'product_name' => $topProduct->name,
            'variant_details' => 'Standard / Black',
            'quantity' => 3,
            'unit_price' => 25000,
            'subtotal' => 75000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.product-analytics.index'))
            ->assertOk()
            ->assertSee('Product Clicks &amp; Best-Seller Suggestions', false)
            ->assertSee('Signature Tote')
            ->assertSee('2 clicks')
            ->assertSee('45s')
            ->assertSee('3 sold');

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('2 clicks')
            ->assertSee(route('admin.product-analytics.index'));
    }
}
