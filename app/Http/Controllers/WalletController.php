<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentsNotConfiguredException;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function __construct(protected PaystackService $paystackService) {}

    public function fund(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:500|max:1000000',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $amount = (float) $request->amount;
        $callbackUrl = route('wallet.callback');

        try {
            $paystackData = $this->paystackService->initializeTransaction(
                $user->email,
                $amount,
                $callbackUrl,
                [
                    'purpose' => 'wallet_funding',
                    'user_id' => $user->id,
                ]
            );

            $reference = $paystackData['data']['reference'];
            $authUrl = $paystackData['data']['authorization_url'];

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $user->wallet_balance,
                'description' => 'Wallet Top-Up via Paystack',
                'reference' => $reference,
                'status' => 'pending',
            ]);

            return redirect()->away($authUrl);
        } catch (PaymentsNotConfiguredException $e) {
            return redirect()->route('dashboard')->with('error', 'Online payments are currently not configured for this store.');
        } catch (\Exception $e) {
            Log::error('Wallet funding initialization failed: '.$e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Unable to initialize wallet funding: '.$e->getMessage());
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (! $reference) {
            return redirect()->route('dashboard')->with('error', 'No transaction reference found.');
        }

        // 1. Look up wallet transaction within current tenant scope only (DO NOT switch CurrentTenant)
        $transaction = WalletTransaction::where('reference', $reference)->first();

        if (! $transaction) {
            $crossTenantTx = WalletTransaction::withoutGlobalScopes()->where('reference', $reference)->first();

            if ($crossTenantTx && $crossTenantTx->tenant) {
                $targetHost = $this->resolveTenantHost($crossTenantTx->tenant);
                if ($targetHost && $targetHost !== $request->getHost()) {
                    $scheme = $request->isSecure() ? 'https://' : 'http://';

                    return redirect()->away("{$scheme}{$targetHost}/wallet/callback?reference=".urlencode($reference));
                }
            }

            abort(404, 'Wallet transaction reference not recognized.');
        }

        // 2. Require authenticated user to own the wallet transaction
        if (! Auth::check() || Auth::id() !== $transaction->user_id) {
            abort(403, 'Unauthorized access to this wallet transaction.');
        }

        if ($transaction->status === 'completed') {
            return redirect()->route('dashboard')->with('success', 'Wallet balance already updated.');
        }

        try {
            $verifyData = $this->paystackService->verifyTransaction($reference);
            $paystackStatus = $verifyData['data']['status'] ?? 'failed';

            if ($paystackStatus === 'success') {
                DB::transaction(function () use ($transaction) {
                    $user = $transaction->user;
                    $user->increment('wallet_balance', $transaction->amount);
                    $user->refresh();

                    $transaction->update([
                        'status' => 'completed',
                        'balance_after' => $user->wallet_balance,
                    ]);
                });

                return redirect()->route('dashboard')->with('success', 'Boutique wallet funded successfully with ₦'.number_format($transaction->amount, 0).'.');
            }

            $transaction->update(['status' => 'failed']);

            return redirect()->route('dashboard')->with('error', 'Payment verification was not successful.');
        } catch (\Exception $e) {
            Log::error('Wallet callback error: '.$e->getMessage());

            return redirect()->route('dashboard')->with('error', 'Error verifying wallet funding: '.$e->getMessage());
        }
    }

    public function testTopUp(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:1000|max:500000',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $amount = (float) ($request->amount ?? 50000);

        $user->creditWallet(
            $amount,
            'Test instant top-up for testing & demonstration',
            'TEST-TOPUP-'.strtoupper(bin2hex(random_bytes(4)))
        );

        return redirect()->route('dashboard')->with('success', 'Boutique wallet test funded with ₦'.number_format($amount, 0).'. Current Balance: ₦'.number_format($user->wallet_balance, 0).'.');
    }

    protected function resolveTenantHost(Tenant $tenant): ?string
    {
        if (! empty($tenant->domain)) {
            return $tenant->domain;
        }

        $baseDomain = config('tenancy.base_domain', 'localhost');
        if (! empty($tenant->subdomain) && ! empty($baseDomain)) {
            return "{$tenant->subdomain}.{$baseDomain}";
        }

        return null;
    }
}
