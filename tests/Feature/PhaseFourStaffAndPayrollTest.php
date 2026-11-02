<?php

namespace Tests\Feature;

use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFourStaffAndPayrollTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staffUser;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@jessiclassiq.com')->first();
        $this->staffUser = User::where('email', 'staff@jessiclassiq.com')->first();
        $this->customer = User::where('email', 'customer@jessiclassiq.com')->first();
    }

    public function test_staff_and_payroll_routes_forbidden_for_non_admins(): void
    {
        // 1. Guest redirected
        $this->get('/admin/staff')->assertRedirect('/login');
        $this->get('/admin/payroll')->assertRedirect('/login');

        // 2. Customer gets 403
        $this->actingAs($this->customer)->get('/admin/staff')->assertStatus(403);
        $this->actingAs($this->customer)->get('/admin/payroll')->assertStatus(403);

        // 3. Regular staff gets 403 (Admin-only privilege)
        $this->actingAs($this->staffUser)->get('/admin/staff')->assertStatus(403);
        $this->actingAs($this->staffUser)->get('/admin/payroll')->assertStatus(403);

        // 4. Admin gets 200
        $this->actingAs($this->admin)->get('/admin/staff')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/payroll')->assertStatus(200);
    }

    public function test_admin_can_view_staff_directory_and_onboard_new_staff(): void
    {
        $directoryView = $this->actingAs($this->admin)->get('/admin/staff');
        $directoryView->assertStatus(200);
        $directoryView->assertSee('Staff &amp; Team Directory', false);
        $directoryView->assertSee('Grace Staff');
        $directoryView->assertSee('Store Sales Lead');

        // Onboard new staff
        $response = $this->actingAs($this->admin)->post('/admin/staff', [
            'name' => 'Tomiwa Balogun',
            'email' => 'tomiwa@jessiclassiq.com',
            'phone' => '+2348099881122',
            'position' => 'Senior Stylist & Atelier Consultant',
            'bank_code' => '058',
            'bank_name' => 'Guaranty Trust Bank (GTB)',
            'bank_account_number' => '0123987654',
            'monthly_salary' => 220000.00,
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/staff');

        // Verify user created
        $user = User::where('email', 'tomiwa@jessiclassiq.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Tomiwa Balogun', $user->name);
        $this->assertTrue($user->hasRole('staff'));

        // Verify staff profile created with Paystack recipient code
        $staff = Staff::where('user_id', $user->id)->first();
        $this->assertNotNull($staff);
        $this->assertEquals('Senior Stylist & Atelier Consultant', $staff->position);
        $this->assertEquals(220000.00, $staff->monthly_salary);
        $this->assertNotEmpty($staff->recipient_code);
        $this->assertStringStartsWith('RCP_', $staff->recipient_code);
    }

    public function test_admin_can_edit_and_deactivate_staff(): void
    {
        $staff = Staff::first();
        $this->assertNotNull($staff);

        // 1. Edit staff details
        $editResponse = $this->actingAs($this->admin)->put('/admin/staff/'.$staff->id, [
            'name' => 'Grace Staff Senior',
            'phone' => '+2348022339900',
            'position' => 'Lead Boutique Manager',
            'bank_code' => '044',
            'bank_name' => 'Access Bank',
            'bank_account_number' => '0123456789',
            'monthly_salary' => 195000.00,
            'is_active' => '1',
        ]);

        $editResponse->assertRedirect('/admin/staff');

        $staff->refresh();
        $this->assertEquals('Lead Boutique Manager', $staff->position);
        $this->assertEquals(195000.00, $staff->monthly_salary);
        $this->assertEquals('Grace Staff Senior', $staff->user->name);

        // 2. Deactivate staff
        $deleteResponse = $this->actingAs($this->admin)->delete('/admin/staff/'.$staff->id);
        $deleteResponse->assertRedirect('/admin/staff');

        $staff->refresh();
        $this->assertFalse($staff->is_active);
    }

    public function test_payroll_overview_calculates_commitments_and_period_metrics(): void
    {
        $period = '2026-09';
        $payrollView = $this->actingAs($this->admin)->get('/admin/payroll?period='.$period);
        $payrollView->assertStatus(200);
        $payrollView->assertSee('Paystack Direct Salary Transfers');
        $payrollView->assertSee('Monthly Commitment');
        $payrollView->assertSee('Grace Staff');
    }

    public function test_admin_can_disburse_individual_staff_salary(): void
    {
        $staff = Staff::where('is_active', true)->first();
        $period = '2026-09';

        $response = $this->actingAs($this->admin)->post('/admin/payroll/disburse', [
            'period' => $period,
            'staff_id' => $staff->id,
        ]);

        $response->assertRedirect('/admin/payroll?period='.$period);

        // Verify SalaryPayment record
        $payment = SalaryPayment::where('staff_id', $staff->id)
            ->where('period', $period)
            ->first();

        $this->assertNotNull($payment);
        $this->assertEquals('success', $payment->status);
        $this->assertEquals($staff->monthly_salary, $payment->amount);
        $this->assertNotNull($payment->paid_at);
        $this->assertNotEmpty($payment->paystack_transfer_code);
    }

    public function test_admin_can_disburse_batch_payroll_and_prevents_duplicate_payment(): void
    {
        $period = '2026-10';

        // 1. Trigger batch disburse
        $batchResponse = $this->actingAs($this->admin)->post('/admin/payroll/disburse', [
            'period' => $period,
            'batch' => '1',
        ]);

        $batchResponse->assertRedirect('/admin/payroll?period='.$period);

        $activeStaffCount = Staff::where('is_active', true)->count();
        $disbursedCount = SalaryPayment::where('period', $period)->where('status', 'success')->count();
        $this->assertEquals($activeStaffCount, $disbursedCount);

        // 2. Trigger batch disburse again for the exact same period
        $repeatResponse = $this->actingAs($this->admin)->post('/admin/payroll/disburse', [
            'period' => $period,
            'batch' => '1',
        ]);

        $repeatResponse->assertRedirect('/admin/payroll?period='.$period);

        // Ensure no duplicate payment records were created
        $finalCount = SalaryPayment::where('period', $period)->count();
        $this->assertEquals($activeStaffCount, $finalCount);
    }

    public function test_admin_can_retry_failed_salary_payment(): void
    {
        $staff = Staff::where('is_active', true)->first();

        // Create a failed record
        $failedPayment = SalaryPayment::create([
            'staff_id' => $staff->id,
            'amount' => $staff->monthly_salary,
            'period' => '2026-08',
            'status' => 'failed',
            'error_message' => 'Insufficient Paystack balance at execution time.',
        ]);

        $this->assertEquals('failed', $failedPayment->status);

        // Retry payment
        $retryResponse = $this->actingAs($this->admin)->post('/admin/payroll/retry/'.$failedPayment->id);
        $retryResponse->assertRedirect();

        $failedPayment->refresh();
        $this->assertEquals('success', $failedPayment->status);
        $this->assertNull($failedPayment->error_message);
        $this->assertNotNull($failedPayment->paid_at);
    }
}
