<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
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
     * Display detailed metrics and configuration for a single tenant.
     */
    public function show(Tenant $tenant): View
    {
        $tenant->loadCount([
            'products' => fn ($q) => $q->withoutTenancy(),
            'orders' => fn ($q) => $q->withoutTenancy(),
            'staff' => fn ($q) => $q->withoutTenancy(),
            'shopLocations' => fn ($q) => $q->withoutTenancy(),
            'ocrImportBatches' => fn ($q) => $q->withoutTenancy(),
        ]);

        $tenantRevenue = Order::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $tenantAdmins = User::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->get();

        $recentOrders = Order::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->with(['user' => fn ($q) => $q->withoutTenancy()])
            ->latest()
            ->take(8)
            ->get();

        return view('super-admin.tenants.show', compact('tenant', 'tenantRevenue', 'tenantAdmins', 'recentOrders'));
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
