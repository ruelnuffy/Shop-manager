<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_super_admin_can_login_via_credentials_and_access_dashboard_with_session(): void
    {
        $loginResponse = $this->post('/login', [
            'email' => 'superadmin@shopmanager.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $loginResponse->assertRedirect('/super-admin');

        $dashboardResponse = $this->get('/super-admin');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Super Admin Cockpit');
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

    public function test_super_admin_visiting_member_dashboard_is_redirected_to_super_admin(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $this->assertNotNull($superAdmin);

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertRedirect(route('super-admin.dashboard'));
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

    public function test_super_admin_can_export_audit_report_csv(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('super-admin.export-report'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Tenant ID', $response->getContent());
        $this->assertStringContainsString('jessis-classiq-store', $response->getContent());
    }

    public function test_super_admin_can_purge_cache(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->post(route('super-admin.clear-cache'));
        $response->assertSessionHas('status');
    }

    public function test_super_admin_can_add_additional_admin_to_tenant(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        $response = $this->actingAs($superAdmin)->post(route('super-admin.tenants.admins.store', $tenant), [
            'name' => 'Secondary Store Manager',
            'email' => 'co-owner@store.test',
            'password' => 'secretPassword123!',
            'password_confirmation' => 'secretPassword123!',
        ]);

        $response->assertSessionHas('status');
        $newAdmin = User::withoutTenancy()->where('email', 'co-owner@store.test')->first();
        $this->assertNotNull($newAdmin);
        $this->assertEquals($tenant->id, $newAdmin->tenant_id);
        $this->assertEquals('admin', $newAdmin->role);
    }

    public function test_super_admin_can_reset_tenant_admin_password(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();
        $admin = User::withoutTenancy()->where('tenant_id', $tenant->id)->where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($superAdmin)->post(route('super-admin.tenants.admins.reset-password', [$tenant, $admin]), [
            'password' => 'newSecretPassword456!',
            'password_confirmation' => 'newSecretPassword456!',
        ]);

        $response->assertSessionHas('status');
        $admin->refresh();
        $this->assertTrue(Hash::check('newSecretPassword456!', $admin->password));
    }

    public function test_super_admin_cannot_delete_the_sole_admin_of_a_store(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();
        $admin = User::withoutTenancy()->where('tenant_id', $tenant->id)->where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // Ensure only 1 admin exists
        User::withoutTenancy()->where('tenant_id', $tenant->id)->where('role', 'admin')->where('id', '!=', $admin->id)->delete();

        $response = $this->actingAs($superAdmin)->delete(route('super-admin.tenants.admins.destroy', [$tenant, $admin]));
        $response->assertSessionHasErrors('admin_error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_super_admin_can_remove_an_admin_if_multiple_exist(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        // Create a second admin
        $secondAdmin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin To Remove',
            'email' => 'toremove@store.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($superAdmin)->delete(route('super-admin.tenants.admins.destroy', [$tenant, $secondAdmin]));
        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('users', ['id' => $secondAdmin->id]);
    }

    public function test_super_admin_can_filter_tenant_analytics_by_period(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        $response = $this->actingAs($superAdmin)->get(route('super-admin.tenants.show', [$tenant, 'period' => '7_days']));
        $response->assertStatus(200);
        $response->assertSee('Store Sales &amp; Performance Reports', false);
        $response->assertSee('Period Revenue');
    }

    public function test_super_admin_can_provision_store_with_full_brand_identity_and_colors(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->post('/super-admin/tenants', [
            'name' => 'Zenith Luxe Enterprises',
            'slug' => 'zenith-luxe',
            'subdomain' => 'zenith',
            'plan' => 'pro',
            'status' => 'active',
            'admin_name' => 'Zenith Owner',
            'admin_email' => 'admin@zenithluxe.com',
            'admin_password' => 'secretPassword123!',
            'brand_name' => 'Zenith Luxe',
            'tagline' => 'Handcrafted Haute Couture & Accessories',
            'logo_url' => 'https://cdn.example.com/zenith-logo.png',
            'primary_color' => '#059669',
            'secondary_color' => '#064e3b',
            'accent_color' => '#d1fae5',
            'invoice_header' => 'Zenith Luxe Flagship Boutique',
            'currency' => 'NGN',
            'currency_symbol' => '₦',
            'features' => [
                'storefront' => '1',
            ],
        ]);

        $tenant = Tenant::where('slug', 'zenith-luxe')->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Zenith Luxe Enterprises', $tenant->name);
        $this->assertEquals('Zenith Luxe', $tenant->setting('branding.name'));
        $this->assertEquals('Handcrafted Haute Couture & Accessories', $tenant->setting('branding.tagline'));
        $this->assertEquals('https://cdn.example.com/zenith-logo.png', $tenant->setting('branding.logo_url'));
        $this->assertEquals('#059669', $tenant->setting('branding.palette.primary'));
        $this->assertEquals('#064e3b', $tenant->setting('branding.palette.secondary'));
        $this->assertEquals('#d1fae5', $tenant->setting('branding.palette.accent'));
        $this->assertEquals('Zenith Luxe Flagship Boutique', $tenant->setting('branding.invoice_header'));

        $response->assertRedirect(route('super-admin.tenants.show', $tenant));
    }

    public function test_super_admin_can_update_store_branding_and_palette(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();
        $tenant = Tenant::first();

        $response = $this->actingAs($superAdmin)->put(route('super-admin.tenants.update', $tenant), [
            'name' => 'Jessi ClassiQ Global',
            'slug' => $tenant->slug,
            'subdomain' => 'jessiglobal',
            'plan' => 'enterprise',
            'status' => 'active',
            'brand_name' => 'ClassiQ World',
            'tagline' => 'The Pinnacle of African Elegance',
            'primary_color' => '#e06383',
            'secondary_color' => '#1a1a1a',
            'accent_color' => '#fbe6eb',
            'invoice_header' => 'ClassiQ World Maison',
            'currency' => 'USD',
            'currency_symbol' => '$',
        ]);

        $response->assertRedirect(route('super-admin.tenants.show', $tenant));

        $tenant->refresh();
        $this->assertEquals('ClassiQ World', $tenant->setting('branding.name'));
        $this->assertEquals('The Pinnacle of African Elegance', $tenant->setting('branding.tagline'));
        $this->assertEquals('#e06383', $tenant->setting('branding.palette.primary'));
        $this->assertEquals('ClassiQ World Maison', $tenant->setting('branding.invoice_header'));
        $this->assertEquals('USD', $tenant->setting('currency'));
        $this->assertEquals('$', $tenant->setting('currency_symbol'));
    }
}
