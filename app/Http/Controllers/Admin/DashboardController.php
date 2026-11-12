<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Services\CurrentTenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        $stats = Cache::remember(CurrentTenant::cacheKey('dashboard_stats'), 60, function () use ($today, $startOfWeek, $startOfMonth) {
            $todayRevenue = Order::where('payment_status', 'paid')->where('created_at', '>=', $today)->sum('total_amount')
                + SalesRecord::where('status', 'confirmed')->where('created_at', '>=', $today)->sum('amount');

            $weekRevenue = Order::where('payment_status', 'paid')->where('created_at', '>=', $startOfWeek)->sum('total_amount')
                + SalesRecord::where('status', 'confirmed')->where('created_at', '>=', $startOfWeek)->sum('amount');

            $monthRevenue = Order::where('payment_status', 'paid')->where('created_at', '>=', $startOfMonth)->sum('total_amount')
                + SalesRecord::where('status', 'confirmed')->where('created_at', '>=', $startOfMonth)->sum('amount');

            $totalOrdersCount = Order::count();
            $pendingOrdersCount = Order::where('status', 'pending')->count();
            $processingOrdersCount = Order::where('status', 'processing')->count();

            $totalInventoryValuation = 0;
            $allVariants = ProductVariant::with('product')->where('is_active', true)->get();
            foreach ($allVariants as $v) {
                $totalInventoryValuation += $v->effective_price * $v->stock_quantity;
            }

            return compact(
                'todayRevenue',
                'weekRevenue',
                'monthRevenue',
                'totalOrdersCount',
                'pendingOrdersCount',
                'processingOrdersCount',
                'totalInventoryValuation'
            );
        });

        // Low stock alerts (variants with stock <= 5)
        $lowStockVariants = ProductVariant::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->take(6)
            ->get();

        // Recent Orders
        $recentOrders = Order::with('items')->latest()->take(6)->get();

        // Recent Stock Movements
        $recentStockMovements = StockMovement::with(['variant.product', 'creator'])->latest()->take(5)->get();

        // Platform Stores as Users & Subscriptions breakdown (for Store Administrators)
        $totalStoresCount = Tenant::count();
        $subscribedStoresCount = Tenant::whereIn('plan', ['starter', 'pro', 'enterprise'])->count();
        $freeStoresCount = Tenant::where(function ($q) {
            $q->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
        })->count();

        $allStores = Tenant::with([
            'users' => fn ($q) => $q->withoutTenancy()->where('role', 'admin')->latest(),
        ])->withCount([
            'products' => fn ($q) => $q->withoutTenancy(),
            'orders' => fn ($q) => $q->withoutTenancy(),
            'staff' => fn ($q) => $q->withoutTenancy(),
            'shopLocations' => fn ($q) => $q->withoutTenancy(),
        ])->latest()->get();

        return view('admin.dashboard', array_merge($stats, compact(
            'lowStockVariants',
            'recentOrders',
            'recentStockMovements',
            'totalStoresCount',
            'subscribedStoresCount',
            'freeStoresCount',
            'allStores'
        )));
    }
}
