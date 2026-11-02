<?php

namespace App\Jobs;

use App\Exceptions\PaystackRejectionException;
use App\Jobs\Concerns\TenantAware;
use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DisbursePayrollBatch implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, TenantAware;

    public int $tries = 1;

    public bool $failOnTimeout = true;

    /**
     * @param  array<int>|null  $staffIds
     */
    public function __construct(
        public string $period,
        public ?array $staffIds = null,
        ?int $tenantId = null
    ) {
        $this->tenantId = $tenantId ?? app(CurrentTenant::class)->id();
    }

    public function handle(PaystackService $paystackService): void
    {
        $query = Staff::with('user')->where('is_active', true);

        if (! empty($this->staffIds)) {
            $query->whereIn('id', $this->staffIds);
        }

        $staffList = $query->get();

        foreach ($staffList as $staff) {
            $payment = null;
            $reference = null;

            // 1. Re-check inside a DB transaction with lockForUpdate that not already pending, success, or needs_review
            DB::beginTransaction();
            try {
                $payment = SalaryPayment::where('staff_id', $staff->id)
                    ->where('period', $this->period)
                    ->lockForUpdate()
                    ->first();

                if ($payment && in_array($payment->status, ['pending', 'success', 'needs_review'], true)) {
                    DB::rollBack();

                    continue;
                }

                if (empty($staff->recipient_code)) {
                    Log::warning("Staff {$staff->user?->name} does not have a Paystack recipient code.");
                    DB::rollBack();

                    continue;
                }

                // Deterministic unique reference stored on row (generated once)
                // Format: lowercase letters, digits, hyphen, underscore; 16-50 chars.
                $reference = $payment?->paystack_reference;
                if (empty($reference)) {
                    $cleanPeriod = str_replace('-', '', $this->period);
                    $random = bin2hex(random_bytes(6));
                    $reference = strtolower("pay-{$staff->tenant_id}-{$staff->id}-{$cleanPeriod}-{$random}");
                    $reference = substr(preg_replace('/[^a-z0-9\-_]/', '', $reference), 0, 50);
                    if (strlen($reference) < 16) {
                        $reference = str_pad($reference, 16, '0');
                    }
                }

                if (! $payment) {
                    $payment = SalaryPayment::create([
                        'staff_id' => $staff->id,
                        'period' => $this->period,
                        'amount' => $staff->monthly_salary,
                        'paystack_reference' => $reference,
                        'status' => 'pending',
                        'notes' => "Disbursing via Paystack Transfers API for period {$this->period}",
                    ]);
                } else {
                    $payment->update([
                        'paystack_reference' => $reference,
                        'status' => 'pending',
                        'error_message' => null,
                    ]);
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error("Failed to prepare salary payment lock for staff {$staff->id}: ".$e->getMessage());

                continue;
            }

            // 2. Call Paystack Transfers API with the deterministic reference (outside DB transaction)
            try {
                $transferData = $paystackService->initiateTransfer(
                    $staff->recipient_code,
                    (float) $staff->monthly_salary,
                    "Jessi ClassiQ Salary - {$this->period} - {$staff->user?->name}",
                    $reference
                );

                $transferCode = $transferData['data']['transfer_code'] ?? null;
                $status = ($transferData['status'] ?? false) ? 'success' : 'failed';

                $payment->update([
                    'paystack_transfer_code' => $transferCode,
                    'status' => $status,
                    'paid_at' => $status === 'success' ? now() : null,
                    'error_message' => $status === 'failed' ? ($transferData['message'] ?? 'Transfer failed') : null,
                    'notes' => "Disbursed via Paystack Transfers API for period {$this->period}",
                ]);
            } catch (PaystackRejectionException $e) {
                // Definitive 4xx rejection
                Log::error("Definitive Paystack rejection for staff {$staff->user?->name}: ".$e->getMessage());
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            } catch (\Throwable $e) {
                $msg = strtolower($e->getMessage());
                if (str_contains($msg, 'insufficient') || str_contains($msg, 'rejected') || str_contains($msg, 'invalid recipient') || str_contains($msg, '4xx') || str_contains($msg, '400') || str_contains($msg, '422')) {
                    $payment->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                } else {
                    // On timeout, connection error, 5xx, or unknown outcome -> mark needs_review, never failed
                    Log::warning("Unknown outcome / timeout for salary payment {$payment->id} (staff {$staff->id}): ".$e->getMessage());
                    $payment->update([
                        'status' => 'needs_review',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function failed(?\Throwable $exception): void
    {
        // On unhandled failure/timeout, mark pending payments in this batch as needs_review, never failed
        $query = SalaryPayment::where('period', $this->period)->where('status', 'pending');
        if (! empty($this->staffIds)) {
            $query->whereIn('staff_id', $this->staffIds);
        }

        $query->update([
            'status' => 'needs_review',
            'error_message' => $exception ? $exception->getMessage() : 'Payroll job failed or timed out',
        ]);
    }
}
