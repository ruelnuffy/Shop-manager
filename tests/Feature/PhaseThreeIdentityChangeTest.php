<?php

namespace Tests\Feature;

use App\Exceptions\PaymentsNotConfiguredException;
use App\Http\Middleware\ResolveTenant;
use App\Models\ShopLocation;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PhaseThreeIdentityChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_platform_name_is_configurable_and_defaults_to_shopmanager(): void
    {
        $this->assertEquals('ShopManager', config('app.platform_name'));
        $this->assertEquals('ShopManager', config('app.name'));

        config(['app.platform_name' => 'CustomPlatform']);
        $this->assertEquals('CustomPlatform', config('app.platform_name'));
    }

    public function test_platform_landing_page_renders_neutral_identity_when_no_tenant(): void
    {
        // When accessing root with explicit platform request (no tenant)
        $response = $this->withHeaders(['X-Platform' => 'true'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('ShopManager');
        $response->assertSee('Unified Retail, Inventory');
        $response->assertDontSee("Jessi's ClassiQ Store &mdash; Luxury Boutique", false);
    }

    public function test_tenant_storefront_renders_when_tenant_has_storefront_feature(): void
    {
        // Default tenant (Jessi's) has storefront feature enabled
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->first();
        $this->assertNotNull($tenant);
        $this->assertTrue($tenant->hasFeature('storefront'));

        $response = $this->withHeaders(['X-Tenant' => 'jessis-classiq-store'])->get('/storefront');

        $response->assertStatus(200);
        $response->assertSee("Jessi's ClassiQ Store", false);
        $response->assertSee('Clothes');
        $response->assertSee('Bags');
    }

    public function test_tenant_without_storefront_feature_falls_back_to_platform_welcome(): void
    {
        $tenantWithoutStorefront = Tenant::create([
            'name' => 'Backoffice Only Tenant',
            'slug' => 'backoffice-only',
            'subdomain' => 'backoffice',
            'plan' => 'standard',
            'status' => 'active',
            'settings' => [
                'features' => [
                    'storefront' => false,
                    'payroll' => true,
                ],
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ShopManager');
        $response->assertSee('Unified Retail, Inventory');
    }

    public function test_platform_login_and_register_chrome_are_neutral_when_no_tenant(): void
    {
        $loginResponse = $this->withHeaders(['X-Platform' => 'true'])->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('ShopManager');
        $loginResponse->assertSee('Sign In to Platform');

        $registerResponse = $this->withHeaders(['X-Platform' => 'true'])->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('ShopManager');
        $registerResponse->assertSee('Launch Your Store');
    }

    public function test_jessi_tenant_settings_and_lagos_location_migrated(): void
    {
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->first();
        $this->assertNotNull($tenant);

        // Check settings branding
        $this->assertEquals("Jessi's ClassiQ Store", $tenant->setting('branding.name'));
        $this->assertEquals('#c5a880', $tenant->setting('branding.palette.primary'));
        $this->assertNotEmpty($tenant->setting('branding.invoice_header'));

        // Check feature flags
        $this->assertTrue($tenant->hasFeature('storefront'));
        $this->assertTrue($tenant->hasFeature('payroll'));

        // Check Lagos showroom location exists
        $lagosLocation = ShopLocation::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('city', 'Lagos')
            ->first();

        $this->assertNotNull($lagosLocation);
        $this->assertEquals('jessi-boutique-lagos', $lagosLocation->slug);
    }

    public function test_default_tenant_without_paystack_keys_cannot_fall_back_to_platform_keys(): void
    {
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->first();
        $tenant->update([
            'paystack_secret_key' => null,
            'paystack_public_key' => null,
        ]);

        app(CurrentTenant::class)->set($tenant);
        $paystack = app(PaystackService::class);

        // Even default tenant cannot use platform keys for storefront payments
        $this->expectException(PaymentsNotConfiguredException::class);
        $this->expectExceptionMessage('Paystack payment keys are not configured for this store.');
        $paystack->getSecretKey();
    }

    public function test_platform_keys_are_retrievable_for_subscription_billing(): void
    {
        config(['services.paystack.secret_key' => 'sk_live_platform_secret_key_123']);
        config(['services.paystack.public_key' => 'pk_live_platform_public_key_123']);

        $paystack = app(PaystackService::class);

        $this->assertEquals('sk_live_platform_secret_key_123', $paystack->getPlatformSecretKey());
        $this->assertEquals('pk_live_platform_public_key_123', $paystack->getPlatformPublicKey());
    }

    public function test_seeded_demo_accounts_include_neutral_shopmanager_test_addresses(): void
    {
        $admin = User::where('email', 'admin@shopmanager.test')->first();
        $staff = User::where('email', 'staff@shopmanager.test')->first();
        $customer = User::where('email', 'customer@shopmanager.test')->first();

        $this->assertNotNull($admin);
        $this->assertEquals('ShopManager Admin', $admin->name);
        $this->assertTrue($admin->isAdmin());

        $this->assertNotNull($staff);
        $this->assertEquals('ShopManager Staff', $staff->name);
        $this->assertTrue($staff->isStaff());

        $this->assertNotNull($customer);
        $this->assertEquals('ShopManager Customer', $customer->name);
        $this->assertTrue($customer->isCustomer());
    }

    public function test_local_default_tenant_slug_does_not_resolve_in_production(): void
    {
        config(['tenancy.base_domain' => 'shopmanager.com']);
        $this->app->detectEnvironment(fn () => 'production');

        $middleware = app(ResolveTenant::class);

        // An unknown store subdomain on the base domain must 404 in production, never fall back
        $request = Request::create('http://random-unregistered-store.shopmanager.com/products', 'GET');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Store not found.');

        $middleware->handle($request, function () {
            return response('OK');
        });
    }

    public function test_localhost_in_local_environment_defaults_to_platform_landing_page(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        app(CurrentTenant::class)->forget();

        $middleware = app(ResolveTenant::class);
        $request = Request::create('http://localhost:8001/', 'GET');

        $middleware->handle($request, function () {
            $this->assertNull(app(CurrentTenant::class)->get());

            return response('OK');
        });
    }

    public function test_platform_merchant_onboarding_creates_new_tenant_and_admin_user(): void
    {
        $response = $this->withHeaders(['X-Platform' => 'true'])->post('/register', [
            'store_name' => 'Aura Retail & Co',
            'name' => 'Alex Morgan',
            'email' => 'alex@auraretail.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticated();

        $tenant = Tenant::where('slug', 'aura-retail-co')->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('Aura Retail & Co', $tenant->name);
        $this->assertTrue($tenant->hasFeature('storefront'));
        $this->assertTrue($tenant->hasFeature('payroll'));

        $user = User::withoutTenancy()->where('email', 'alex@auraretail.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals($tenant->id, $user->tenant_id);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals($tenant->id, session('active_tenant_id'));
    }

    public function test_platform_login_resolves_user_tenant_and_redirects_admin_to_store_admin(): void
    {
        $response = $this->withHeaders(['X-Platform' => 'true'])->post('/login', [
            'email' => 'admin@shopmanager.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertAuthenticated();

        $jessiTenant = Tenant::where('slug', 'jessis-classiq-store')->first();
        $this->assertEquals($jessiTenant->id, session('active_tenant_id'));
    }

    public function test_logged_in_store_admin_opening_platform_landing_page_renders_platform_welcome_not_storefront(): void
    {
        $admin = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->first();
        $this->assertNotNull($admin);

        // When logged-in store admin visits platform landing page directly without any platform headers
        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('ShopManager');
        $response->assertSee('Dashboard');
        $response->assertDontSee("Jessi's ClassiQ Store &mdash; Luxury Boutique", false);
    }

    public function test_storefront_route_renders_tenant_storefront(): void
    {
        $admin = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->first();

        $response = $this->actingAs($admin)
            ->withSession(['active_tenant_id' => $admin->tenant_id])
            ->get('/storefront');

        $response->assertStatus(200);
        $response->assertSee("Jessi's ClassiQ Store", false);
        $response->assertSee('Clothes');
    }

    public function test_super_admin_layout_does_not_contain_tenant_stores_navigation_item(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->first();

        $response = $this->actingAs($superAdmin)->get(route('super-admin.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('<span>Tenant Stores</span>', false);
        $response->assertSee('Platform Users');
    }
}
