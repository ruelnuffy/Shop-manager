<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/super-admin');
        $response->assertRedirect('/login');

        $responseTenants = $this->get('/super-admin/tenants');
        $responseTenants->assertRedirect('/login');
    }

    public function test_regular_customer_is_forbidden_from_super_admin(): void
    {
        $customer = User::withoutTenancy()->where('email', 'customer@jessiclassiq.com')->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get('/super-admin');
        $response->assertStatus(403);

        $responseTenants = $this->actingAs($customer)->get('/super-admin/tenants');
        $responseTenants->assertStatus(403);
    }

    public function test_regular_staff_is_forbidden_from_super_admin(): void
    {
        $staff = User::withoutTenancy()->where('email', 'staff@jessiclassiq.com')->first();
        $this->assertNotNull($staff);

        $response = $this->actingAs($staff)->get('/super-admin');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_dashboard_and_view_metrics(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->isSuperAdmin());

        $response = $this->actingAs($superAdmin)->get('/super-admin');
        $response->assertStatus(200);
        $response->assertSee('Super Admin Cockpit');
        $response->assertSee('Platform Overview');
        $response->assertSee('Tenant Stores');
    }

    public function test_super_admin_can_view_tenants_directory(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->get('/super-admin/tenants');
        $response->assertStatus(200);
        $response->assertSee('Tenant Stores Directory');
        $response->assertSee('Jessi&#039;s ClassiQ Store', false);
    }

    public function test_super_admin_can_provision_new_tenant_store_and_admin(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->post('/super-admin/tenants', [
            'name' => 'Aura Luxury Boutique',
            'slug' => 'aura-luxury',
            'subdomain' => 'auraluxury',
            'plan' => 'pro',
            'status' => 'active',
            'admin_name' => 'Aura Admin',
            'admin_email' => 'owner@auraluxury.com',
            'admin_password' => 'secretPassword123!',
            'features' => [
                'storefront' => '1',
                'payroll' => '1',
            ],
        ]);

        $tenant = Tenant::where('slug', 'aura-luxury')->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Aura Luxury Boutique', $tenant->name);
        $this->assertEquals('pro', $tenant->plan);
        $this->assertTrue($tenant->hasFeature('storefront'));
        $this->assertTrue($tenant->hasFeature('payroll'));

        $owner = User::withoutTenancy()->where('email', 'owner@auraluxury.com')->first();
        $this->assertNotNull($owner);
        $this->assertEquals($tenant->id, $owner->tenant_id);
        $this->assertEquals('admin', $owner->role);

        $response->assertRedirect(route('super-admin.tenants.show', $tenant));
    }

    public function test_super_admin_can_view_tenant_details(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        $response = $this->actingAs($superAdmin)->get(route('super-admin.tenants.show', $tenant));
        $response->assertStatus(200);
        $response->assertSee($tenant->name);
        $response->assertSee('Store Administrators');
    }

    public function test_super_admin_can_edit_tenant_settings(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        $response = $this->actingAs($superAdmin)->put(route('super-admin.tenants.update', $tenant), [
            'name' => 'Jessi ClassiQ Global',
            'slug' => $tenant->slug,
            'subdomain' => 'jessiglobal',
            'domain' => 'www.jessiclassiq.com',
            'plan' => 'enterprise',
            'status' => 'active',
            'tagline' => 'Global Haute Couture',
            'features' => [
                'storefront' => '1',
                'ocr' => '1',
            ],
        ]);

        $response->assertRedirect(route('super-admin.tenants.show', $tenant));

        $tenant->refresh();
        $this->assertEquals('Jessi ClassiQ Global', $tenant->name);
        $this->assertEquals('enterprise', $tenant->plan);
        $this->assertEquals('www.jessiclassiq.com', $tenant->domain);
        $this->assertTrue($tenant->hasFeature('storefront'));
        $this->assertTrue($tenant->hasFeature('ocr'));
    }

    public function test_super_admin_can_toggle_tenant_status(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();
        $this->assertEquals('active', $tenant->status);

        $response = $this->actingAs($superAdmin)->post(route('super-admin.tenants.toggle-status', $tenant));
        $response->assertSessionHas('status');

        $tenant->refresh();
        $this->assertEquals('suspended', $tenant->status);

        // Toggle back to active
        $this->actingAs($superAdmin)->post(route('super-admin.tenants.toggle-status', $tenant));
        $tenant->refresh();
        $this->assertEquals('active', $tenant->status);
    }

    public function test_super_admin_can_enter_and_impersonate_tenant(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        $response = $this->actingAs($superAdmin)->post(route('super-admin.tenants.enter', $tenant));
        $response->assertRedirect('/admin?tenant='.$tenant->slug);
        $this->assertEquals($tenant->id, session('active_tenant_id'));
    }
}
