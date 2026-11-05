<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\OcrImportBatch;
use App\Models\Order;
use App\Models\Product;
use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Platform Super Admin overview dashboard.
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', 'all');

        $tenantsCount = Tenant::count();
        $activeTenantsCount = Tenant::where('status', 'active')->count();
        $suspendedTenantsCount = Tenant::where('status', 'suspended')->count();

        $ordersQuery = Order::withoutTenancy();
        if ($period === 'today') {
            $ordersQuery->whereDate('created_at', Carbon::today());
        } elseif ($period === '7_days') {
            $ordersQuery->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($period === '30_days') {
            $ordersQuery->where('created_at', '>=', Carbon::now()->subDays(30));
        } elseif ($period === 'this_month') {
            $ordersQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        }

        $totalGmv = (float) (clone $ordersQuery)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrdersCount = (clone $ordersQuery)->count();
        $paidOrdersCount = (clone $ordersQuery)->where('payment_status', 'paid')->count();
        $averageOrderValue = $paidOrdersCount > 0 ? ($totalGmv / $paidOrdersCount) : 0.0;

        $totalProductsCount = Product::withoutTenancy()->count();
        $totalStaffCount = Staff::withoutTenancy()->count();

        $totalPayrollDisbursed = (float) SalaryPayment::withoutTenancy()
            ->where('status', 'success')
            ->sum('amount');

        $totalOcrBatches = OcrImportBatch::withoutTenancy()->count();

        // Estimated Platform Monthly Recurring Revenue (MRR) based on active tiers
        $mrrPricing = [
            'starter' => 25000,
            'pro' => 65000,
            'enterprise' => 180000,
            'standard' => 45000,
        ];
        $platformMrr = (float) Tenant::where('status', 'active')->get()->sum(function (Tenant $tenant) use ($mrrPricing) {
            return $mrrPricing[$tenant->plan] ?? 35000;
        });

        // Plan distribution
        $planCounts = [
            'starter' => Tenant::where('plan', 'starter')->count(),
            'pro' => Tenant::where('plan', 'pro')->count(),
            'enterprise' => Tenant::where('plan', 'enterprise')->count(),
            'standard' => Tenant::where('plan', 'standard')->count(),
        ];

        // 7-day GMV & Orders Trend for Chart.js
        $chartLabels = [];
        $chartGmvData = [];
        $chartOrdersData = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $dayString = $day->format('Y-m-d');
            $chartLabels[] = $day->format('D, M j');

            $dayGmv = (float) Order::withoutTenancy()
                ->where('payment_status', 'paid')
                ->whereDate('created_at', $dayString)
                ->sum('total_amount');

            $dayOrders = Order::withoutTenancy()
                ->whereDate('created_at', $dayString)
                ->count();

            $chartGmvData[] = $dayGmv;
            $chartOrdersData[] = $dayOrders;
        }

        // Leaderboard: Top stores by GMV
        $topTenants = Tenant::withCount([
            'products' => fn ($query) => $query->withoutTenancy(),
            'orders' => fn ($query) => $query->withoutTenancy(),
            'staff' => fn ($query) => $query->withoutTenancy(),
        ])
            ->get()
            ->map(function (Tenant $tenant) {
                $tenantGmv = (float) Order::withoutTenancy()
                    ->where('tenant_id', $tenant->id)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                $tenant->gmv = $tenantGmv;

                return $tenant;
            })
            ->sortByDesc('gmv')
            ->values()
            ->take(5);

        // Recent Tenants
        $recentTenants = Tenant::withCount([
            'products' => fn ($query) => $query->withoutTenancy(),
            'orders' => fn ($query) => $query->withoutTenancy(),
            'staff' => fn ($query) => $query->withoutTenancy(),
            'shopLocations' => fn ($query) => $query->withoutTenancy(),
        ])
            ->latest()
            ->take(6)
            ->get();

        // Recent Orders across platform
        $recentOrders = Order::withoutTenancy()
            ->with([
                'tenant',
                'user' => fn ($query) => $query->withoutTenancy(),
            ])
            ->latest()
            ->take(8)
            ->get();

        // System Telemetry Diagnostics
        $dbStatus = 'Operational';
        $dbLatency = 0.8;
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $dbLatency = round((microtime(true) - $start) * 1000, 2);
        } catch (\Throwable) {
            $dbStatus = 'Degraded';
        }

        $systemTelemetry = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_driver' => config('database.default'),
            'db_status' => $dbStatus,
            'db_latency' => $dbLatency,
            'cache_driver' => config('cache.default'),
            'app_env' => app()->environment(),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 1).' MB',
        ];

        return view('super-admin.dashboard', compact(
            'tenantsCount',
            'activeTenantsCount',
            'suspendedTenantsCount',
            'totalGmv',
            'totalOrdersCount',
            'paidOrdersCount',
            'averageOrderValue',
            'totalProductsCount',
            'totalStaffCount',
            'totalPayrollDisbursed',
            'totalOcrBatches',
            'platformMrr',
            'planCounts',
            'chartLabels',
            'chartGmvData',
            'chartOrdersData',
            'topTenants',
            'recentTenants',
            'recentOrders',
            'systemTelemetry',
            'period'
        ));
    }

    /**
     * Export all tenant stores data to CSV audit report.
     */
    public function exportAuditReport(): Response
    {
        $tenants = Tenant::withCount([
            'products' => fn ($q) => $q->withoutTenancy(),
            'orders' => fn ($q) => $q->withoutTenancy(),
            'staff' => fn ($q) => $q->withoutTenancy(),
        ])->get();

        $csvRows = [];
        $csvRows[] = ['Tenant ID', 'Name', 'Slug', 'Subdomain', 'Apex Domain', 'Plan', 'Status', 'Total SKUs', 'Total Orders', 'Total GMV (NGN)', 'Created Date'];

        foreach ($tenants as $tenant) {
            $gmv = Order::withoutTenancy()->where('tenant_id', $tenant->id)->where('payment_status', 'paid')->sum('total_amount');
            $csvRows[] = [
                $tenant->id,
                $tenant->name,
                $tenant->slug,
                $tenant->subdomain,
                $tenant->domain ?? 'N/A',
                strtoupper($tenant->plan),
                strtoupper($tenant->status),
                $tenant->products_count,
                $tenant->orders_count,
                number_format($gmv, 2, '.', ''),
                $tenant->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $handle = fopen('php://temp', 'r+');
        foreach ($csvRows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopmanager-audit-'.now()->format('Y-m-d').'.csv"',
        ]);
    }

    /**
     * Purge platform application caches and compiled views.
     */
    public function clearCache(): RedirectResponse
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        return back()->with('status', 'Platform cache and compiled Blade templates purged successfully.');
    }
}
