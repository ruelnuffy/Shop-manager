<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display a listing of stores as users with subscription tiers on the admin console.
     */
    public function index(Request $request): View
    {
        $totalStoresCount = Tenant::count();
        $subscribedCount = Tenant::whereIn('plan', ['starter', 'pro', 'enterprise'])->count();
        $freeCount = Tenant::where(function ($q) {
            $q->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
        })->count();

        $query = Tenant::with([
            'users' => fn ($q) => $q->withoutTenancy()->where('role', 'admin')->latest(),
        ])->withCount([
            'products' => fn ($q) => $q->withoutTenancy(),
            'orders' => fn ($q) => $q->withoutTenancy(),
            'staff' => fn ($q) => $q->withoutTenancy(),
            'shopLocations' => fn ($q) => $q->withoutTenancy(),
        ])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('subdomain', 'like', "%{$search}%")
                    ->orWhere('domain', 'like', "%{$search}%")
                    ->orWhereHas('users', function ($uq) use ($search) {
                        $uq->withoutTenancy()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($tier = $request->input('tier')) {
            if ($tier === 'subscribed') {
                $query->whereIn('plan', ['starter', 'pro', 'enterprise']);
            } elseif ($tier === 'free') {
                $query->where(function ($q) {
                    $q->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
                });
            }
        }

        if ($plan = $request->input('plan')) {
            if ($plan !== 'all') {
                $query->where('plan', $plan);
            }
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $stores = $query->paginate(15)->withQueryString();

        return view('admin.stores.index', compact('stores', 'totalStoresCount', 'subscribedCount', 'freeCount'));
    }
}
