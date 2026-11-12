<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    /**
     * Display a listing of store admin users with subscription tiers and nested staff/customers.
     */
    public function index(Request $request): View
    {
        $adminUsersBase = User::withoutTenancy()->where('role', 'admin');

        $totalAdminUsersCount = (clone $adminUsersBase)->count();
        $subscribedCount = (clone $adminUsersBase)->whereHas('tenant', function ($q) {
            $q->whereIn('plan', ['starter', 'pro', 'enterprise']);
        })->count();
        $freeCount = (clone $adminUsersBase)->where(function ($q) {
            $q->whereHas('tenant', function ($tq) {
                $tq->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
            })->orWhereNull('tenant_id');
        })->count();

        $query = User::withoutTenancy()
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
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($tq) use ($search) {
                        $tq->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('subdomain', 'like', "%{$search}%");
                    });
            });
        }

        if ($tier = $request->input('tier')) {
            if ($tier === 'subscribed') {
                $query->whereHas('tenant', function ($q) {
                    $q->whereIn('plan', ['starter', 'pro', 'enterprise']);
                });
            } elseif ($tier === 'free') {
                $query->where(function ($q) {
                    $q->whereHas('tenant', function ($tq) {
                        $tq->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
                    })->orWhereNull('tenant_id');
                });
            }
        }

        if ($plan = $request->input('plan')) {
            if ($plan !== 'all') {
                $query->whereHas('tenant', fn ($q) => $q->where('plan', $plan));
            }
        }

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->whereHas('tenant', fn ($q) => $q->where('status', $status));
            }
        }

        $storeAdmins = $query->paginate(15)->withQueryString();

        return view('admin.stores.index', compact(
            'storeAdmins',
            'totalAdminUsersCount',
            'subscribedCount',
            'freeCount'
        ));
    }
}
