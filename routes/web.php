<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CpanelDeployController;
use App\Http\Controllers\PaystackWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store;
use App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsAdminOrStaff;
use App\Models\Product;
use App\Models\ShopLocation;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Jessi's ClassiQ Store
|--------------------------------------------------------------------------
*/

// Staging & cPanel Web Deployment Suite
Route::match(['get', 'post'], '/cpanel-deploy', CpanelDeployController::class)->name('cpanel.deploy');

// Paystack Multi-Tenant Webhook
// Raised to 600/min per IP to prevent dropping high-volume webhook bursts during payroll batch transfers or sales spikes.
// The HMAC-SHA512 tenant secret key signature verification is the cryptographic gate for payload authenticity.
Route::post('/paystack/webhook', PaystackWebhookController::class.'@handle')
    ->middleware('throttle:600,1')
    ->name('paystack.webhook');

// Public Storefront / Platform Welcome
Route::get('/', function () {
    $tenant = app(CurrentTenant::class)->get();
    if ($tenant && $tenant->hasFeature('storefront')) {
        return view('welcome');
    }

    return view('platform.welcome');
})->name('home');

Route::get('/products', [Store\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [Store\ProductController::class, 'show'])->name('products.show');

Route::get('/cart', [Store\CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [Store\CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{variant}', [Store\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{variant}', [Store\CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/clear', [Store\CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [Store\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [Store\CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/simulate-paystack', [Store\CheckoutController::class, 'simulatePaystack'])->name('checkout.simulate');
Route::get('/checkout/callback', [Store\CheckoutController::class, 'callback'])->name('checkout.callback');
Route::get('/orders/confirmation/{orderNumber}', [Store\CheckoutController::class, 'confirmation'])->name('order.confirmation');

Route::get('/store-location', [Store\ShopLocationController::class, 'index'])->name('store.location');

// Authenticated Customer Space
Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [Store\CheckoutController::class, 'orders'])->name('customer.orders');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Member Dashboard Route
Route::get('/dashboard', function () {
    /** @var User $user */
    $user = Auth::user();

    // 1. If super admin, redirect to platform super admin cockpit
    if ($user?->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    }

    // 2. Ensure active tenant is bound in context for this user
    if (! app(CurrentTenant::class)->check()) {
        $tenantId = $user?->tenant_id ?? session('active_tenant_id');
        $tenant = $tenantId ? Tenant::find($tenantId) : Tenant::first();
        if ($tenant) {
            app(CurrentTenant::class)->set($tenant);
            session(['active_tenant_id' => $tenant->id]);
        }
    }

    $orders = $user->orders()->with('items')->latest()->get();
    $curatedProducts = Product::with(['category', 'variants'])
        ->latest()
        ->take(8)
        ->get();
    $activeOrder = $orders->firstWhere('status', '!=', 'cancelled');
    $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
    $primaryLocation = ShopLocation::where('is_primary', true)->first();
    $walletTransactions = $user->walletTransactions()->latest()->take(5)->get();
    $totalSpend = $orders->where('payment_status', 'paid')->sum('total_amount');
    $vipTier = $totalSpend >= 250000 ? 'Diamond Privé' : ($totalSpend >= 100000 ? 'Platinum Privé' : ($totalSpend >= 30000 ? 'Gold Privé' : 'Silver Member'));

    return view('dashboard', compact('orders', 'curatedProducts', 'activeOrder', 'cartCount', 'primaryLocation', 'walletTransactions', 'vipTier', 'totalSpend'));
})->middleware(['auth'])->name('dashboard');

// Member Wallet Funding & Management Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/wallet/fund', [WalletController::class, 'fund'])->name('wallet.fund');
    Route::get('/wallet/callback', [WalletController::class, 'callback'])->name('wallet.callback');
    Route::post('/wallet/test-topup', [WalletController::class, 'testTopUp'])->name('wallet.test-topup');
});

// Admin & Staff Atelier Portal
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureUserIsAdminOrStaff::class])
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', Admin\ProductController::class);

        Route::get('/stock', [Admin\StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/adjust', [Admin\StockController::class, 'adjust'])->name('stock.adjust');

        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

        Route::get('/sales/create', [Admin\ManualSaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [Admin\ManualSaleController::class, 'store'])->name('sales.store');

        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [Admin\ReportController::class, 'exportCsv'])->name('reports.export');

        // OCR Paper Ledger Imports
        Route::get('/ocr-imports', [Admin\OcrImportController::class, 'index'])->name('ocr.index');
        Route::get('/ocr-imports/upload', [Admin\OcrImportController::class, 'create'])->name('ocr.create');
        Route::post('/ocr-imports', [Admin\OcrImportController::class, 'store'])->name('ocr.store');
        Route::get('/ocr-imports/{batch}', [Admin\OcrImportController::class, 'show'])->name('ocr.show');
        Route::get('/ocr-imports/{batch}/image', [Admin\OcrImportController::class, 'image'])->name('ocr.image');
        Route::get('/ocr-imports/{batch}/review', [Admin\OcrImportController::class, 'review'])->name('ocr.review');
        Route::post('/ocr-imports/{batch}/commit', [Admin\OcrImportController::class, 'commit'])->name('ocr.commit');

        // Staff & Payroll Management (Admin Only)
        Route::middleware(EnsureUserIsAdmin::class)->group(function () {
            Route::resource('staff', Admin\StaffController::class);
            Route::get('/payroll', [Admin\PayrollController::class, 'index'])->name('payroll.index');
            Route::post('/payroll/disburse', [Admin\PayrollController::class, 'disburse'])->name('payroll.disburse');
            Route::post('/payroll/retry/{salaryPayment}', [Admin\PayrollController::class, 'retry'])->name('payroll.retry');
            Route::post('/payroll/verify/{salaryPayment}', [Admin\PayrollController::class, 'verifyStatus'])->name('payroll.verify');
        });
    });

// Platform Super Admin Control Plane
Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'super_admin'])
    ->group(function () {
        Route::get('/', [SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/tenants', [SuperAdmin\TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [SuperAdmin\TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [SuperAdmin\TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [SuperAdmin\TenantController::class, 'show'])->name('tenants.show');
        Route::get('/tenants/{tenant}/edit', [SuperAdmin\TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}', [SuperAdmin\TenantController::class, 'update'])->name('tenants.update');
        Route::post('/tenants/{tenant}/toggle-status', [SuperAdmin\TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
        Route::post('/tenants/{tenant}/enter', [SuperAdmin\TenantController::class, 'enter'])->name('tenants.enter');
        Route::post('/tenants/{tenant}/admins', [SuperAdmin\TenantController::class, 'storeAdmin'])->name('tenants.admins.store');
        Route::post('/tenants/{tenant}/admins/{user}/reset-password', [SuperAdmin\TenantController::class, 'resetAdminPassword'])->name('tenants.admins.reset-password');
        Route::delete('/tenants/{tenant}/admins/{user}', [SuperAdmin\TenantController::class, 'destroyAdmin'])->name('tenants.admins.destroy');
        Route::get('/export-report', [SuperAdmin\DashboardController::class, 'exportAuditReport'])->name('export-report');
        Route::post('/clear-cache', [SuperAdmin\DashboardController::class, 'clearCache'])->name('clear-cache');
    });

require __DIR__.'/auth.php';
