<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_user_profile_switch_plan_activate_and_reset_password(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'tenant_id' => null,
        ]);

        $tenant = Tenant::create([
            'name' => 'Old Store',
            'slug' => 'old-store',
            'subdomain' => 'old-store',
            'plan' => 'starter',
            'status' => 'pending',
        ]);

        $merchant = User::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '08000000000',
            'role' => 'admin',
        ]);

        $this->actingAs($superAdmin)
            ->put(route('super-admin.users.update', $merchant), [
                'name' => 'Updated Owner',
                'email' => 'updated@example.com',
                'phone' => '+234 904 668 6176',
                'store_name' => 'Updated Store',
                'plan' => 'enterprise',
                'status' => 'active',
            ])
            ->assertRedirect();

        $merchant->refresh();
        $tenant->refresh();

        $this->assertSame('Updated Owner', $merchant->name);
        $this->assertSame('updated@example.com', $merchant->email);
        $this->assertSame('+234 904 668 6176', $merchant->phone);
        $this->assertSame('Updated Store', $tenant->name);
        $this->assertSame('enterprise', $tenant->plan);
        $this->assertSame('active', $tenant->status);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.users.reset-password', $merchant), [
                'password' => 'new-secret-123',
                'password_confirmation' => 'new-secret-123',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('new-secret-123', $merchant->fresh()->password));
    }

    public function test_regular_store_admin_cannot_manage_or_reset_other_users(): void
    {
        $tenant = Tenant::create([
            'name' => 'Store One',
            'slug' => 'store-one',
            'subdomain' => 'store-one',
            'plan' => 'starter',
            'status' => 'active',
        ]);

        $storeAdmin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);

        $otherUser = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'staff',
        ]);

        $this->actingAs($storeAdmin)
            ->put(route('super-admin.users.update', $otherUser), [
                'name' => 'Hacked',
                'email' => 'hacked@example.com',
            ])
            ->assertForbidden();

        $this->actingAs($storeAdmin)
            ->post(route('super-admin.users.reset-password', $otherUser), [
                'password' => 'new-secret-123',
                'password_confirmation' => 'new-secret-123',
            ])
            ->assertForbidden();
    }
}
