<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\SiteVisit;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $metrics = $this->buildMetrics(useCache: true);

        $totalAdminUsersCount = 0;
        $subscribedUsersCount = 0;
        $freeUsersCount = 0;
        $storeAdmins = collect();

        if (request()->user()?->isSuperAdmin()) {
            $adminUsersBase = User::withoutTenancy()->where('role', 'admin');

            $totalAdminUsersCount = (clone $adminUsersBase)->count();
            $subscribedUsersCount = (clone $adminUsersBase)->whereHas('tenant', function ($q) {
                $q->whereIn('plan', ['starter', 'pro', 'enterprise']);
            })->count();
            $freeUsersCount = (clone $adminUsersBase)->where(function ($q) {
                $q->whereHas('tenant', function ($tq) {
                    $tq->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
                })->orWhereNull('tenant_id');
            })->count();

            $storeAdmins = User::withoutTenancy()
                ->where('role', 'admin')
                ->with(['tenant' => function ($tq) {
                    $tq->with([
                        'users' => fn ($uq) => $uq->withoutTenancy()->whereIn('role', ['staff', 'customer']),
                    ])->withCount([
                        'products' => fn ($q) => $q->withoutTenancy(),
                        'orders' => fn ($q) => $q->withoutTenancy(),
                        'staff' => fn ($q) => $q->withoutTenancy(),
                    ]);
                }])
                ->latest()
                ->get();
        }

        return view('admin.dashboard', array_merge($metrics, compact(
            'totalAdminUsersCount',
            'subscribedUsersCount',
            'freeUsersCount',
            'storeAdmins'
        )));
    }

    /**
     * Return fresh dashboard metrics as JSON for live AJAX updates.
     */
    public function liveData(Request $request): JsonResponse
    {
        $metrics = $this->buildMetrics(useCache: false);

        /** @var Collection<int, Order> $recentOrders */
        $recentOrders = $metrics['recentOrders'];

        /** @var Collection<int, ProductVariant> $lowStockVariants */
        $lowStockVariants = $metrics['lowStockVariants'];

        return response()->json([
            'today_revenue' => (float) $metrics['todayRevenue'],
            'week_revenue' => (float) $metrics['weekRevenue'],
            'month_revenue' => (float) $metrics['monthRevenue'],
            'total_orders_count' => (int) $metrics['totalOrdersCount'],
            'pending_orders_count' => (int) $metrics['pendingOrdersCount'],
            'processing_orders_count' => (int) $metrics['processingOrdersCount'],
            'total_inventory_valuation' => (float) $metrics['totalInventoryValuation'],
            'low_stock_count' => $lowStockVariants->count(),
            'low_stock_items' => $lowStockVariants->take(3)->map(function (ProductVariant $variant): array {
                return [
                    'product_name' => $variant->product?->name ?? 'Product',
                    'label' => $variant->size ?? $variant->color ?? 'variant',
                    'stock_quantity' => (int) $variant->stock_quantity,
                ];
            })->values(),
            'storefront_visits_today' => (int) $metrics['storefrontVisitsToday'],
            'storefront_visits_total' => (int) $metrics['storefrontVisitsTotal'],
            'storefront_unique_visitors' => (int) $metrics['storefrontUniqueVisitors'],
            'unread_notifications_count' => $request->user()?->unreadNotifications()->count() ?? 0,
            'recent_orders' => $recentOrders->map(function (Order $order): array {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->shipping_name ?? 'Walk-in Customer',
                    'items_count' => $order->items->count(),
                    'items_label' => Str::plural('item', $order->items->count()),
                    'time_ago' => $order->created_at?->diffForHumans() ?? 'Just now',
                    'total_amount' => (float) $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'status' => $order->status,
                    'show_url' => route('admin.orders.show', $order->id),
                ];
            })->values(),
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Compute core storefront admin dashboard metrics.
     *
     * @return array<string, mixed>
     */
    protected function buildMetrics(bool $useCache): array
    {
        $computeStats = function (): array {
            $today = now()->startOfDay();
            $startOfWeek = now()->startOfWeek();
            $startOfMonth = now()->startOfMonth();

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
            foreach ($allVariants as $variant) {
                $totalInventoryValuation += $variant->effective_price * $variant->stock_quantity;
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
        };

        $cacheKey = CurrentTenant::cacheKey('dashboard_stats');
        if ($useCache) {
            $stats = Cache::remember($cacheKey, 60, $computeStats);
        } else {
            $stats = $computeStats();
            Cache::put($cacheKey, $stats, 60);
        }

        $lowStockVariants = ProductVariant::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->take(6)
            ->get();

        $recentOrders = Order::with('items')->latest()->take(6)->get();
        $recentStockMovements = StockMovement::with(['variant.product', 'creator'])->latest()->take(5)->get();

        $tenantId = app(CurrentTenant::class)->id();
        $storefrontVisitsToday = $tenantId
            ? SiteVisit::where('tenant_id', $tenantId)->whereDate('visited_on', now()->toDateString())->count()
            : 0;
        $storefrontVisitsTotal = $tenantId
            ? SiteVisit::where('tenant_id', $tenantId)->count()
            : 0;
        $storefrontUniqueVisitors = $tenantId
            ? SiteVisit::where('tenant_id', $tenantId)->whereNotNull('session_id')->distinct('session_id')->count('session_id')
            : 0;

        return array_merge($stats, compact(
            'lowStockVariants',
            'recentOrders',
            'recentStockMovements',
            'storefrontVisitsToday',
            'storefrontVisitsTotal',
            'storefrontUniqueVisitors'
        ));
    }
}
