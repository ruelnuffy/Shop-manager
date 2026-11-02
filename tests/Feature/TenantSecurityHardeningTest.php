<?php

namespace Tests\Feature;

use App\Exceptions\PaymentsNotConfiguredException;
use App\Exceptions\TenantNotResolvedException;
use App\Http\Middleware\ResolveTenant;
use App\Jobs\Concerns\TenantAware;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class TenantSecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;

    protected Tenant $tenantB;

    protected User $userA;

    protected User $userB;

    protected Category $categoryA;

    protected Product $productA;

    protected ProductVariant $variantA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        // Tenant A (Custom tenant with its own Paystack keys)
        $this->tenantA = Tenant::create([
            'name' => 'Atelier Alpha',
            'slug' => 'atelier-alpha',
            'subdomain' => 'alpha',
            'domain' => 'alpha.boutique.test',
            'plan' => 'standard',
            'status' => 'active',
            'paystack_secret_key' => 'sk_live_alpha_secret_key_1234567890',
            'paystack_public_key' => 'pk_live_alpha_public_key_1234567890',
        ]);

        // Tenant B (Custom tenant with its own separate Paystack keys)
        $this->tenantB = Tenant::create([
            'name' => 'Boutique Beta',
            'slug' => 'boutique-beta',
            'subdomain' => 'beta',
            'domain' => 'beta.boutique.test',
            'plan' => 'standard',
            'status' => 'active',
            'paystack_secret_key' => 'sk_live_beta_secret_key_0987654321',
            'paystack_public_key' => 'pk_live_beta_public_key_0987654321',
        ]);

        // Create Users
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);

        $this->userA = User::create([
            'name' => 'Alice Alpha',
            'email' => 'alice@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->userA->assignRole('admin');

        $this->categoryA = Category::create([
            'name' => 'Silk Dresses',
            'slug' => 'silk-dresses',
            'is_active' => true,
        ]);

        $this->productA = Product::create([
            'category_id' => $this->categoryA->id,
            'name' => 'Alpha Silk Dress',
            'slug' => 'alpha-silk-dress',
            'base_price' => 50000,
            'status' => 'active',
        ]);

        $this->variantA = ProductVariant::create([
            'product_id' => $this->productA->id,
            'size' => 'M',
            'color' => 'Navy',
            'sku' => 'ALPHA-NAVY-M',
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);

        $this->userB = User::create([
            'name' => 'Bob Beta',
            'email' => 'bob@beta.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->userB->assignRole('admin');

        app(CurrentTenant::class)->forget();
    }

    public function test_header_spoofing_ignored_in_production_environment(): void
    {
        config(['tenancy.base_domain' => 'jessiclassiq.com']);
        $this->app->detectEnvironment(fn () => 'production');

        $middleware = app(ResolveTenant::class);

        // Host is alpha.jessiclassiq.com, but attacker sends X-Tenant: boutique-beta
        $request = Request::create('http://alpha.jessiclassiq.com/products', 'GET');
        $request->headers->set('X-Tenant', 'boutique-beta');

        $resolvedTenantId = null;
        $middleware->handle($request, function () use (&$resolvedTenantId) {
            $resolvedTenantId = app(CurrentTenant::class)->id();

            return response('OK');
        });

        // The middleware strictly resolved Tenant A from host, ignoring X-Tenant spoof
        $this->assertEquals($this->tenantA->id, $resolvedTenantId);
    }

    public function test_unknown_host_returns_404_in_production_environment(): void
    {
        config(['tenancy.base_domain' => 'jessiclassiq.com']);
        $this->app->detectEnvironment(fn () => 'production');

        $middleware = app(ResolveTenant::class);

        $request = Request::create('http://unknown-store.jessiclassiq.com/products', 'GET');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Store not found.');

        $middleware->handle($request, function () {
            return response('OK');
        });
    }

    public function test_inactive_tenant_returns_503(): void
    {
        $this->tenantA->update(['status' => 'suspended']);

        $middleware = app(ResolveTenant::class);
        $request = Request::create('http://alpha.boutique.test/products', 'GET');

        try {
            $middleware->handle($request, function () {
                return response('OK');
            });
            $this->fail('Expected 503 HttpException was not thrown for suspended tenant.');
        } catch (HttpException $e) {
            $this->assertEquals(503, $e->getStatusCode());
            $this->assertStringContainsString('This store is currently inactive.', $e->getMessage());
        }
    }

    public function test_non_default_tenant_without_paystack_keys_cannot_fall_back_to_platform_keys(): void
    {
        // Non-default tenant with no keys configured
        $this->tenantA->update([
            'paystack_secret_key' => null,
            'paystack_public_key' => null,
        ]);

        app(CurrentTenant::class)->set($this->tenantA);

        $paystack = app(PaystackService::class);

        $this->expectException(PaymentsNotConfiguredException::class);
        $paystack->getSecretKey();
    }

    public function test_checkout_handles_missing_payment_keys_gracefully(): void
    {
        $this->tenantA->update([
            'paystack_secret_key' => null,
            'paystack_public_key' => null,
        ]);

        app(CurrentTenant::class)->set($this->tenantA);

        $response = $this->actingAs($this->userA)
            ->withSession([
                'cart' => [
                    $this->variantA->id => [
                        'variant_id' => $this->variantA->id,
                        'product_id' => $this->productA->id,
                        'product_name' => $this->productA->name,
                        'size' => $this->variantA->size,
                        'color' => $this->variantA->color,
                        'price' => 50000,
                        'quantity' => 1,
                    ],
                ],
            ])
            ->post('/checkout', [
                'shipping_name' => 'Alice Customer',
                'shipping_email' => 'alice@alpha.test',
                'shipping_phone' => '08012345678',
                'shipping_address' => '12 Marina St',
                'shipping_city' => 'Lagos',
                'payment_method' => 'paystack',
            ], ['X-Tenant' => $this->tenantA->slug]);

        // Expect user-facing redirect back with error flash, not 500 exception
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Online payments are currently not configured for this store.');
    }

    public function test_webhook_with_valid_signature_for_tenant_b_key_cannot_confirm_tenant_a_payment(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $order = Order::create([
            'user_id' => $this->userA->id,
            'order_number' => 'ORD-ALPHA-100',
            'status' => 'pending',
            'subtotal' => 50000,
            'shipping_fee' => 3500,
            'total_amount' => 53500,
            'shipping_name' => 'Alice Alpha',
            'shipping_email' => 'alice@alpha.test',
            'shipping_phone' => '08011112222',
            'shipping_address' => '12 Alpha Way',
            'shipping_city' => 'Lagos',
            'payment_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $this->variantA->id,
            'product_name' => $this->productA->name,
            'variant_description' => 'M - Navy',
            'unit_price' => 50000,
            'quantity' => 2,
            'subtotal' => 100000,
        ]);

        $reference = 'PAY_ALPHA_REF_999';
        $payment = Payment::create([
            'order_id' => $order->id,
            'paystack_reference' => $reference,
            'status' => 'pending',
            'amount' => 53500,
            'currency' => 'NGN',
            'customer_email' => 'alice@alpha.test',
        ]);

        app(CurrentTenant::class)->forget();

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => $reference,
                'status' => 'success',
                'amount' => 5350000,
                'channel' => 'card',
            ],
        ]);

        // Sign with Tenant B's secret key!
        $tamperedSignature = hash_hmac('sha512', $payload, $this->tenantB->paystack_secret_key);

        $response = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            ['HTTP_X_PAYSTACK_SIGNATURE' => $tamperedSignature, 'CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Invalid signature for tenant.']);

        $payment->refresh();
        $this->assertEquals('pending', $payment->status);
    }

    public function test_webhook_with_valid_tenant_a_signature_confirms_payment_and_is_idempotent(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $order = Order::create([
            'user_id' => $this->userA->id,
            'order_number' => 'ORD-ALPHA-200',
            'status' => 'pending',
            'subtotal' => 50000,
            'shipping_fee' => 3500,
            'total_amount' => 53500,
            'shipping_name' => 'Alice Alpha',
            'shipping_email' => 'alice@alpha.test',
            'shipping_phone' => '08011112222',
            'shipping_address' => '12 Alpha Way',
            'shipping_city' => 'Lagos',
            'payment_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $this->variantA->id,
            'product_name' => $this->productA->name,
            'variant_description' => 'M - Navy',
            'unit_price' => 50000,
            'quantity' => 2,
            'subtotal' => 100000,
        ]);

        $reference = 'PAY_ALPHA_REF_888';
        $payment = Payment::create([
            'order_id' => $order->id,
            'paystack_reference' => $reference,
            'status' => 'pending',
            'amount' => 53500,
            'currency' => 'NGN',
            'customer_email' => 'alice@alpha.test',
        ]);

        app(CurrentTenant::class)->forget();

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => $reference,
                'status' => 'success',
                'amount' => 5350000,
                'channel' => 'card',
            ],
        ]);

        // Sign with Tenant A's authentic secret key
        $signature = hash_hmac('sha512', $payload, $this->tenantA->paystack_secret_key);

        $initialStock = $this->variantA->fresh()->stock_quantity; // 10

        // 1. First webhook execution
        $response = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            ['HTTP_X_PAYSTACK_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $payment->refresh();
        $order->refresh();
        $this->assertEquals('successful', $payment->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals($initialStock - 2, $this->variantA->fresh()->stock_quantity); // 8

        // 2. Second webhook execution with the exact same payload (Repeated webhook)
        $secondResponse = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            ['HTTP_X_PAYSTACK_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $secondResponse->assertStatus(200);
        $secondResponse->assertJson(['status' => 'success', 'message' => 'Payment already processed.']);

        // Stock MUST NOT be decremented twice!
        $this->assertEquals($initialStock - 2, $this->variantA->fresh()->stock_quantity);
    }

    public function test_queued_job_runs_under_correct_tenant_and_restores_permissions(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $testJob = new TestTenantSecurityJob($this->tenantB->id);

        dispatch($testJob);

        $this->assertEquals($this->tenantB->id, Context::get('job_executed_tenant_id'));
        $this->assertEquals($this->tenantB->id, Context::get('job_executed_team_id'));

        // Confirm parent process context restored to Tenant A
        $this->assertEquals($this->tenantA->id, app(CurrentTenant::class)->id());
    }

    public function test_file_paths_are_tenant_prefixed(): void
    {
        Storage::fake('public');
        app(CurrentTenant::class)->set($this->tenantA);

        $coverFile = UploadedFile::fake()->image('alpha_gown.jpg');

        $this->actingAs($this->userA)->post('/admin/products', [
            'name' => 'Alpha Elegance Gown',
            'category_id' => $this->categoryA->id,
            'base_price' => 65000,
            'featured_image_file' => $coverFile,
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'S',
                    'color' => 'Crimson',
                    'sku' => 'ALPHA-ELEG-S',
                    'stock' => 5,
                ],
            ],
        ], ['X-Tenant' => $this->tenantA->slug]);

        $product = Product::withoutGlobalScopes()->where('name', 'Alpha Elegance Gown')->first();
        $this->assertNotNull($product);
        $this->assertStringStartsWith('/storage/tenants/'.$this->tenantA->id.'/products/', $product->featured_image);

        // OCR Import Sheet test
        $ocrFile = UploadedFile::fake()->image('alpha_ledger.jpg');
        $this->actingAs($this->userA)->post('/admin/ocr-imports', [
            'ledger_image' => $ocrFile,
        ], ['X-Tenant' => $this->tenantA->slug]);

        $batch = DB::table('ocr_import_batches')->where('tenant_id', $this->tenantA->id)->latest('id')->first();
        $this->assertNotNull($batch);
        $this->assertStringStartsWith('tenants/'.$this->tenantA->id.'/ledger_uploads/', $batch->image_path);
    }

    public function test_no_tenant_query_throws_in_http_request_with_run_as_and_without_global_scopes_escape_hatches(): void
    {
        CurrentTenant::setIsHttpRequest(true);
        app(CurrentTenant::class)->forget();

        try {
            // Unscoped query in HTTP context must throw TenantNotResolvedException
            $this->expectException(TenantNotResolvedException::class);
            Product::count();
        } finally {
            CurrentTenant::setIsHttpRequest(false);
        }
    }

    public function test_run_as_and_without_global_scopes_escape_hatches_work_when_no_tenant_resolved(): void
    {
        CurrentTenant::setIsHttpRequest(true);
        app(CurrentTenant::class)->forget();

        try {
            // Escape hatch 1: withoutGlobalScopes works without throwing
            $count = Product::withoutGlobalScopes()->count();
            $this->assertGreaterThanOrEqual(1, $count);

            // Escape hatch 2: CurrentTenant::runAs works and scopes correctly
            $scopedCount = CurrentTenant::runAs($this->tenantA, function () {
                return Product::count();
            });
            $this->assertEquals(1, $scopedCount);
        } finally {
            CurrentTenant::setIsHttpRequest(false);
        }
    }

    public function test_session_and_user_lookup_isolated_across_tenant_subdomains(): void
    {
        // Session cookie domain is null (not shared across subdomains)
        $this->assertNull(config('session.domain'));

        // Tenant-scoped user lookup isolation: User A belongs to Tenant A
        app(CurrentTenant::class)->set($this->tenantA);
        $foundUserOnA = User::find($this->userA->id);
        $this->assertNotNull($foundUserOnA);
        $this->assertEquals($this->userA->id, $foundUserOnA->id);

        // Under Tenant B, lookup of User A returns null due to tenant scope
        app(CurrentTenant::class)->set($this->tenantB);
        $foundUserOnB = User::find($this->userA->id);
        $this->assertNull($foundUserOnB);
    }
}

class TestTenantSecurityJob implements ShouldQueue
{
    use TenantAware;

    public function __construct(int $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function handle(): void
    {
        Context::add('job_executed_tenant_id', app(CurrentTenant::class)->id());
        Context::add('job_executed_team_id', getPermissionsTeamId());
    }
}
