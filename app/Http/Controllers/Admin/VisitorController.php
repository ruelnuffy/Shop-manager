<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitorController extends Controller
{
    /**
     * Display storefront visitor analytics for the active tenant.
     */
    public function index(Request $request): View
    {
        $tenantId = app(CurrentTenant::class)->id();
        $search = trim((string) $request->input('search', ''));

        $baseQuery = SiteVisit::query()->where('tenant_id', $tenantId);

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('path', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('referer', 'like', "%{$search}%");
            });
        }

        $visitsToday = SiteVisit::where('tenant_id', $tenantId)
            ->whereDate('visited_on', now()->toDateString())
            ->count();
        $uniqueVisitors = SiteVisit::where('tenant_id', $tenantId)
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');
        $totalVisits = SiteVisit::where('tenant_id', $tenantId)->count();

        $visits = $baseQuery->latest()->paginate(25)->withQueryString();

        return view('admin.visitors.index', compact(
            'visits',
            'search',
            'visitsToday',
            'uniqueVisitors',
            'totalVisits'
        ));
    }
}
