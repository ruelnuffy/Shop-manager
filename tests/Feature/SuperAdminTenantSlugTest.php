<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTenantSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_store_validates_normalized_slug_instead_of_throwing_duplicate_key_error(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        Tenant::factory()->create([
            'name' => 'Existing Store',
            'slug' => 'style-for-everyone',
            'subdomain' => 'style-for-everyone',
        ]);

        $this->actingAs($superAdmin)
            ->from(route('super-admin.tenants.create'))
            ->post(route('super-admin.tenants.store'), [
                'name' => 'New Store',
                'slug' => 'Style_for_everyone',
                'subdomain' => '',
                'plan' => 'starter',
                'status' => 'active',
                'admin_name' => 'Rinret',
                'admin_email' => 'rinret@example.com',
                'admin_password' => 'shopmanager2026',
            ])
            ->assertRedirect(route('super-admin.tenants.create'))
            ->assertSessionHasErrors('slug');
    }
}
