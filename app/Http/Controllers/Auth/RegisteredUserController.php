<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewAccountRegisteredMail;
use App\Models\Category;
use App\Models\ShopLocation;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $currentTenant = app(CurrentTenant::class);

        // Platform SaaS Merchant Onboarding (store_name provided or no active tenant)
        if ($request->filled('store_name') || ! $currentTenant->check()) {
            $request->validate([
                'store_name' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'phone' => ['required', 'string', 'max:30'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'plan' => ['nullable', 'string', 'in:starter,pro,enterprise'],
            ]);

            $phone = trim($request->string('phone')->toString());

            $user = DB::transaction(function () use ($request, $currentTenant, $phone) {
                $storeName = trim($request->string('store_name')->toString());
                $baseSlug = Str::slug($storeName) ?: 'store';
                $reserved = config('tenancy.reserved_subdomains', ['www', 'app', 'admin', 'api', 'mail', 'billing', 'support', 'status']);

                if (in_array($baseSlug, $reserved, true)) {
                    $baseSlug .= '-store';
                }

                $slug = $baseSlug;
                $counter = 1;
                while (Tenant::where('slug', $slug)->orWhere('subdomain', $slug)->exists()) {
                    $counter++;
                    $slug = "{$baseSlug}-{$counter}";
                }

                $chosenPlan = $request->input('plan', 'starter');
                if (! in_array($chosenPlan, ['starter', 'pro', 'enterprise'], true)) {
                    $chosenPlan = 'starter';
                }

                $tenant = Tenant::create([
                    'name' => $storeName,
                    'slug' => $slug,
                    'subdomain' => $slug,
                    'plan' => $chosenPlan,
                    'status' => 'pending',
                    'settings' => [
                        'branding' => [
                            'name' => $storeName,
                            'tagline' => 'Curated Collection • Powered by '.config('app.platform_name', 'ShopManager'),
                            'invoice_header' => $storeName,
                        ],
                        'features' => [
                            'storefront' => true,
                            'payroll' => true,
                        ],
                        'contact' => [
                            'phone' => $phone,
                            'whatsapp' => preg_replace('/\D+/', '', $phone),
                        ],
                    ],
                ]);

                $currentTenant->set($tenant);

                foreach (['admin', 'staff', 'customer'] as $roleName) {
                    Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                }

                foreach (['Clothes', 'Bags', 'Shoes', 'Accessories'] as $categoryName) {
                    Category::create([
                        'tenant_id' => $tenant->id,
                        'name' => $categoryName,
                        'slug' => Str::slug($categoryName),
                        'description' => "{$categoryName} collection for {$storeName}",
                    ]);
                }

                ShopLocation::create([
                    'tenant_id' => $tenant->id,
                    'name' => "{$storeName} — Main Store",
                    'slug' => "{$slug}-main",
                    'address' => 'Main Retail Branch',
                    'city' => 'Lagos',
                    'state' => 'Lagos State',
                    'country' => 'Nigeria',
                    'latitude' => 6.4474000,
                    'longitude' => 3.4723000,
                    'phone' => $phone,
                    'email' => $request->string('email')->toString(),
                    'opening_hours' => 'Mon - Sat: 9:00 AM - 6:00 PM',
                    'is_primary' => true,
                ]);

                $merchantUser = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $request->string('name')->toString(),
                    'email' => $request->string('email')->toString(),
                    'phone' => $phone,
                    'password' => Hash::make($request->string('password')->toString()),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]);

                $merchantUser->assignRole('admin');
                app(PermissionRegistrar::class)->forgetCachedPermissions();

                return $merchantUser;
            });

            if ($request->hasSession()) {
                $request->session()->put('active_tenant_id', $user->tenant_id);
            }

            event(new Registered($user));
            Auth::login($user);

            try {
                Mail::to('ruelnuffy@gmail.com')->send(new NewAccountRegisteredMail($user, $user->tenant));
            } catch (\Throwable $e) {
                Log::warning('Failed to send new account registration email', [
                    'user_id' => $user->id,
                    'tenant_id' => $user->tenant_id,
                    'error' => $e->getMessage(),
                ]);
            }

            $whatsappText = rawurlencode(
                "Hello ShopManager! I just registered my store \"{$user->tenant->name}\" ({$user->tenant->plan} plan). Name: {$user->name}, Email: {$user->email}, Phone: {$user->phone}. Please help me activate my plan."
            );

            return redirect(route('admin.dashboard', absolute: false))
                ->with('success', 'Welcome to '.config('app.platform_name', 'ShopManager')."! Your store ({$user->tenant->name}) is live and ready.")
                ->with('whatsapp_onboarding', [
                    'store_name' => $user->tenant->name,
                    'plan' => $user->tenant->plan,
                    'phone' => $user->phone,
                    'whatsapp_url' => "https://wa.me/2349046686176?text={$whatsappText}",
                ]);
        }

        // Tenant Storefront Customer Registration
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->where(fn ($query) => $query->where('tenant_id', $currentTenant->id())),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        if (Role::where('name', 'customer')->exists()) {
            $user->assignRole('customer');
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
