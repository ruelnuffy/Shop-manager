<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminUserDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_platform_users_directory(): void
    {
        $response = $this->get('/super-admin/users');
        $response->assertRedirect('/login');
    }

    public function test_store_admin_or_staff_cannot_access_platform_users_directory(): void
    {
        $admin = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/super-admin/users');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_platform_users_directory(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->get('/super-admin/users');
        $response->assertStatus(200);
        $response->assertSee('Platform Users &amp; Merchants Directory', false);
        $response->assertSee('Subscribed Users');
        $response->assertSee('Free &amp; Trial Users', false);
        $response->assertSee('admin@jessiclassiq.com');
    }

    public function test_super_admin_can_filter_users_by_subscription_tier(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        // Create a subscribed tenant (Pro) and merchant user
        $paidTenant = Tenant::create([
            'name' => 'Luxe Couture Hub',
            'slug' => 'luxe-couture',
            'subdomain' => 'luxecouture',
            'plan' => 'pro',
            'status' => 'active',
        ]);
        $paidUser = User::create([
            'tenant_id' => $paidTenant->id,
            'name' => 'Luxe Merchant',
            'email' => 'merchant@luxecouture.com',
            'password' => 'secret12345',
            'role' => 'admin',
        ]);

        // Filter: Subscribed
        $responseSubscribed = $this->actingAs($superAdmin)->get('/super-admin/users?tier=subscribed');
        $responseSubscribed->assertStatus(200);
        $this->assertTrue($responseSubscribed->viewData('users')->contains('email', 'merchant@luxecouture.com'));
        $responseSubscribed->assertSee('Subscribed');

        // Filter: Free
        $responseFree = $this->actingAs($superAdmin)->get('/super-admin/users?tier=free');
        $responseFree->assertStatus(200);
        $this->assertFalse($responseFree->viewData('users')->contains('email', 'merchant@luxecouture.com'));
    }

    public function test_super_admin_can_filter_tenants_by_subscription_tier(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        // Ensure we have a subscribed store
        Tenant::create([
            'name' => 'Apex Footwear',
            'slug' => 'apex-footwear',
            'subdomain' => 'apexfootwear',
            'plan' => 'enterprise',
            'status' => 'active',
        ]);

        $responseSubscribed = $this->actingAs($superAdmin)->get('/super-admin/tenants?tier=subscribed');
        $responseSubscribed->assertStatus(200);
        $this->assertTrue($responseSubscribed->viewData('tenants')->contains('slug', 'apex-footwear'));

        $responseFree = $this->actingAs($superAdmin)->get('/super-admin/tenants?tier=free');
        $responseFree->assertStatus(200);
        $this->assertFalse($responseFree->viewData('tenants')->contains('slug', 'apex-footwear'));
    }

    public function test_super_admin_can_impersonate_user_from_users_directory(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $merchant = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->first();
        $this->assertNotNull($merchant);

        $response = $this->actingAs($superAdmin)->post("/super-admin/users/{$merchant->id}/impersonate");

        $this->assertAuthenticatedAs($merchant);
        $response->assertRedirect('/admin');
    }

    public function test_new_user_can_sign_up_with_selected_plan_and_reach_store_dashboard(): void
    {
        // Clear active tenant to simulate platform signup
        app(CurrentTenant::class)->forget();

        $response = $this->post('/register', [
            'store_name' => 'Bella Lagos Boutique',
            'name' => 'Bella Adenuga',
            'email' => 'bella@bellaboutique.ng',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'plan' => 'pro',
        ]);

        $this->assertAuthenticated();

        $user = User::withoutTenancy()->where('email', 'bella@bellaboutique.ng')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Bella Adenuga', $user->name);
        $this->assertEquals('admin', $user->role);
        $this->assertNotNull($user->tenant);
        $this->assertEquals('pro', $user->tenant->plan);
        $this->assertEquals('Bella Lagos Boutique', $user->tenant->name);

        $response->assertRedirect('/admin');
    }
}
