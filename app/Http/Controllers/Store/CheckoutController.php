<?php

namespace App\Http\Controllers\Store;

use App\Exceptions\PaymentsNotConfiguredException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Services\CartService;
use App\Services\CurrentTenant;
use App\Services\PaystackService;
use App\Services\StoreNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected PaystackService $paystackService
    ) {}

    public function index(): View|RedirectResponse
    {
        $items = $this->cartService->getItems();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Your shopping bag is empty.');
        }

        $subtotal = $this->cartService->getSubtotal();
        $shippingFee = 3500.00; // Flat Lagos / Nigeria courier shipping
        $total = $subtotal + $shippingFee;
        $user = Auth::user();

        return view('store.checkout.index', compact('items', 'subtotal', 'shippingFee', 'total', 'user'));
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:25',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:paystack,wallet',
        ]);

        $items = $this->cartService->getItems();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Your shopping bag is empty.');
        }

        // Verify stock for all items
        foreach ($items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (! $variant || $variant->stock_quantity < $item['quantity']) {
                return redirect()->route('cart.index')->with(
                    'error',
                    "Sorry, '{$item['product_name']} ({$item['size']})' has only {$variant?->stock_quantity} in stock."
                );
            }
        }

        $subtotal = $this->cartService->getSubtotal();
        $shippingFee = 3500.00;
        $total = $subtotal + $shippingFee;
        $orderNumber = 'JC-'.strtoupper(Str::random(8));
        $paymentMethod = $request->input('payment_method', 'paystack');

        DB::beginTransaction();

        try {
            // Option A: Pay with Boutique Wallet
            if ($paymentMethod === 'wallet') {
                /** @var User $user */
                $user = Auth::user();
                if (! $user) {
                    return redirect()->route('login')->with('error', 'Please log in to pay with your boutique wallet.');
                }

                if ($user->wallet_balance < $total) {
                    return redirect()->back()->withInput()->with('error', 'Insufficient boutique wallet balance (Current: ₦'.number_format($user->wallet_balance, 0).'). Please fund your wallet or select Paystack.');
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'status' => 'processing',
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'tax' => 0.00,
                    'total_amount' => $total,
                    'shipping_name' => $request->shipping_name,
                    'shipping_email' => $request->shipping_email,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_address' => $request->shipping_address,
                    'shipping_city' => $request->shipping_city,
                    'payment_status' => 'paid',
                    'payment_method' => 'wallet',
                    'notes' => $request->notes,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $item['variant_id'],
                        'product_name' => $item['product_name'],
                        'variant_description' => "{$item['size']} - {$item['color']}",
                        'unit_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    $variant = ProductVariant::find($item['variant_id']);
                    if ($variant) {
                        $variant->decrement('stock_quantity', $item['quantity']);
                    }
                }

                $user->debitWallet(
                    $total,
                    "Payment for Order #{$orderNumber}",
                    'WALLET-ORD-'.$orderNumber,
                    $order->id
                );

                Payment::create([
                    'order_id' => $order->id,
                    'paystack_reference' => 'WALLET-'.$orderNumber,
                    'status' => 'successful',
                    'amount' => $total,
                    'currency' => 'NGN',
                    'customer_email' => $request->shipping_email,
                ]);

                $this->cartService->clear();

                DB::commit();

                if ($tenant = Tenant::find($order->tenant_id) ?? app(CurrentTenant::class)->get()) {
                    app(StoreNotificationService::class)->notifyStorefrontOrder($tenant, $order);
                }

                return redirect()->route('order.confirmation', $order->order_number)
                    ->with('success', 'Order placed successfully using your Boutique Wallet balance!');
            }

            // Option B: Pay with Paystack
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'tax' => 0.00,
                'total_amount' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'payment_status' => 'pending',
                'payment_method' => 'paystack',
                'notes' => $request->notes,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_description' => "{$item['size']} - {$item['color']}",
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            // Initialize Paystack payment
            $callbackUrl = route('checkout.callback');
            $paystackData = $this->paystackService->initializeTransaction(
                $request->shipping_email,
                $total,
                $callbackUrl,
                [
                    'order_id' => $order->id,
                    'order_number' => $orderNumber,
                ]
            );

            $reference = $paystackData['data']['reference'];
            $authUrl = $paystackData['data']['authorization_url'];

            Payment::create([
                'order_id' => $order->id,
                'paystack_reference' => $reference,
                'status' => 'pending',
                'amount' => $total,
                'currency' => 'NGN',
                'customer_email' => $request->shipping_email,
            ]);

            DB::commit();

            return redirect()->away($authUrl);
        } catch (PaymentsNotConfiguredException $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Online payments are currently not configured for this store.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout processing error: '.$e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Unable to initiate checkout: '.$e->getMessage());
        }
    }

    public function simulatePaystack(Request $request): View
    {
        $reference = $request->query('reference', 'JC_TEST_123');
        $amount = (float) $request->query('amount', 52000);
        $email = $request->query('email', 'customer@example.com');
        $callbackUrl = route('checkout.callback', ['reference' => $reference]);

        return view('store.checkout.simulate_paystack', compact('reference', 'amount', 'email', 'callbackUrl'));
    }

    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (! $reference) {
            return redirect()->route('cart.index')->with('error', 'No payment reference returned.');
        }

        // 1. Look up payment within current tenant scope only (DO NOT switch CurrentTenant)
        $payment = Payment::where('paystack_reference', $reference)->first();

        if (! $payment) {
            // Check withoutGlobalScopes() purely to decide between 404 and redirect to owning tenant's host
            $crossTenantPayment = Payment::withoutGlobalScopes()->where('paystack_reference', $reference)->first();

            if ($crossTenantPayment && $crossTenantPayment->tenant) {
                $targetHost = $this->resolveTenantHost($crossTenantPayment->tenant);
                if ($targetHost && $targetHost !== $request->getHost()) {
                    $scheme = $request->isSecure() ? 'https://' : 'http://';

                    return redirect()->away("{$scheme}{$targetHost}/checkout/callback?reference=".urlencode($reference));
                }
            }

            abort(404, 'Payment record not found.');
        }

        $order = $payment->order;
        if (! $order) {
            abort(404, 'Order not found for this payment.');
        }

        // 2. Require authenticated user to own the order (if order has an assigned user)
        if ($order->user_id !== null) {
            if (! Auth::check() || Auth::id() !== $order->user_id) {
                abort(403, 'Unauthorized access to this order payment.');
            }
        }

        // Verify transaction server-side
        try {
            $verifyData = $this->paystackService->verifyTransaction($reference);
            $status = $verifyData['data']['status'] ?? 'failed';

            if ($status === 'success') {
                if ($payment->status !== 'successful') {
                    DB::transaction(function () use ($payment, $order, $verifyData) {
                        $payment->update([
                            'status' => 'successful',
                            'paid_at' => now(),
                            'channel' => $verifyData['data']['channel'] ?? 'card',
                            'raw_response' => $verifyData,
                        ]);

                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                        ]);

                        // Decrement stock and create stock movement audit records
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
                    });

                    // Clear shopping cart on successful payment
                    $this->cartService->clear();

                    if ($tenant = Tenant::find($order->tenant_id) ?? app(CurrentTenant::class)->get()) {
                        app(StoreNotificationService::class)->notifyStorefrontOrder($tenant, $order);
                    }
                }

                return redirect()->route('order.confirmation', $order->order_number)
                    ->with('success', 'Payment confirmed! Your order has been placed.');
            } else {
                $payment->update(['status' => 'failed']);
                $order->update(['payment_status' => 'failed']);

                return redirect()->route('cart.index')->with('error', 'Payment was not successful. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Paystack verification error: '.$e->getMessage());

            return redirect()->route('cart.index')->with('error', 'Verification failed: '.$e->getMessage());
        }
    }

    public function confirmation(string $orderNumber): View
    {
        $order = Order::with(['items.variant.product', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('store.checkout.confirmation', compact('order'));
    }

    public function orders(): View
    {
        $orders = Order::with(['items', 'latestPayment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('store.orders.index', compact('orders'));
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
