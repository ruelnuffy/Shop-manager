<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\OcrImportBatch;
use App\Models\Order;
use App\Models\Product;
use App\Models\SalaryPayment;
use App\Models\Staff;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Platform Super Admin overview dashboard.
     */
    public function index(): View
    {
        $tenantsCount = Tenant::count();
        $activeTenantsCount = Tenant::where('status', 'active')->count();
        $suspendedTenantsCount = Tenant::where('status', 'suspended')->count();

        $totalGmv = Order::withoutTenancy()
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalOrdersCount = Order::withoutTenancy()->count();
        $totalProductsCount = Product::withoutTenancy()->count();
        $totalStaffCount = Staff::withoutTenancy()->count();

        $totalPayrollDisbursed = SalaryPayment::withoutTenancy()
            ->where('status', 'success')
            ->sum('amount');

        $totalOcrBatches = OcrImportBatch::withoutTenancy()->count();

        $recentTenants = Tenant::withCount([
            'products' => fn ($query) => $query->withoutTenancy(),
            'orders' => fn ($query) => $query->withoutTenancy(),
            'staff' => fn ($query) => $query->withoutTenancy(),
            'shopLocations' => fn ($query) => $query->withoutTenancy(),
        ])
            ->latest()
            ->take(6)
            ->get();

        $recentOrders = Order::withoutTenancy()
            ->with([
                'tenant',
                'user' => fn ($query) => $query->withoutTenancy(),
            ])
            ->latest()
            ->take(8)
            ->get();

        return view('super-admin.dashboard', compact(
            'tenantsCount',
            'activeTenantsCount',
            'suspendedTenantsCount',
            'totalGmv',
            'totalOrdersCount',
            'totalProductsCount',
            'totalStaffCount',
            'totalPayrollDisbursed',
            'totalOcrBatches',
            'recentTenants',
            'recentOrders'
        ));
    }
}
