<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\PaymentsNotConfiguredException;
use App\Exceptions\PaystackRejectionException;
use App\Http\Controllers\Controller;
use App\Jobs\DisbursePayrollBatch;
use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(protected PaystackService $paystackService) {}

    public function index(Request $request): View
    {
        $period = $request->get('period', now()->format('Y-m'));

        $staffMembers = Staff::with(['user', 'salaryPayments' => function ($q) use ($period) {
            $q->where('period', $period);
        }])->where('is_active', true)->get();

        $totalCommitment = $staffMembers->sum('monthly_salary');

        // Computed metrics for current period
        $paidPayments = SalaryPayment::where('period', $period)
            ->where('status', 'success')
            ->get();

        $totalDisbursed = $paidPayments->sum('amount');
        $paidStaffIds = $paidPayments->pluck('staff_id')->toArray();
        $pendingStaffCount = $staffMembers->whereNotIn('id', $paidStaffIds)->count();

        // Recent salary payments history
        $recentPayments = SalaryPayment::with('staff.user')
            ->latest()
            ->take(15)
            ->get();

        return view('admin.payroll.index', compact(
            'period',
            'staffMembers',
            'totalCommitment',
            'totalDisbursed',
            'pendingStaffCount',
            'recentPayments'
        ));
    }

    public function disburse(Request $request): RedirectResponse
    {
        $request->validate([
            'period' => 'required|date_format:Y-m',
            'staff_id' => 'nullable|exists:staff,id',
            'batch' => 'nullable|boolean',
        ]);

        $period = $request->period;

        try {
            $this->paystackService->getSecretKey();
        } catch (PaymentsNotConfiguredException $e) {
            return redirect()->back()->with('error', 'Online payments / transfers are not configured for this store.');
        }

        if ($request->filled('staff_id')) {
            $staffList = Staff::with('user')->where('id', $request->staff_id)->where('is_active', true)->get();
        } else {
            $staffList = Staff::with('user')->where('is_active', true)->get();
        }

        if ($staffList->isEmpty()) {
            return redirect()->back()->with('error', 'No active staff found for disbursement.');
        }

        $staffIds = $request->filled('staff_id') ? [(int) $request->staff_id] : null;
        $tenantId = app(CurrentTenant::class)->id();

        DisbursePayrollBatch::dispatch($period, $staffIds, $tenantId);

        return redirect()->route('admin.payroll.index', ['period' => $period])
            ->with('success', "Payroll processing initiated for {$period}.");
    }

    public function verifyStatus(SalaryPayment $salaryPayment): RedirectResponse
    {
        $reference = $salaryPayment->paystack_reference;
        if (empty($reference)) {
            return redirect()->back()->with('error', 'Salary payment has no Paystack reference to verify.');
        }

        try {
            $verifyData = $this->paystackService->verifyTransfer($reference);
            $paystackStatus = strtolower($verifyData['data']['status'] ?? '');

            if (($verifyData['status'] ?? false) && ! empty($paystackStatus)) {
                $transferCode = $verifyData['data']['transfer_code'] ?? $salaryPayment->paystack_transfer_code;

                if ($paystackStatus === 'success') {
                    $salaryPayment->update([
                        'status' => 'success',
                        'paystack_transfer_code' => $transferCode,
                        'paid_at' => $salaryPayment->paid_at ?? now(),
                        'error_message' => null,
                    ]);

                    return redirect()->back()->with('success', "Transfer verified as successful with Paystack ({$transferCode}).");
                }

                if (in_array($paystackStatus, ['pending', 'processing', 'otp'], true)) {
                    $salaryPayment->update([
                        'status' => 'pending',
                        'paystack_transfer_code' => $transferCode,
                        'error_message' => null,
                    ]);

                    return redirect()->back()->with('info', "Transfer is currently {$paystackStatus} on Paystack.");
                }

                if ($paystackStatus === 'failed') {
                    $salaryPayment->update([
                        'status' => 'failed',
                        'paystack_transfer_code' => $transferCode,
                        'error_message' => $verifyData['data']['reason'] ?? 'Transfer failed on Paystack.',
                    ]);

                    return redirect()->back()->with('error', 'Paystack reported this transfer as failed: '.($verifyData['data']['reason'] ?? ''));
                }

                if ($paystackStatus === 'reversed') {
                    $salaryPayment->update([
                        'status' => 'reversed',
                        'error_message' => 'Transfer was reversed by Paystack.',
                    ]);

                    return redirect()->back()->with('warning', 'Paystack reported this transfer as reversed.');
                }
            }

            return redirect()->back()->with('warning', 'Transfer reference was not found on Paystack. You may re-initiate the transfer.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to verify transfer with Paystack: '.$e->getMessage());
        }
    }

    public function retry(SalaryPayment $salaryPayment): RedirectResponse
    {
        $staff = $salaryPayment->staff;

        if (! $staff || ! $staff->is_active) {
            return redirect()->back()->with('error', 'Staff member is inactive or not found.');
        }

        $reference = $salaryPayment->paystack_reference;
        if (empty($reference)) {
            $cleanPeriod = str_replace('-', '', $salaryPayment->period);
            $random = bin2hex(random_bytes(6));
            $reference = strtolower("pay-{$staff->tenant_id}-{$staff->id}-{$cleanPeriod}-{$random}");
            $reference = substr(preg_replace('/[^a-z0-9\-_]/', '', $reference), 0, 50);
            if (strlen($reference) < 16) {
                $reference = str_pad($reference, 16, '0');
            }
            $salaryPayment->update(['paystack_reference' => $reference]);
        }

        // 1. First call Paystack GET /transfer/verify/{reference}
        try {
            $verifyData = $this->paystackService->verifyTransfer($reference);
            $paystackStatus = strtolower($verifyData['data']['status'] ?? '');

            // If it exists as success/pending/otp, sync local status and DO NOT send again!
            if (($verifyData['status'] ?? false) && in_array($paystackStatus, ['success', 'pending', 'processing', 'otp'], true)) {
                $transferCode = $verifyData['data']['transfer_code'] ?? $salaryPayment->paystack_transfer_code;
                $localStatus = $paystackStatus === 'success' ? 'success' : 'pending';

                $salaryPayment->update([
                    'status' => $localStatus,
                    'paystack_transfer_code' => $transferCode,
                    'paid_at' => $localStatus === 'success' ? ($salaryPayment->paid_at ?? now()) : null,
                    'error_message' => null,
                ]);

                return redirect()->back()->with('info', "Transfer already exists on Paystack with status '{$paystackStatus}'. Synced status without re-sending.");
            }
        } catch (\Throwable $e) {
            // If verification timed out or connection error, do not blindly initiate another transfer!
            $salaryPayment->update([
                'status' => 'needs_review',
                'error_message' => 'Status check timed out before retry: '.$e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Could not verify existing transfer status with Paystack. Marked as Needs Review: '.$e->getMessage());
        }

        // 2. Only if Paystack confirms the reference does not exist (or failed previously) may it initiate a new transfer, reusing the SAME reference
        try {
            $transferData = $this->paystackService->initiateTransfer(
                $staff->recipient_code,
                (float) $salaryPayment->amount,
                "Jessi ClassiQ Salary Retry - {$salaryPayment->period} - {$staff->user->name}",
                $reference
            );

            $transferCode = $transferData['data']['transfer_code'] ?? null;
            $status = ($transferData['status'] ?? false) ? 'success' : 'failed';

            $salaryPayment->update([
                'paystack_transfer_code' => $transferCode,
                'status' => $status,
                'paid_at' => $status === 'success' ? now() : null,
                'error_message' => $status === 'failed' ? ($transferData['message'] ?? 'Transfer failed') : null,
            ]);

            return redirect()->back()->with('success', "Transfer re-initiated for {$staff->user->name}.");
        } catch (PaymentsNotConfiguredException $e) {
            return redirect()->back()->with('error', 'Online payments / transfers are not configured for this store.');
        } catch (PaystackRejectionException $e) {
            $salaryPayment->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Paystack rejected transfer: '.$e->getMessage());
        } catch (\Throwable $e) {
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'insufficient') || str_contains($msg, 'rejected') || str_contains($msg, 'invalid recipient') || str_contains($msg, '4xx') || str_contains($msg, '400') || str_contains($msg, '422')) {
                $salaryPayment->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            } else {
                $salaryPayment->update([
                    'status' => 'needs_review',
                    'error_message' => $e->getMessage(),
                ]);
            }

            return redirect()->back()->with('error', 'Retry encountered unknown outcome. Marked as Needs Review: '.$e->getMessage());
        }
    }
}
