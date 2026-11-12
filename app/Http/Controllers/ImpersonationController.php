<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImpersonationController extends Controller
{
    /**
     * Platform Super Admin logs in directly as a Tenant's Store Admin.
     */
    public function superAdminEnterAdmin(Request $request, Tenant $tenant): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser || ! $currentUser->isSuperAdmin()) {
            abort(403, 'Unauthorized. Platform Super Admin access required.');
        }

        // Find primary or first admin for this tenant
        $admin = User::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->first();

        if (! $admin) {
            // Provision a default admin user for this tenant if none exists
            $admin = User::withoutTenancy()->create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name.' Admin',
                'email' => 'admin.'.Str::slug($tenant->slug).'@shopmanager.test',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $admin->syncRoles(['admin']);
        }

        // Record impersonation provenance in session if not already in an impersonation session
        if (! $request->session()->has('impersonator_id')) {
            $request->session()->put('impersonator_id', $currentUser->id);
            $request->session()->put('impersonator_role', 'super_admin');
            $request->session()->put('impersonator_name', $currentUser->name);
        }

        $request->session()->put('active_tenant_id', $tenant->id);
        app(CurrentTenant::class)->set($tenant);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant->id);
        }

        Auth::login($admin);
        $request->session()->regenerate();

        return redirect()->to(route('admin.dashboard', ['tenant' => $tenant->slug], false))
            ->with('status', "Masquerade Active: Logged in as {$admin->name} ({$tenant->name} Admin).");
    }

    /**
     * Platform Super Admin logs in directly as a Tenant's Staff / POS Cashier.
     */
    public function superAdminEnterStaff(Request $request, Tenant $tenant): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser || ! $currentUser->isSuperAdmin()) {
            abort(403, 'Unauthorized. Platform Super Admin access required.');
        }

        $staffUser = null;
        if ($userId = $request->input('user_id')) {
            $staffUser = User::withoutTenancy()
                ->where('tenant_id', $tenant->id)
                ->where('id', $userId)
                ->first();
        }

        if (! $staffUser) {
            $staffUser = User::withoutTenancy()
                ->where('tenant_id', $tenant->id)
                ->where('role', 'staff')
                ->first();
        }

        if (! $staffUser) {
            // Provision a default staff member for this tenant
            $staffUser = User::withoutTenancy()->create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name.' Staff',
                'email' => 'staff.'.Str::slug($tenant->slug).'@shopmanager.test',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'email_verified_at' => now(),
            ]);
            $staffUser->syncRoles(['staff']);

            Staff::create([
                'tenant_id' => $tenant->id,
                'user_id' => $staffUser->id,
                'position' => 'Store Cashier',
                'is_active' => true,
                'monthly_salary' => 120000,
            ]);
        }

        if (! $request->session()->has('impersonator_id')) {
            $request->session()->put('impersonator_id', $currentUser->id);
            $request->session()->put('impersonator_role', 'super_admin');
            $request->session()->put('impersonator_name', $currentUser->name);
        }

        $request->session()->put('active_tenant_id', $tenant->id);
        app(CurrentTenant::class)->set($tenant);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant->id);
        }

        Auth::login($staffUser);
        $request->session()->regenerate();

        return redirect()->to(route('admin.sales.create', ['tenant' => $tenant->slug], false))
            ->with('status', "Masquerade Active: Logged in directly to {$tenant->name} Staff Till as {$staffUser->name}.");
    }

    /**
     * Store Admin logs in directly to a staff member's view/dashboard within their store.
     */
    public function adminEnterStaff(Request $request): RedirectResponse
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        if (! $currentUser || ! $currentUser->isAdmin()) {
            abort(403, 'Unauthorized access. Only Store Administrators can enter staff view.');
        }

        $tenantId = app(CurrentTenant::class)->id() ?? $currentUser->tenant_id;
        $tenant = $tenantId ? Tenant::find($tenantId) : null;

        if (! $tenant) {
            abort(404, 'Store context could not be resolved.');
        }

        $staffUser = null;
        if ($userId = $request->input('user_id')) {
            $staffUser = User::withoutTenancy()
                ->where('tenant_id', $tenant->id)
                ->where('id', $userId)
                ->where('role', 'staff')
                ->first();
        }

        if (! $staffUser) {
            $staffUser = User::withoutTenancy()
                ->where('tenant_id', $tenant->id)
                ->where('role', 'staff')
                ->first();
        }

        if (! $staffUser) {
            $staffUser = User::withoutTenancy()->create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name.' Staff',
                'email' => 'staff.'.Str::slug($tenant->slug).'@shopmanager.test',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'email_verified_at' => now(),
            ]);
            $staffUser->syncRoles(['staff']);

            Staff::create([
                'tenant_id' => $tenant->id,
                'user_id' => $staffUser->id,
                'position' => 'Store Cashier',
                'is_active' => true,
                'monthly_salary' => 120000,
            ]);
        }

        if (! $request->session()->has('impersonator_id')) {
            $request->session()->put('impersonator_id', $currentUser->id);
            $request->session()->put('impersonator_role', 'admin');
            $request->session()->put('impersonator_name', $currentUser->name);
        }

        $request->session()->put('active_tenant_id', $tenant->id);
        app(CurrentTenant::class)->set($tenant);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant->id);
        }

        Auth::login($staffUser);
        $request->session()->regenerate();

        return redirect()->to(route('admin.sales.create', [], false))
            ->with('status', "Staff View Active: Switched to staff dashboard as {$staffUser->name}.");
    }

    /**
     * Revert impersonation session and return to original administrator.
     */
    public function leave(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->get('impersonator_id');
        $impersonatorRole = $request->session()->get('impersonator_role', 'admin');

        if (! $impersonatorId) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['impersonation' => 'No active impersonation session found.']);
        }

        $originalUser = User::withoutTenancy()->find($impersonatorId);

        if (! $originalUser) {
            $request->session()->forget(['impersonator_id', 'impersonator_role', 'impersonator_name']);
            Auth::logout();

            return redirect()->route('login');
        }

        // Clear impersonation markers from session
        $request->session()->forget(['impersonator_id', 'impersonator_role', 'impersonator_name']);

        Auth::login($originalUser);
        $request->session()->regenerate();

        if ($impersonatorRole === 'super_admin') {
            $request->session()->forget('active_tenant_id');
            app(CurrentTenant::class)->forget();

            if (function_exists('setPermissionsTeamId')) {
                setPermissionsTeamId(null);
            }

            return redirect()->route('super-admin.tenants.index')
                ->with('status', 'Returned safely to Super Admin Cockpit.');
        }

        // Return to Store Admin dashboard
        if ($originalUser->tenant_id) {
            $tenant = Tenant::find($originalUser->tenant_id);
            if ($tenant) {
                $request->session()->put('active_tenant_id', $tenant->id);
                app(CurrentTenant::class)->set($tenant);

                if (function_exists('setPermissionsTeamId')) {
                    setPermissionsTeamId($tenant->id);
                }
            }
        }

        return redirect()->route('admin.dashboard')
            ->with('status', "Returned to Admin Dashboard ({$originalUser->name}).");
    }
}
