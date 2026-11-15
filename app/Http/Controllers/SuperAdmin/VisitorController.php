<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorController extends Controller
{
    /**
     * Display platform and storefront visitor logs.
     */
    public function index(Request $request): View
    {
        $scope = (string) $request->input('scope', 'all');
        $search = trim((string) $request->input('search', ''));

        $baseQuery = SiteVisit::query()->with('tenant');

        if ($scope === 'platform') {
            $baseQuery->whereNull('tenant_id');
        } elseif ($scope !== 'all' && ctype_digit($scope)) {
            $baseQuery->where('tenant_id', (int) $scope);
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('path', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('referer', 'like', "%{$search}%");
            });
        }

        $totalVisits = SiteVisit::count();
        $uniqueVisitors = SiteVisit::whereNotNull('session_id')->distinct('session_id')->count('session_id');
        $todayVisits = SiteVisit::whereDate('visited_on', now()->toDateString())->count();
        $platformVisits = SiteVisit::whereNull('tenant_id')->count();

        $visits = $baseQuery->latest()->paginate(25)->withQueryString();
        $tenants = Tenant::orderBy('name')->get(['id', 'name']);

        return view('super-admin.visitors.index', compact(
            'visits',
            'tenants',
            'scope',
            'search',
            'totalVisits',
            'uniqueVisitors',
            'todayVisits',
            'platformVisits'
        ));
    }
}
