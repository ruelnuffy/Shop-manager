<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Display a listing of all tenant stores on the platform.
     */
    public function index(Request $request): View
    {
        $query = Tenant::withCount([
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
                    ->orWhere('domain', 'like', "%{$search}%");
            });
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

        $tenants = $query->paginate(15)->withQueryString();

        return view('super-admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant store.
     */
    public function create(): View
    {
        return view('super-admin.tenants.create');
    }

    /**
     * Store a newly created tenant and provision its initial store admin user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:tenants,slug',
            'subdomain' => 'nullable|string|max:100|alpha_dash|unique:tenants,subdomain',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'plan' => 'required|string|in:starter,pro,enterprise,standard',
            'status' => 'required|string|in:active,suspended',
            'features' => 'nullable|array',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        $slug = Str::slug($validated['slug']);
        $subdomain = ! empty($validated['subdomain']) ? Str::slug($validated['subdomain']) : $slug;

        $features = [
            'storefront' => ! empty($request->input('features.storefront')),
            'payroll' => ! empty($request->input('features.payroll')),
            'ocr' => ! empty($request->input('features.ocr')),
            'custom_domain' => ! empty($request->input('features.custom_domain')),
        ];

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'subdomain' => $subdomain,
            'domain' => ($validated['domain'] ?? null) ?: null,
            'plan' => $validated['plan'],
            'status' => $validated['status'],
            'settings' => [
                'currency' => 'NGN',
                'currency_symbol' => '₦',
                'features' => $features,
                'branding' => [
                    'name' => $validated['name'],
                    'tagline' => 'Powered by '.config('app.platform_name', 'ShopManager'),
                ],
            ],
        ]);

        // Provision Initial Store Admin User
        User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('status', "Store '{$tenant->name}' provisioned successfully with owner {$validated['admin_email']}!");
    }

    /**
     * Display detailed metrics, sales reports, analytics, and administrator accounts for a single tenant.
     */
    public function show(Tenant $tenant, Request $request): View
    {
        $tenant->loadCount([
            'products' => fn ($q) => $q->withoutTenancy(),
            'orders' => fn ($q) => $q->withoutTenancy(),
            'staff' => fn ($q) => $q->withoutTenancy(),
            'shopLocations' => fn ($q) => $q->withoutTenancy(),
            'ocrImportBatches' => fn ($q) => $q->withoutTenancy(),
        ]);

        $period = $request->input('period', 'all');

        $ordersQuery = Order::withoutTenancy()->where('tenant_id', $tenant->id);
        if ($period === 'today') {
            $ordersQuery->whereDate('created_at', Carbon::today());
        } elseif ($period === '7_days') {
            $ordersQuery->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($period === '30_days') {
            $ordersQuery->where('created_at', '>=', Carbon::now()->subDays(30));
        } elseif ($period === 'this_month') {
            $ordersQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        }

        $allTimeRevenue = (float) Order::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $periodRevenue = (float) (clone $ordersQuery)->where('payment_status', 'paid')->sum('total_amount');
        $periodPaidCount = (clone $ordersQuery)->where('payment_status', 'paid')->count();
        $periodTotalOrders = (clone $ordersQuery)->count();
        $periodAov = $periodPaidCount > 0 ? ($periodRevenue / $periodPaidCount) : 0.0;

        $orderStatusCounts = [
            'paid' => (clone $ordersQuery)->where('payment_status', 'paid')->count(),
            'pending' => (clone $ordersQuery)->where('payment_status', 'pending')->count(),
            'failed' => (clone $ordersQuery)->where('payment_status', 'failed')->count(),
        ];

        // Maintain compatibility with existing blade references
        $tenantRevenue = $allTimeRevenue;

        $tenantAdmins = User::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->latest()
            ->get();

        $recentOrders = Order::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->with(['user' => fn ($q) => $q->withoutTenancy()])
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.tenants.show', compact(
            'tenant',
            'tenantRevenue',
            'allTimeRevenue',
            'period',
            'periodRevenue',
            'periodPaidCount',
            'periodTotalOrders',
            'periodAov',
            'orderStatusCounts',
            'tenantAdmins',
            'recentOrders'
        ));
    }

    /**
     * Create an additional store administrator account for the tenant.
     */
    public function storeAdmin(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        return back()->with('status', "Admin account [{$validated['email']}] added successfully to {$tenant->name}.");
    }

    /**
     * Reset the password of a store administrator.
     */
    public function resetAdminPassword(Request $request, Tenant $tenant, User $user): RedirectResponse
    {
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', "Password for admin [{$user->email}] has been reset successfully.");
    }

    /**
     * Remove an administrator from the tenant store.
     */
    public function destroyAdmin(Tenant $tenant, User $user): RedirectResponse
    {
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $adminCount = User::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->count();

        if ($adminCount <= 1) {
            return back()->withErrors(['admin_error' => 'Cannot remove the sole administrator of this store. Each store must maintain at least one admin account.']);
        }

        $email = $user->email;
        $user->delete();

        return back()->with('status', "Admin account [{$email}] was removed from {$tenant->name}.");
    }

    /**
     * Show the form for editing an existing tenant.
     */
    public function edit(Tenant $tenant): View
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant store in storage.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:tenants,slug,'.$tenant->id,
            'subdomain' => 'nullable|string|max:100|alpha_dash|unique:tenants,subdomain,'.$tenant->id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,'.$tenant->id,
            'plan' => 'required|string|in:starter,pro,enterprise,standard',
            'status' => 'required|string|in:active,suspended',
            'features' => 'nullable|array',
            'tagline' => 'nullable|string|max:255',
        ]);

        $settings = $tenant->settings ?? [];
        $settings['features'] = [
            'storefront' => ! empty($request->input('features.storefront')),
            'payroll' => ! empty($request->input('features.payroll')),
            'ocr' => ! empty($request->input('features.ocr')),
            'custom_domain' => ! empty($request->input('features.custom_domain')),
        ];

        if (isset($validated['tagline'])) {
            data_set($settings, 'branding.tagline', $validated['tagline']);
        }
        data_set($settings, 'branding.name', $validated['name']);

        $tenant->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'subdomain' => ! empty($validated['subdomain']) ? Str::slug($validated['subdomain']) : Str::slug($validated['slug']),
            'domain' => ($validated['domain'] ?? null) ?: null,
            'plan' => $validated['plan'],
            'status' => $validated['status'],
            'settings' => $settings,
        ]);

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('status', "Store '{$tenant->name}' settings updated successfully.");
    }

    /**
     * Toggle a store's status between active and suspended.
     */
    public function toggleStatus(Tenant $tenant): RedirectResponse
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'activated' : 'suspended';

        return back()->with('status', "Store '{$tenant->name}' has been {$label}.");
    }

    /**
     * Seamlessly impersonate / enter the tenant store as admin.
     */
    public function enter(Tenant $tenant): RedirectResponse
    {
        session(['active_tenant_id' => $tenant->id]);

        return redirect()->to('/admin?tenant='.$tenant->slug);
    }
}
