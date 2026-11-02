<?php

namespace Tests\Feature;

use App\Exceptions\PaystackTimeoutException;
use App\Jobs\DisbursePayrollBatch;
use App\Models\OcrImportBatch;
use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class PhaseTwoPointSixPayrollSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;

    protected Tenant $tenantB;

    protected User $adminA;

    protected Staff $staffA;

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
            'paystack_secret_key' => 'sk_live_alpha_payroll_key_1234567890',
            'paystack_public_key' => 'pk_live_alpha_payroll_key_1234567890',
        ]);

        $this->tenantB = Tenant::create([
            'name' => 'Boutique Beta',
            'slug' => 'boutique-beta',
            'subdomain' => 'beta',
            'domain' => 'beta.boutique.test',
            'plan' => 'standard',
            'status' => 'active',
            'paystack_secret_key' => 'sk_live_beta_payroll_key_0987654321',
            'paystack_public_key' => 'pk_live_beta_payroll_key_0987654321',
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

        $staffUser = User::create([
            'name' => 'Staff Alpha',
            'email' => 'staff@alpha.test',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);
        $staffUser->assignRole('staff');

        $this->staffA = Staff::create([
            'user_id' => $staffUser->id,
            'position' => 'Senior Tailor',
            'bank_name' => 'Access Bank',
            'bank_account_number' => '0123456789',
            'bank_code' => '044',
            'recipient_code' => 'RCP_alpha_tailor_123',
            'monthly_salary' => 175000.00,
            'is_active' => true,
        ]);
    }

    public function test_transfer_timeout_or_unknown_outcome_marks_salary_payment_as_needs_review_not_failed(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $paystackMock = Mockery::mock(PaystackService::class);
        $paystackMock->shouldReceive('initiateTransfer')
            ->once()
            ->andThrow(new PaystackTimeoutException('Connection timed out after 30 seconds'));

        $period = '2026-11';
        $job = new DisbursePayrollBatch($period, [$this->staffA->id], $this->tenantA->id);
        $job->handle($paystackMock);

        $payment = SalaryPayment::where('staff_id', $this->staffA->id)
            ->where('period', $period)
            ->first();

        $this->assertNotNull($payment);
        $this->assertEquals('needs_review', $payment->status);
        $this->assertNotEquals('failed', $payment->status);
        $this->assertStringContainsString('Connection timed out', $payment->error_message);
    }

    public function test_job_failed_handler_marks_pending_payments_as_needs_review_never_failed(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $period = '2026-12';
        $payment = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => $period,
            'amount' => $this->staffA->monthly_salary,
            'paystack_reference' => 'pay-alpha-timeout-ref-12345',
            'status' => 'pending',
        ]);

        $job = new DisbursePayrollBatch($period, [$this->staffA->id], $this->tenantA->id);
        $job->failed(new \Exception('Worker timeout or process killed'));

        $payment->refresh();
        $this->assertEquals('needs_review', $payment->status);
        $this->assertNotEquals('failed', $payment->status);
        $this->assertStringContainsString('Worker timeout', $payment->error_message);
    }

    public function test_retry_of_needs_review_row_whose_verify_returns_success_syncs_status_and_does_not_call_transfer(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $period = '2027-01';
        $ref = 'pay-1-2-202701-a1b2c3d4e5f6';

        $payment = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => $period,
            'amount' => $this->staffA->monthly_salary,
            'paystack_reference' => $ref,
            'status' => 'needs_review',
            'error_message' => 'Previous network timeout',
        ]);

        $paystackMock = Mockery::mock(PaystackService::class);
        $paystackMock->shouldReceive('getSecretKey')->andReturn('sk_test');
        $paystackMock->shouldReceive('verifyTransfer')
            ->once()
            ->with($ref)
            ->andReturn([
                'status' => true,
                'message' => 'Transfer retrieved',
                'data' => [
                    'status' => 'success',
                    'transfer_code' => 'TRF_VERIFIED_SUCCESS_99',
                    'reference' => $ref,
                ],
            ]);

        // initiateTransfer must NEVER be called because it already exists on Paystack as success!
        $paystackMock->shouldNotReceive('initiateTransfer');

        $this->app->instance(PaystackService::class, $paystackMock);

        $response = $this->actingAs($this->adminA)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->post("/admin/payroll/retry/{$payment->id}");

        $response->assertSessionHas('info');

        $payment->refresh();
        $this->assertEquals('success', $payment->status);
        $this->assertEquals('TRF_VERIFIED_SUCCESS_99', $payment->paystack_transfer_code);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_retry_where_verify_says_not_found_reuses_the_same_reference(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $period = '2027-02';
        $ref = 'pay-1-2-202702-testref12345';

        $payment = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => $period,
            'amount' => $this->staffA->monthly_salary,
            'paystack_reference' => $ref,
            'status' => 'needs_review',
        ]);

        $paystackMock = Mockery::mock(PaystackService::class);
        $paystackMock->shouldReceive('getSecretKey')->andReturn('sk_test');
        $paystackMock->shouldReceive('verifyTransfer')
            ->once()
            ->with($ref)
            ->andReturn([
                'status' => false,
                'message' => 'Transfer reference not found',
            ]);

        // Confirmed absent: should initiate transfer reusing the exact same reference
        $paystackMock->shouldReceive('initiateTransfer')
            ->once()
            ->with(
                $this->staffA->recipient_code,
                (float) $this->staffA->monthly_salary,
                Mockery::any(),
                $ref
            )
            ->andReturn([
                'status' => true,
                'message' => 'Transfer initiated',
                'data' => [
                    'transfer_code' => 'TRF_NEW_DISBURSE_88',
                    'status' => 'success',
                ],
            ]);

        $this->app->instance(PaystackService::class, $paystackMock);

        $response = $this->actingAs($this->adminA)
            ->withServerVariables(['HTTP_HOST' => 'alpha.boutique.test'])
            ->withHeader('X-Tenant', 'alpha')
            ->post("/admin/payroll/retry/{$payment->id}");

        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals('success', $payment->status);
        $this->assertEquals($ref, $payment->paystack_reference);
        $this->assertEquals('TRF_NEW_DISBURSE_88', $payment->paystack_transfer_code);
    }

    public function test_payroll_reference_passes_paystack_format_rule(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $paystackMock = Mockery::mock(PaystackService::class);
        $capturedReference = null;

        $paystackMock->shouldReceive('initiateTransfer')
            ->once()
            ->andReturnUsing(function ($rc, $amt, $reason, $ref) use (&$capturedReference) {
                $capturedReference = $ref;

                return [
                    'status' => true,
                    'data' => ['transfer_code' => 'TRF_FORMAT_OK', 'status' => 'success'],
                ];
            });

        $job = new DisbursePayrollBatch('2027-03', [$this->staffA->id], $this->tenantA->id);
        $job->handle($paystackMock);

        $this->assertNotNull($capturedReference);

        // Format check: lowercase letters, digits, hyphen, underscore; 16-50 chars
        $this->assertMatchesRegularExpression('/^[a-z0-9\-_]{16,50}$/', $capturedReference);
        $this->assertEquals(strtolower($capturedReference), $capturedReference);
        $this->assertGreaterThanOrEqual(16, strlen($capturedReference));
        $this->assertLessThanOrEqual(50, strlen($capturedReference));
    }

    public function test_transfer_success_webhook_updates_correct_tenant_row_and_rejects_wrong_tenant_signature(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $ref = 'pay-alpha-webhook-test-12345';
        $payment = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => '2027-04',
            'amount' => 175000.00,
            'paystack_reference' => $ref,
            'status' => 'pending',
        ]);

        $payload = json_encode([
            'event' => 'transfer.success',
            'data' => [
                'reference' => $ref,
                'transfer_code' => 'TRF_WEBHOOK_OK_123',
                'amount' => 17500000,
                'status' => 'success',
            ],
        ]);

        // 1. Sign with Tenant B's key -> should fail with 401
        $badSignature = hash_hmac('sha512', $payload, $this->tenantB->paystack_secret_key);

        $failResponse = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            [
                'HTTP_X_PAYSTACK_SIGNATURE' => $badSignature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $failResponse->assertStatus(401);
        $payment->refresh();
        $this->assertEquals('pending', $payment->status);

        // 2. Sign with Tenant A's key -> should succeed with 200
        $goodSignature = hash_hmac('sha512', $payload, $this->tenantA->paystack_secret_key);

        $successResponse = $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            [
                'HTTP_X_PAYSTACK_SIGNATURE' => $goodSignature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $successResponse->assertStatus(200);
        $payment->refresh();
        $this->assertEquals('success', $payment->status);
        $this->assertEquals('TRF_WEBHOOK_OK_123', $payment->paystack_transfer_code);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_transfer_failed_and_reversed_webhooks_update_status(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        // 1. Test transfer.failed webhook
        $refFail = 'pay-alpha-fail-ref-99999';
        $paymentFail = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => '2027-05',
            'amount' => 175000.00,
            'paystack_reference' => $refFail,
            'status' => 'pending',
        ]);

        $failPayload = json_encode([
            'event' => 'transfer.failed',
            'data' => [
                'reference' => $refFail,
                'reason' => 'Destination bank account inactive',
            ],
        ]);

        $sigFail = hash_hmac('sha512', $failPayload, $this->tenantA->paystack_secret_key);

        $respFail = $this->call('POST', '/paystack/webhook', [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => $sigFail,
            'CONTENT_TYPE' => 'application/json',
        ], $failPayload);

        $respFail->assertStatus(200);
        $paymentFail->refresh();
        $this->assertEquals('failed', $paymentFail->status);
        $this->assertEquals('Destination bank account inactive', $paymentFail->error_message);

        // 2. Test transfer.reversed webhook after success
        $refRev = 'pay-alpha-rev-ref-88888';
        $paymentRev = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => '2027-06',
            'amount' => 175000.00,
            'paystack_reference' => $refRev,
            'status' => 'success',
            'paid_at' => now(),
        ]);

        $revPayload = json_encode([
            'event' => 'transfer.reversed',
            'data' => [
                'reference' => $refRev,
            ],
        ]);

        $sigRev = hash_hmac('sha512', $revPayload, $this->tenantA->paystack_secret_key);

        $respRev = $this->call('POST', '/paystack/webhook', [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => $sigRev,
            'CONTENT_TYPE' => 'application/json',
        ], $revPayload);

        $respRev->assertStatus(200);
        $paymentRev->refresh();
        $this->assertEquals('reversed', $paymentRev->status);
    }

    public function test_duplicate_transfer_webhook_is_an_idempotent_no_op(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        $ref = 'pay-alpha-idempotent-ref-777';
        $payment = SalaryPayment::create([
            'staff_id' => $this->staffA->id,
            'period' => '2027-07',
            'amount' => 175000.00,
            'paystack_reference' => $ref,
            'status' => 'success',
            'paid_at' => now()->subHour(),
        ]);

        $origPaidAt = $payment->paid_at->toIso8601String();

        $payload = json_encode([
            'event' => 'transfer.success',
            'data' => [
                'reference' => $ref,
                'transfer_code' => 'TRF_EXISTING_123',
                'status' => 'success',
            ],
        ]);

        $sig = hash_hmac('sha512', $payload, $this->tenantA->paystack_secret_key);

        $response = $this->call('POST', '/paystack/webhook', [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => $sig,
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success', 'message' => 'Transfer already marked successful.']);

        $payment->refresh();
        $this->assertEquals('success', $payment->status);
        $this->assertEquals($origPaidAt, $payment->paid_at->toIso8601String());
    }

    public function test_unknown_webhook_reference_returns_200_and_logs_warning_without_processing(): void
    {
        $payload = json_encode([
            'event' => 'transfer.success',
            'data' => [
                'reference' => 'nonexistent-paystack-ref-999',
            ],
        ]);

        $response = $this->call('POST', '/paystack/webhook', [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => 'dummy_signature',
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ignored']);
    }

    public function test_stuck_ocr_batch_is_failed_by_reaper(): void
    {
        app(CurrentTenant::class)->set($this->tenantA);

        // 1. Batch stuck for 15 minutes
        $stuckBatch = OcrImportBatch::create([
            'image_path' => 'tenants/1/ledger_uploads/stuck.jpg',
            'original_filename' => 'stuck.jpg',
            'status' => 'processing',
        ]);
        DB::table('ocr_import_batches')
            ->where('id', $stuckBatch->id)
            ->update(['updated_at' => now()->subMinutes(15)]);

        // 2. Active batch running for only 2 minutes
        $activeBatch = OcrImportBatch::create([
            'image_path' => 'tenants/1/ledger_uploads/active.jpg',
            'original_filename' => 'active.jpg',
            'status' => 'processing',
        ]);
        DB::table('ocr_import_batches')
            ->where('id', $activeBatch->id)
            ->update(['updated_at' => now()->subMinutes(2)]);

        // Run the reaper
        Artisan::call('ocr:reap-stuck-batches');

        $stuckBatch->refresh();
        $this->assertEquals('failed', $stuckBatch->status);
        $this->assertStringContainsString('timed out after 10 minutes', $stuckBatch->error_message);

        $activeBatch->refresh();
        $this->assertEquals('processing', $activeBatch->status);
    }

    public function test_migrate_ledgers_command_moves_files_and_is_idempotent(): void
    {
        Storage::fake('public');
        Storage::fake('private');

        app(CurrentTenant::class)->set($this->tenantA);

        $oldPath = 'ocr_sheets/legacy_sheet_to_move.jpg';
        $dummyContent = 'Legacy daybook image content binary';
        Storage::disk('public')->put($oldPath, $dummyContent);

        $batch = OcrImportBatch::create([
            'image_path' => $oldPath,
            'original_filename' => 'legacy_sheet_to_move.jpg',
            'status' => 'ready',
        ]);

        // 1. Dry run does not move files or update DB
        Artisan::call('ocr:migrate-ledgers-to-private', ['--dry-run' => true]);

        Storage::disk('public')->assertExists($oldPath);
        Storage::disk('private')->assertMissing("tenants/{$this->tenantA->id}/ledger_uploads/legacy_sheet_to_move.jpg");
        $batch->refresh();
        $this->assertEquals($oldPath, $batch->image_path);

        // 2. Real migration moves file and updates DB
        $exitCode = Artisan::call('ocr:migrate-ledgers-to-private');
        $this->assertEquals(0, $exitCode);

        $expectedPrivatePath = "tenants/{$this->tenantA->id}/ledger_uploads/legacy_sheet_to_move.jpg";
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('private')->assertExists($expectedPrivatePath);

        $batch->refresh();
        $this->assertEquals($expectedPrivatePath, $batch->image_path);

        // 3. Re-run is idempotent
        $secondExitCode = Artisan::call('ocr:migrate-ledgers-to-private');
        $this->assertEquals(0, $secondExitCode);
        Storage::disk('private')->assertExists($expectedPrivatePath);
    }
}
