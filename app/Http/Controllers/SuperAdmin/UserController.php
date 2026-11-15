<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of all platform users, store owners, and staff with subscription status.
     */
    public function index(Request $request): View
    {
        $adminUsersBase = User::withoutTenancy()->where('role', 'admin');

        $totalUsersCount = (clone $adminUsersBase)->count();
        $adminCount = $totalUsersCount;
        $staffCount = User::withoutTenancy()->where('role', 'staff')->count();
        $customerCount = User::withoutTenancy()->where('role', 'customer')->count();

        // Subscribed users: Store Admins whose store is on a paid plan
        $subscribedUsersCount = (clone $adminUsersBase)->whereHas('tenant', function ($q) {
            $q->whereIn('plan', ['starter', 'pro', 'enterprise']);
        })->count();

        // Free / Trial users: Store Admins on standard/free plans
        $freeUsersCount = (clone $adminUsersBase)->where(function ($q) {
            $q->whereHas('tenant', function ($tq) {
                $tq->whereNotIn('plan', ['starter', 'pro', 'enterprise'])->orWhereNull('plan');
            })->orWhereNull('tenant_id');
        })->count();

        $query = User::withoutTenancy()
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

        // Default to Store Admin accounts only (staff and customers are nested under each admin)
        $role = $request->input('role', 'admin');
        if ($role !== 'all') {
            $query->where('role', $role);
        }

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
                });
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

    /**
     * Update a platform user's profile details and store plan/status (Super Admin only).
     */
    public function update(Request $request, int|string $user): RedirectResponse
    {
        $targetUser = User::withoutTenancy()->with('tenant')->findOrFail($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->where(fn ($query) => $query->where('tenant_id', $targetUser->tenant_id))
                    ->ignore($targetUser->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'store_name' => ['nullable', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'in:starter,pro,enterprise,standard'],
            'status' => ['nullable', 'string', 'in:active,pending,suspended'],
        ]);

        $targetUser->update([
            'name' => trim($validated['name']),
            'email' => $validated['email'],
            'phone' => isset($validated['phone']) ? trim($validated['phone']) : null,
        ]);

        if ($targetUser->tenant) {
            $tenantUpdates = [];
            if (! empty($validated['plan'])) {
                $tenantUpdates['plan'] = $validated['plan'];
            }
            if (! empty($validated['status'])) {
                $tenantUpdates['status'] = $validated['status'];
            }
            if (! empty($validated['store_name']) && $targetUser->isAdmin()) {
                $storeName = trim($validated['store_name']);
                $settings = $targetUser->tenant->settings ?? [];
                data_set($settings, 'branding.name', $storeName);
                $tenantUpdates['name'] = $storeName;
                $tenantUpdates['settings'] = $settings;
            }
            if ($tenantUpdates !== []) {
                $targetUser->tenant->update($tenantUpdates);
            }
        }

        return back()->with('status', "Updated profile and plan details for {$targetUser->name}.");
    }

    /**
     * Reset a platform user's password (Super Admin only).
     */
    public function resetPassword(Request $request, int|string $user): RedirectResponse
    {
        $targetUser = User::withoutTenancy()->findOrFail($user);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $targetUser->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', "Password reset successfully for {$targetUser->email}.");
    }
}
