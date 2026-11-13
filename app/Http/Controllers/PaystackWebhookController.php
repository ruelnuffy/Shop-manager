<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SalaryPayment;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\WalletTransaction;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use App\Services\StoreNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function __construct(
        protected CurrentTenant $currentTenant,
        protected PaystackService $paystackService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('x-paystack-signature');
        if (! $signature) {
            return response()->json(['message' => 'Missing x-paystack-signature header.'], 400);
        }

        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if (! is_array($event) || ! isset($event['event'])) {
            return response()->json(['message' => 'Invalid webhook payload structure.'], 400);
        }

        $reference = $event['data']['reference'] ?? null;
        if (! $reference) {
            return response()->json(['message' => 'Missing payment reference in event data.'], 400);
        }

        // Identify the tenant from the payment, wallet, or salary payment reference using withoutGlobalScopes
        $payment = Payment::withoutGlobalScopes()->where('paystack_reference', $reference)->first();
        $walletTx = null;
        $salaryPayment = null;
        $tenantId = null;

        if ($payment) {
            $tenantId = $payment->tenant_id;
        } else {
            $walletTx = WalletTransaction::withoutGlobalScopes()->where('reference', $reference)->first();
            if ($walletTx) {
                $tenantId = $walletTx->tenant_id;
            } else {
                $salaryPayment = SalaryPayment::withoutGlobalScopes()->where('paystack_reference', $reference)->first();
                if ($salaryPayment) {
                    $tenantId = $salaryPayment->tenant_id;
                }
            }
        }

        if (! $tenantId) {
            Log::warning("Paystack webhook received for unknown reference: {$reference}");

            return response()->json(['status' => 'ignored', 'message' => 'Unknown payment reference.'], 200);
        }

        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            Log::warning("Tenant associated with Paystack reference not found: {$reference}");

            return response()->json(['status' => 'ignored', 'message' => 'Tenant associated with payment reference not found.'], 200);
        }

        // Set CurrentTenant context for this request
        $this->currentTenant->set($tenant);
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant->id);
        }

        // Verify HMAC-SHA512 using THAT tenant's secret key
        $secretKey = $this->paystackService->getSecretKey();
        $computedSignature = hash_hmac('sha512', $payload, $secretKey);

        if (! hash_equals($computedSignature, (string) $signature)) {
            return response()->json(['message' => 'Invalid signature for tenant.'], 401);
        }

        // Handle charge.success idempotently
        if ($event['event'] === 'charge.success') {
            if ($payment) {
                if ($payment->status === 'successful') {
                    return response()->json(['status' => 'success', 'message' => 'Payment already processed.']);
                }

                DB::transaction(function () use ($payment, $event) {
                    $payment->update([
                        'status' => 'successful',
                        'paid_at' => now(),
                        'channel' => $event['data']['channel'] ?? 'card',
                        'raw_response' => $event,
                    ]);

                    $order = $payment->order;
                    if ($order && $order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                        ]);

                        foreach ($order->items as $orderItem) {
                            if ($orderItem->variant) {
                                $variant = $orderItem->variant;
                                $prev = $variant->stock_quantity;
                                $newStock = max(0, $prev - $orderItem->quantity);

                                $variant->update(['stock_quantity' => $newStock]);

                                StockMovement::create([
                                    'product_variant_id' => $variant->id,
                                    'type' => 'out',
                                    'quantity' => $orderItem->quantity,
                                    'previous_stock' => $prev,
                                    'new_stock' => $newStock,
                                    'reason' => 'sale',
                                    'reference_type' => 'Order',
                                    'reference_id' => $order->id,
                                    'created_by' => $order->user_id,
                                ]);
                            }
                        }
                    }
                });

                $order = $payment->order;
                if ($order && $order->payment_status === 'paid' && ($tenant = Tenant::find($order->tenant_id))) {
                    app(StoreNotificationService::class)->notifyStorefrontOrder($tenant, $order);
                }

                return response()->json(['status' => 'success', 'message' => 'Order payment processed.']);
            }

            if ($walletTx) {
                if ($walletTx->status === 'completed') {
                    return response()->json(['status' => 'success', 'message' => 'Wallet funding already completed.']);
                }

                DB::transaction(function () use ($walletTx) {
                    $user = $walletTx->user;
                    if ($user) {
                        $user->increment('wallet_balance', $walletTx->amount);
                        $user->refresh();
                    }

                    $walletTx->update([
                        'status' => 'completed',
                        'balance_after' => $user?->wallet_balance ?? $walletTx->amount,
                    ]);
                });

                return response()->json(['status' => 'success', 'message' => 'Wallet funding processed.']);
            }
        }

        // Handle transfer.success idempotently
        if ($event['event'] === 'transfer.success' && $salaryPayment) {
            if ($salaryPayment->status === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Transfer already marked successful.']);
            }

            $transferCode = $event['data']['transfer_code'] ?? $salaryPayment->paystack_transfer_code;
            $salaryPayment->update([
                'status' => 'success',
                'paystack_transfer_code' => $transferCode,
                'paid_at' => $salaryPayment->paid_at ?? now(),
                'error_message' => null,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Salary payment marked as success.']);
        }

        // Handle transfer.failed idempotently
        if ($event['event'] === 'transfer.failed' && $salaryPayment) {
            if ($salaryPayment->status === 'failed') {
                return response()->json(['status' => 'success', 'message' => 'Transfer already marked failed.']);
            }

            $transferCode = $event['data']['transfer_code'] ?? $salaryPayment->paystack_transfer_code;
            $salaryPayment->update([
                'status' => 'failed',
                'paystack_transfer_code' => $transferCode,
                'error_message' => $event['data']['reason'] ?? $event['data']['message'] ?? 'Transfer failed on Paystack.',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Salary payment marked as failed.']);
        }

        // Handle transfer.reversed idempotently
        if ($event['event'] === 'transfer.reversed' && $salaryPayment) {
            if ($salaryPayment->status === 'reversed') {
                return response()->json(['status' => 'success', 'message' => 'Transfer already marked reversed.']);
            }

            $previousStatus = $salaryPayment->status;
            $salaryPayment->update([
                'status' => 'reversed',
                'error_message' => 'Transfer was reversed by Paystack.',
            ]);

            Log::warning("Salary payment {$salaryPayment->id} (Staff: {$salaryPayment->staff_id}, Period: {$salaryPayment->period}) reversed by Paystack. Previous status: {$previousStatus}");

            return response()->json(['status' => 'success', 'message' => 'Salary payment marked as reversed.']);
        }

        return response()->json(['status' => 'success', 'message' => 'Event acknowledged.']);
    }
}
