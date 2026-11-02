<?php

namespace Tests\Feature;

use App\Http\Middleware\ResolveTenant;
use App\Http\Requests\Auth\LoginRequest;
use App\Jobs\DisbursePayrollBatch;
use App\Jobs\ProcessOcrImportBatch;
use App\Models\OcrImportBatch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PhaseTwoPointFiveMoneySafetyTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;

    protected Tenant $tenantB;

    protected User $adminA;

    protected User $adminB;

    protected User $userA1;

    protected User $userA2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $this->tenantA = Tenant::create([
            'name' => 'Atelier Alpha',
            'slug' => 'atelier-alpha',
            'subdomain' => 'alpha',
            'domain' => 'alpha.boutique.test',
            'plan' => 'standard',
            'status' => 'active',
            'paystack_secret_key' => 'sk_live_alpha_secret_key_123',
            'paystack_public_key' => 'pk_live_alpha_public_key_123',
        ]);

        $this->tenantB = Tenant::create([
            'name' => 'Boutique Beta',
            'slug' => 'boutique-beta',
            'subdomain' => 'beta',
            'domain' => 'beta.boutique.test',
            'plan' => 'standard',
            'status' => 'active',
            'paystack_secret_key' => 'sk_live_beta_secret_key_456',
            'paystack_public_key' => 'pk_live_beta_public_key_456',
        ]);

        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);

        $this->adminA = User::create([
            'name' => 'Admin Alpha',
            'email' => 'admin@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->adminA->assignRole('admin');

        $this->userA1 = User::create([
            'name' => 'User A1',
            'email' => 'usera1@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $this->userA2 = User::create([
            'name' => 'User A2',
            'email' => 'usera2@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);

        $this->adminB = User::create([
            'name' => 'Admin Beta',
            'email' => 'admin@beta.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->adminB->assignRole('admin');
    }

    public function test_payroll_batch_disbursement_runs_exactly_once_per_period_preventing_duplicate_transfers(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $staffUser = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $staff = Staff::create([
            'user_id' => $staffUser->id,
            'position' => 'Stylist',
            'bank_name' => 'Access Bank',
            'bank_account_number' => '0123456789',
            'bank_code' => '044',
            'recipient_code' => 'RCP_test_unique_staff_1',
            'monthly_salary' => 150000.00,
            'is_active' => true,
        ]);

        $paystackMock = Mockery::mock(PaystackService::class);
        $paystackMock->shouldReceive('initiateTransfer')
            ->once()
            ->andReturn([
                'status' => true,
                'message' => 'Transfer queued',
                'data' => [
                    'transfer_code' => 'TRF_TEST_12345',
                    'status' => 'success',
                ],
            ]);

        $period = '2026-11';

        // First run initiates transfer
        $job = new DisbursePayrollBatch($period, [$staff->id], $this->tenantA->id);
        $job->handle($paystackMock);

        $payment = SalaryPayment::where('staff_id', $staff->id)
            ->where('period', $period)
            ->first();

        $this->assertNotNull($payment);
        $this->assertEquals('success', $payment->status);
        $this->assertTrue(
            str_starts_with($payment->paystack_reference, "pay-{$this->tenantA->id}-{$staff->id}-202611-") ||
            str_starts_with($payment->paystack_reference, "PAYROLL_{$this->tenantA->id}_{$staff->id}_202611_")
        );
        $this->assertEquals('TRF_TEST_12345', $payment->paystack_transfer_code);

        // Second run must skip because status is already success (initiateTransfer will not be called again)
        $jobSecond = new DisbursePayrollBatch($period, [$staff->id], $this->tenantA->id);
        $jobSecond->handle($paystackMock);

        $this->assertEquals(1, SalaryPayment::where('staff_id', $staff->id)->where('period', $period)->count());
    }

    public function test_payroll_batch_marks_salary_payment_as_failed_on_error_for_manual_retry(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $staffUser = User::create([
            'name' => 'Failing Staff',
            'email' => 'failing@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $staff = Staff::create([
            'user_id' => $staffUser->id,
            'position' => 'Cashier',
            'bank_name' => 'Zenith Bank',
            'bank_account_number' => '1234567890',
            'bank_code' => '057',
            'recipient_code' => 'RCP_test_fail_staff',
            'monthly_salary' => 120000.00,
            'is_active' => true,
        ]);

        $paystackMock = Mockery::mock(PaystackService::class);
        $paystackMock->shouldReceive('initiateTransfer')
            ->once()
            ->andThrow(new \Exception('Insufficient Paystack balance'));

        $period = '2026-12';

        $job = new DisbursePayrollBatch($period, [$staff->id], $this->tenantA->id);
        $job->handle($paystackMock);

        $payment = SalaryPayment::where('staff_id', $staff->id)
            ->where('period', $period)
            ->first();

        $this->assertNotNull($payment);
        $this->assertEquals('failed', $payment->status);
        $this->assertStringContainsString('Insufficient Paystack balance', $payment->error_message);

        // Test the failed() callback handler when job fails or times out
        $timeoutJob = new DisbursePayrollBatch('2027-01', [$staff->id], $this->tenantA->id);
        SalaryPayment::create([
            'staff_id' => $staff->id,
            'period' => '2027-01',
            'amount' => 120000.00,
            'status' => 'pending',
            'paystack_reference' => 'PAYROLL_TIMEOUT_TEST',
        ]);

        $timeoutJob->failed(new \Exception('Queue worker timeout'));

        $timeoutPayment = SalaryPayment::where('staff_id', $staff->id)->where('period', '2027-01')->first();
        $this->assertTrue(in_array($timeoutPayment->status, ['failed', 'needs_review']));
        $this->assertStringContainsString('Queue worker timeout', $timeoutPayment->error_message);
    }

    public function test_payment_callback_does_not_switch_tenant_and_redirects_cross_tenant_reference_to_owning_host(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $order = Order::create([
            'user_id' => $this->userA1->id,
            'order_number' => 'ORD-ALPHA-TEST-1',
            'status' => 'pending',
            'subtotal' => 25000,
            'shipping_fee' => 2000,
            'tax' => 0,
            'total_amount' => 27000,
            'shipping_name' => 'User A1',
            'shipping_email' => 'usera1@alpha.test',
            'shipping_phone' => '08012345678',
            'shipping_address' => '123 Alpha St',
            'shipping_city' => 'Lagos',
            'payment_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'paystack_reference' => 'PAY_ALPHA_CROSS_REF_123',
            'status' => 'pending',
            'amount' => 27000,
            'currency' => 'NGN',
            'customer_email' => 'usera1@alpha.test',
        ]);

        // Attempt callback on Tenant B's host
        $response = $this->withServerVariables(['HTTP_HOST' => 'beta.boutique.test'])
            ->withHeader('X-Tenant', 'beta')
            ->get('/checkout/callback?reference=PAY_ALPHA_CROSS_REF_123');

        // Should redirect to Tenant A's host
        $response->assertRedirect('http://alpha.boutique.test/checkout/callback?reference=PAY_ALPHA_CROSS_REF_123');

        // Verify Tenant B remained current during the execution
        $this->assertNotEquals($this->tenantA->id, app(CurrentTenant::class)->id());

        // Completely unknown reference returns 404
        $unknownResponse = $this->withServerVariables(['HTTP_HOST' => 'beta.boutique.test'])
            ->withHeader('X-Tenant', 'beta')
            ->get('/checkout/callback?reference=PAY_DOES_NOT_EXIST_ANYWHERE');
        $unknownResponse->assertStatus(404);
    }

    public function test_payment_callback_requires_authenticated_user_ownership(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $order = Order::create([
            'user_id' => $this->userA1->id,
            'order_number' => 'ORD-ALPHA-TEST-OWN',
            'status' => 'pending',
            'subtotal' => 15000,
            'shipping_fee' => 1500,
            'tax' => 0,
            'total_amount' => 16500,
            'shipping_name' => 'User A1',
            'shipping_email' => 'usera1@alpha.test',
            'shipping_phone' => '08012345678',
            'shipping_address' => '123 Alpha St',
            'shipping_city' => 'Lagos',
            'payment_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'paystack_reference' => 'PAY_ALPHA_USER_OWN_REF',
            'status' => 'pending',
            'amount' => 16500,
            'currency' => 'NGN',
            'customer_email' => 'usera1@alpha.test',
        ]);

        // User A2 attempts to hit callback for User A1's order
        $response = $this->actingAs($this->userA2)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->get('/checkout/callback?reference=PAY_ALPHA_USER_OWN_REF');

        $response->assertStatus(403);
    }

    public function test_wallet_callback_requires_authenticated_user_ownership(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $transaction = WalletTransaction::create([
            'user_id' => $this->userA1->id,
            'type' => 'credit',
            'amount' => 10000,
            'balance_after' => 10000,
            'description' => 'Wallet funding pending',
            'reference' => 'WT_ALPHA_OWN_REF_123',
            'status' => 'pending',
        ]);

        // User A2 attempts to hit wallet callback for User A1's transaction
        $response = $this->actingAs($this->userA2)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->get('/wallet/callback?reference=WT_ALPHA_OWN_REF_123');

        $response->assertStatus(403);
    }

    public function test_global_uniqueness_prevents_duplicate_paystack_reference_across_tenants(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);
        $orderA = Order::create([
            'user_id' => $this->userA1->id,
            'order_number' => 'ORD-DUP-A',
            'status' => 'pending',
            'subtotal' => 10000,
            'shipping_fee' => 1000,
            'tax' => 0,
            'total_amount' => 11000,
            'shipping_name' => 'User A1',
            'shipping_email' => 'usera1@alpha.test',
            'shipping_phone' => '08012345678',
            'shipping_address' => '123 Alpha St',
            'shipping_city' => 'Lagos',
            'payment_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        Payment::create([
            'order_id' => $orderA->id,
            'paystack_reference' => 'GLOBAL_PAY_REF_DUPLICATE_CHECK',
            'status' => 'pending',
            'amount' => 11000,
            'currency' => 'NGN',
            'customer_email' => 'usera1@alpha.test',
        ]);

        // Now attempt creating same reference in Tenant B
        app(CurrentTenant::class)->set($this->tenantB);
        $orderB = Order::create([
            'user_id' => $this->adminB->id,
            'order_number' => 'ORD-DUP-B',
            'status' => 'pending',
            'subtotal' => 10000,
            'shipping_fee' => 1000,
            'tax' => 0,
            'total_amount' => 11000,
            'shipping_name' => 'Admin B',
            'shipping_email' => 'admin@beta.test',
            'shipping_phone' => '08012345678',
            'shipping_address' => '123 Beta St',
            'shipping_city' => 'Lagos',
            'payment_status' => 'pending',
            'payment_method' => 'paystack',
        ]);

        $this->expectException(QueryException::class);
        Payment::create([
            'order_id' => $orderB->id,
            'paystack_reference' => 'GLOBAL_PAY_REF_DUPLICATE_CHECK',
            'status' => 'pending',
            'amount' => 11000,
            'currency' => 'NGN',
            'customer_email' => 'admin@beta.test',
        ]);
    }

    public function test_global_uniqueness_prevents_duplicate_wallet_transaction_reference_across_tenants(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);
        WalletTransaction::create([
            'user_id' => $this->userA1->id,
            'type' => 'credit',
            'amount' => 5000,
            'balance_after' => 5000,
            'description' => 'Test wallet ref',
            'reference' => 'GLOBAL_WALLET_REF_DUPLICATE_CHECK',
            'status' => 'pending',
        ]);

        // Attempt creating same reference in Tenant B
        app(CurrentTenant::class)->set($this->tenantB);

        $this->expectException(QueryException::class);
        WalletTransaction::create([
            'user_id' => $this->adminB->id,
            'type' => 'credit',
            'amount' => 5000,
            'balance_after' => 5000,
            'description' => 'Test wallet ref B',
            'reference' => 'GLOBAL_WALLET_REF_DUPLICATE_CHECK',
            'status' => 'pending',
        ]);
    }

    public function test_ocr_batch_lifecycle_and_error_handling(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        // 1. Batch created with pending/processing
        $batch = OcrImportBatch::create([
            'original_filename' => 'ledger_corrupt.jpg',
            'image_path' => "tenants/{$this->tenantA->id}/ledger_uploads/nonexistent_corrupt.jpg",
            'status' => 'pending',
        ]);

        $job = new ProcessOcrImportBatch($batch->id);
        $job->failed(new \Exception('Unreadable image resolution or corrupted file'));

        $batch->refresh();
        $this->assertEquals('failed', $batch->status);
        $this->assertStringContainsString('Unreadable image resolution', $batch->error_message);

        // 2. Review view renders failed status banner
        $response = $this->actingAs($this->adminA)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->get("/admin/ocr-imports/{$batch->id}/review");

        $response->assertStatus(200);
        $response->assertSee('OCR Processing Failed');
        $response->assertSee('Unreadable image resolution');

        // 3. Review view renders processing banner when batch is processing
        $batch->update(['status' => 'processing', 'error_message' => null]);
        $processingResponse = $this->actingAs($this->adminA)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->get("/admin/ocr-imports/{$batch->id}/review");

        $processingResponse->assertStatus(200);
        $processingResponse->assertSee('Processing Paper Ledger...');

    }

    public function test_private_ledger_storage_authorization_and_fallback(): void
    {
        Storage::fake('private');
        Storage::fake('public');

        app(CurrentTenant::class)->set($this->tenantA);

        $filePath = "tenants/{$this->tenantA->id}/ledger_uploads/private_ledger.jpg";
        Storage::disk('private')->put($filePath, 'fake image content for private disk');

        $batch = OcrImportBatch::create([
            'original_filename' => 'private_ledger.jpg',
            'image_path' => $filePath,
            'status' => 'ready',
        ]);

        // 1. Owning admin can download/view image via secure route
        $response = $this->actingAs($this->adminA)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->get("/admin/ocr-imports/{$batch->id}/image");

        $response->assertStatus(200);

        // 2. Cross-tenant admin cannot access batch (404 due to tenant global scope or 403)
        $crossResponse = $this->actingAs($this->adminB)
            ->withServerVariables(['HTTP_HOST' => 'beta.boutique.test'])
            ->withHeader('X-Tenant', 'beta')
            ->get("/admin/ocr-imports/{$batch->id}/image");

        $this->assertTrue(in_array($crossResponse->status(), [403, 404]));

        // 3. Fallback to public disk for legacy batches
        app(CurrentTenant::class)->set($this->tenantA);
        $legacyPath = 'ocr_sheets/legacy_sheet.jpg';
        Storage::disk('public')->put($legacyPath, 'fake legacy public image content');

        $legacyBatch = OcrImportBatch::create([
            'original_filename' => 'legacy_sheet.jpg',
            'image_path' => $legacyPath,
            'status' => 'ready',
        ]);

        $legacyResponse = $this->actingAs($this->adminA)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->get("/admin/ocr-imports/{$legacyBatch->id}/image");

        $legacyResponse->assertStatus(200);
    }

    public function test_spatie_role_checks_work_across_multiple_tenants_without_clearing_cache_on_switch(): void
    {
        // In Tenant A, assign staff role to userA1
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);
        $this->userA1->assignRole('staff');

        $this->assertTrue($this->userA1->hasRole('staff'));

        // Switch to Tenant B
        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);
        $this->userA1->unsetRelation('roles');

        // userA1 should NOT have staff role in Tenant B
        $this->assertFalse($this->userA1->hasRole('staff'));

        // Switch back to Tenant A
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);
        $this->userA1->unsetRelation('roles');

        // userA1 should have staff role in Tenant A
        $this->assertTrue($this->userA1->hasRole('staff'));
    }

    public function test_ip_host_resolution_in_production_returns_404_and_in_testing_falls_back(): void
    {
        $middleware = app(ResolveTenant::class);

        // In testing environment: 127.0.0.1 falls back to default tenant
        $requestTesting = Request::create('http://127.0.0.1/');
        $responseTesting = $middleware->handle($requestTesting, function ($req) {
            return new Response('OK');
        });
        $this->assertEquals(200, $responseTesting->getStatusCode());

        // In production environment: raw IP host should abort 404
        app()->detectEnvironment(fn () => 'production');

        $this->expectException(HttpException::class);
        $requestProd = Request::create('http://192.168.1.50/');
        $middleware->handle($requestProd, function ($req) {
            return new Response('OK');
        });
    }

    public function test_paystack_webhook_route_has_rate_limit_middleware(): void
    {
        $routes = RouteFacade::getRoutes()->getByName('paystack.webhook');
        $this->assertNotNull($routes);

        $middleware = $routes->middleware();
        $this->assertTrue(in_array('throttle:600,1', $middleware) || in_array('throttle:120,1', $middleware));
    }

    public function test_login_throttle_key_includes_tenant_id(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);
        $requestA = new LoginRequest;
        $requestA->merge(['email' => 'Customer@Alpha.Test']);
        $requestA->server->set('REMOTE_ADDR', '10.0.0.1');

        $throttleKeyA = $requestA->throttleKey();
        $this->assertStringStartsWith("{$this->tenantA->id}|", $throttleKeyA);
        $this->assertStringContainsString('customer@alpha.test', $throttleKeyA);

        app(CurrentTenant::class)->set($this->tenantB);
        $requestB = new LoginRequest;
        $requestB->merge(['email' => 'Customer@Alpha.Test']);
        $requestB->server->set('REMOTE_ADDR', '10.0.0.1');

        $throttleKeyB = $requestB->throttleKey();
        $this->assertStringStartsWith("{$this->tenantB->id}|", $throttleKeyB);
        $this->assertNotEquals($throttleKeyA, $throttleKeyB);
    }
}
