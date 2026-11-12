<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    /**
     * Show the storefront template customization form for the current store admin.
     */
    public function edit(): View
    {
        $tenant = $this->resolveCurrentTenant();

        if (! $tenant) {
            abort(404, 'Store tenant could not be resolved.');
        }

        $branding = $tenant->setting('branding', []);
        $storefront = $tenant->setting('storefront', []);

        return view('admin.storefront.edit', compact('tenant', 'branding', 'storefront'));
    }

    /**
     * Update the storefront template hero images, featuring images, and branding.
     */
    public function update(Request $request): RedirectResponse
    {
        $tenant = $this->resolveCurrentTenant();

        if (! $tenant) {
            abort(404, 'Store tenant could not be resolved.');
        }

        $validated = $request->validate([
            'brand_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:50',
            'secondary_color' => 'nullable|string|max:50',
            'accent_color' => 'nullable|string|max:50',

            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'logo_url' => 'nullable|string|max:1000',

            // Hero section customization
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_badge' => 'nullable|string|max:100',
            'hero_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'hero_image_url' => 'nullable|string|max:1000',

            // Featuring card 1
            'featuring_1_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'featuring_1_image_url' => 'nullable|string|max:1000',
            'featuring_1_title' => 'nullable|string|max:150',
            'featuring_1_subtitle' => 'nullable|string|max:255',
            'featuring_1_badge' => 'nullable|string|max:50',
            'featuring_1_link' => 'nullable|string|max:500',

            // Featuring card 2
            'featuring_2_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'featuring_2_image_url' => 'nullable|string|max:1000',
            'featuring_2_title' => 'nullable|string|max:150',
            'featuring_2_subtitle' => 'nullable|string|max:255',
            'featuring_2_badge' => 'nullable|string|max:50',
            'featuring_2_link' => 'nullable|string|max:500',

            // Featuring card 3
            'featuring_3_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'featuring_3_image_url' => 'nullable|string|max:1000',
            'featuring_3_title' => 'nullable|string|max:150',
            'featuring_3_subtitle' => 'nullable|string|max:255',
            'featuring_3_badge' => 'nullable|string|max:50',
            'featuring_3_link' => 'nullable|string|max:500',
        ]);

        $settings = $tenant->settings ?? [];
        $tenantId = $tenant->id;

        // 1. Branding updates
        if ($request->filled('brand_name')) {
            data_set($settings, 'branding.name', $request->input('brand_name'));
        }
        if ($request->has('tagline')) {
            data_set($settings, 'branding.tagline', $request->input('tagline'));
        }
        if ($request->filled('primary_color')) {
            data_set($settings, 'branding.palette.primary', $request->input('primary_color'));
        }
        if ($request->filled('secondary_color')) {
            data_set($settings, 'branding.palette.secondary', $request->input('secondary_color'));
        }
        if ($request->filled('accent_color')) {
            data_set($settings, 'branding.palette.accent', $request->input('accent_color'));
        }

        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store("tenants/{$tenantId}/branding", 'public');
            data_set($settings, 'branding.logo_url', '/storage/'.$path);
        } elseif ($request->filled('logo_url')) {
            data_set($settings, 'branding.logo_url', $request->input('logo_url'));
        }

        // 2. Hero Section
        if ($request->has('hero_title')) {
            data_set($settings, 'storefront.hero_title', $request->input('hero_title'));
        }
        if ($request->has('hero_subtitle')) {
            data_set($settings, 'storefront.hero_subtitle', $request->input('hero_subtitle'));
        }
        if ($request->has('hero_badge')) {
            data_set($settings, 'storefront.hero_badge', $request->input('hero_badge'));
        }

        if ($request->hasFile('hero_image_file')) {
            $path = $request->file('hero_image_file')->store("tenants/{$tenantId}/storefront", 'public');
            data_set($settings, 'storefront.hero_image', '/storage/'.$path);
        } elseif ($request->filled('hero_image_url')) {
            data_set($settings, 'storefront.hero_image', $request->input('hero_image_url'));
        }

        // 3. Featuring Cards (1 to 3)
        $featuringList = data_get($settings, 'storefront.featuring', []);

        for ($i = 1; $i <= 3; $i++) {
            $fileKey = "featuring_{$i}_image_file";
            $urlKey = "featuring_{$i}_image_url";
            $titleKey = "featuring_{$i}_title";
            $subKey = "featuring_{$i}_subtitle";
            $badgeKey = "featuring_{$i}_badge";
            $linkKey = "featuring_{$i}_link";

            $existingCard = $featuringList[$i - 1] ?? [];

            $imageUrl = $existingCard['image'] ?? null;
            if ($request->hasFile($fileKey)) {
                $path = $request->file($fileKey)->store("tenants/{$tenantId}/storefront", 'public');
                $imageUrl = '/storage/'.$path;
            } elseif ($request->filled($urlKey)) {
                $imageUrl = $request->input($urlKey);
            }

            $featuringList[$i - 1] = [
                'image' => $imageUrl,
                'title' => $request->input($titleKey, $existingCard['title'] ?? ''),
                'subtitle' => $request->input($subKey, $existingCard['subtitle'] ?? ''),
                'badge' => $request->input($badgeKey, $existingCard['badge'] ?? ''),
                'link' => $request->input($linkKey, $existingCard['link'] ?? route('products.index')),
            ];
        }

        data_set($settings, 'storefront.featuring', $featuringList);

        $tenant->settings = $settings;
        $tenant->save();

        app(CurrentTenant::class)->set($tenant);

        return redirect()->route('admin.storefront.edit')
            ->with('status', 'Storefront hero and featuring images updated successfully!');
    }

    /**
     * Resolve the active tenant model for the current authenticated user.
     */
    protected function resolveCurrentTenant(): ?Tenant
    {
        $user = Auth::user();

        if ($user && $user->tenant_id) {
            return Tenant::find($user->tenant_id);
        }

        return app(CurrentTenant::class)->get();
    }
}
