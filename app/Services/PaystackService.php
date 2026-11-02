<?php

namespace App\Services;

use App\Exceptions\PaymentsNotConfiguredException;
use App\Exceptions\PaystackRejectionException;
use App\Exceptions\PaystackServerException;
use App\Exceptions\PaystackTimeoutException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.paystack.payment_url', 'https://api.paystack.co'), '/');
    }

    public function getSecretKey(): string
    {
        $tenant = app(CurrentTenant::class)->get();
        if ($tenant && ! empty($tenant->paystack_secret_key)) {
            return $tenant->paystack_secret_key;
        }

        throw new PaymentsNotConfiguredException('Paystack payment keys are not configured for this store.');
    }

    public function getPublicKey(): string
    {
        $tenant = app(CurrentTenant::class)->get();
        if ($tenant && ! empty($tenant->paystack_public_key)) {
            return $tenant->paystack_public_key;
        }

        throw new PaymentsNotConfiguredException('Paystack payment keys are not configured for this store.');
    }

    /**
     * Retrieve the platform-level Paystack secret key used ONLY for subscription billing.
     */
    public function getPlatformSecretKey(): string
    {
        $key = (string) config('services.paystack.secret_key', '');
        if (empty($key)) {
            throw new PaymentsNotConfiguredException('Platform Paystack secret key is not configured.');
        }

        return $key;
    }

    /**
     * Retrieve the platform-level Paystack public key used ONLY for subscription billing.
     */
    public function getPlatformPublicKey(): string
    {
        $key = (string) config('services.paystack.public_key', '');
        if (empty($key)) {
            throw new PaymentsNotConfiguredException('Platform Paystack public key is not configured.');
        }

        return $key;
    }

    public function isTestPlaceholder(): bool
    {
        try {
            $secretKey = $this->getSecretKey();

            return empty($secretKey) || str_contains($secretKey, 'placeholder');
        } catch (PaymentsNotConfiguredException) {
            return false;
        }
    }

    /**
     * Initialize a customer payment transaction.
     */
    public function initializeTransaction(string $email, float $amountInNaira, string $callbackUrl, array $metadata = []): array
    {
        // Paystack amount is in Kobo (Naira * 100)
        $amountInKobo = (int) round($amountInNaira * 100);
        $reference = 'JC_'.time().'_'.bin2hex(random_bytes(4));

        if ($this->isTestPlaceholder()) {
            Log::info("Paystack test mode simulation initialized for {$email}", [
                'reference' => $reference,
                'amount' => $amountInNaira,
            ]);

            return [
                'status' => true,
                'message' => 'Authorization URL created (simulation)',
                'data' => [
                    'authorization_url' => url("/checkout/simulate-paystack?reference={$reference}&amount={$amountInNaira}&email=".urlencode($email)),
                    'access_code' => 'SIM_'.$reference,
                    'reference' => $reference,
                ],
            ];
        }

        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $email,
                'amount' => $amountInKobo,
                'callback_url' => $callbackUrl,
                'reference' => $reference,
                'metadata' => $metadata,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Paystack initialization failed', ['body' => $response->body()]);
        throw new \Exception($response->json('message') ?? 'Unable to connect to Paystack.');
    }

    /**
     * Verify payment status via Paystack API.
     */
    public function verifyTransaction(string $reference): array
    {
        if ($this->isTestPlaceholder() && str_starts_with($reference, 'JC_')) {
            Log::info("Paystack test mode verification simulated for reference: {$reference}");

            return [
                'status' => true,
                'message' => 'Verification successful (simulated)',
                'data' => [
                    'status' => 'success',
                    'reference' => $reference,
                    'amount' => 0, // checked in caller
                    'channel' => 'card',
                    'currency' => 'NGN',
                    'paid_at' => now()->toIso8601String(),
                ],
            ];
        }

        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Paystack verification failed', ['reference' => $reference, 'body' => $response->body()]);
        throw new \Exception($response->json('message') ?? 'Payment verification failed.');
    }

    /**
     * List Nigerian banks supported by Paystack.
     */
    public function listBanks(): array
    {
        if ($this->isTestPlaceholder()) {
            return [
                ['name' => 'Access Bank', 'code' => '044'],
                ['name' => 'Guaranty Trust Bank (GTB)', 'code' => '058'],
                ['name' => 'Zenith Bank', 'code' => '057'],
                ['name' => 'First Bank of Nigeria', 'code' => '011'],
                ['name' => 'United Bank for Africa (UBA)', 'code' => '033'],
                ['name' => 'Kuda Bank', 'code' => '50211'],
                ['name' => 'OPay', 'code' => '999992'],
            ];
        }

        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->get("{$this->baseUrl}/bank?country=nigeria");

        if ($response->successful()) {
            return $response->json('data') ?? [];
        }

        return [];
    }

    /**
     * Resolve account number to verify recipient bank account name.
     */
    public function resolveAccount(string $accountNumber, string $bankCode): array
    {
        if ($this->isTestPlaceholder()) {
            return [
                'status' => true,
                'account_number' => $accountNumber,
                'account_name' => 'VERIFIED RECIPIENT ACCOUNT',
            ];
        }

        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->get("{$this->baseUrl}/bank/resolve", [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

        if ($response->successful()) {
            return $response->json('data') ?? [];
        }

        throw new \Exception($response->json('message') ?? 'Unable to resolve bank account.');
    }

    /**
     * Create a Paystack Transfer Recipient.
     */
    public function createTransferRecipient(string $name, string $accountNumber, string $bankCode): array
    {
        if ($this->isTestPlaceholder()) {
            return [
                'status' => true,
                'data' => [
                    'recipient_code' => 'RCP_'.bin2hex(random_bytes(6)),
                    'name' => $name,
                    'details' => ['account_number' => $accountNumber, 'bank_code' => $bankCode],
                ],
            ];
        }

        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->post("{$this->baseUrl}/transferrecipient", [
                'type' => 'nuban',
                'name' => $name,
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
                'currency' => 'NGN',
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception($response->json('message') ?? 'Could not create transfer recipient.');
    }

    /**
     * Initiate a bank transfer for payroll payout.
     */
    public function initiateTransfer(string $recipientCode, float $amountInNaira, string $reason, ?string $reference = null): array
    {
        $amountInKobo = (int) round($amountInNaira * 100);
        $reference = $reference ?? ('TRF_'.time().'_'.bin2hex(random_bytes(4)));

        if ($this->isTestPlaceholder()) {
            return [
                'status' => true,
                'message' => 'Transfer initiated (simulation)',
                'data' => [
                    'transfer_code' => 'TRF_'.bin2hex(random_bytes(5)),
                    'reference' => $reference,
                    'status' => 'success',
                ],
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withToken($this->getSecretKey())
                ->acceptJson()
                ->post("{$this->baseUrl}/transfer", [
                    'source' => 'balance',
                    'amount' => $amountInKobo,
                    'recipient' => $recipientCode,
                    'reason' => $reason,
                    'reference' => $reference,
                ]);
        } catch (ConnectionException $e) {
            throw new PaystackTimeoutException('Connection to Paystack timed out: '.$e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            if ($e instanceof PaystackRejectionException || $e instanceof PaystackTimeoutException || $e instanceof PaystackServerException) {
                throw $e;
            }
            throw new PaystackTimeoutException($e->getMessage(), 0, $e);
        }

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        if ($response->clientError()) {
            throw new PaystackRejectionException($response->json('message') ?? 'Transfer rejected by Paystack (4xx).', $response->status());
        }

        throw new PaystackServerException($response->json('message') ?? 'Paystack server error (5xx).', $response->status());
    }

    /**
     * Verify a transfer status with Paystack by reference.
     */
    public function verifyTransfer(string $reference): array
    {
        if ($this->isTestPlaceholder()) {
            return [
                'status' => true,
                'message' => 'Transfer verified (simulation)',
                'data' => [
                    'reference' => $reference,
                    'status' => 'success',
                ],
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withToken($this->getSecretKey())
                ->acceptJson()
                ->get("{$this->baseUrl}/transfer/verify/{$reference}");
        } catch (ConnectionException $e) {
            throw new PaystackTimeoutException('Connection to Paystack timed out while verifying transfer: '.$e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            if ($e instanceof PaystackTimeoutException || $e instanceof PaystackServerException) {
                throw $e;
            }
            throw new PaystackTimeoutException($e->getMessage(), 0, $e);
        }

        if ($response->serverError()) {
            throw new PaystackServerException($response->json('message') ?? 'Paystack server error while verifying transfer.', $response->status());
        }

        return $response->json() ?? [];
    }
}
