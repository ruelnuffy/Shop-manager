<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of all platform users, store owners, and staff with subscription status.
     */
    public function index(Request $request): View
    {
        $allUsersBase = User::withoutTenancy();

        $totalUsersCount = (clone $allUsersBase)->count();
        $adminCount = (clone $allUsersBase)->where('role', 'admin')->count();
        $staffCount = (clone $allUsersBase)->where('role', 'staff')->count();
        $customerCount = (clone $allUsersBase)->where('role', 'customer')->count();

        // Subscribed users: Admins/Merchants whose store is on a paid plan
        $subscribedUsersCount = (clone $allUsersBase)->whereHas('tenant', function ($q) {
            $q->whereIn('plan', ['starter', 'pro', 'enterprise']);
        })->count();

        // Free / Trial users: Users on standard/free plans or no paid plan
        $freeUsersCount = (clone $allUsersBase)->where(function ($q) {
            $q->whereHas('tenant', function ($tq) {
                $tq->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
            })->orWhereNull('tenant_id');
        })->where('role', '!=', 'super_admin')->count();

        $query = User::withoutTenancy()->with('tenant')->latest();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($tq) use ($search) {
                        $tq->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Role
        if ($role = $request->input('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        // Filter by Subscription Tier
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
                })->where('role', '!=', 'super_admin');
            }
        }

        $users = $query->paginate(20)->withQueryString();

        return view('super-admin.users.index', compact(
            'users',
            'totalUsersCount',
            'subscribedUsersCount',
            'freeUsersCount',
            'adminCount',
            'staffCount',
            'customerCount'
        ));
    }

    /**
     * Directly impersonate any platform user as Super Admin.
     */
    public function impersonate(Request $request, User $user): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser || ! $currentUser->isSuperAdmin()) {
            abort(403, 'Unauthorized. Platform Super Admin access required.');
        }

        if (! $request->session()->has('impersonator_id')) {
            $request->session()->put('impersonator_id', $currentUser->id);
            $request->session()->put('impersonator_role', 'super_admin');
            $request->session()->put('impersonator_name', $currentUser->name);
        }

        if ($user->tenant_id) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant) {
                $request->session()->put('active_tenant_id', $tenant->id);
                app(CurrentTenant::class)->set($tenant);

                if (function_exists('setPermissionsTeamId')) {
                    setPermissionsTeamId($tenant->id);
                }
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isAdmin() || $user->isStaff()) {
            $target = ($user->role === 'staff')
                ? route('admin.sales.create', [], false)
                : route('admin.dashboard', [], false);

            return redirect()->to($target)
                ->with('status', "Masquerade Active: Logged in directly as {$user->name} ({$user->role}).");
        }

        return redirect()->route('dashboard')
            ->with('status', "Masquerade Active: Logged in as customer {$user->name}.");
    }
}
