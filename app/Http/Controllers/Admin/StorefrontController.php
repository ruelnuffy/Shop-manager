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
        $contact = $tenant->setting('contact', []);

        return view('admin.storefront.edit', compact('tenant', 'branding', 'storefront', 'contact'));
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

            // Customer care, chats & WhatsApp concierge
            'whatsapp_number' => 'nullable|string|max:50',
            'whatsapp_message' => 'nullable|string|max:500',
            'customer_care_phone' => 'nullable|string|max:50',
            'customer_care_email' => 'nullable|string|max:255',
            'chat_label' => 'nullable|string|max:100',
            'chat_enabled' => 'nullable',
            'address' => 'nullable|string|max:500',
            'opening_hours' => 'nullable|string|max:255',

            // Store badges & lead byline
            'season_badge' => 'nullable|string|max:100',
            'location_badge' => 'nullable|string|max:150',
            'lead_name' => 'nullable|string|max:100',
            'lead_role' => 'nullable|string|max:50',
            'lead_status' => 'nullable|string|max:100',
            'about_text' => 'nullable|string|max:2000',

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

        // 2. Customer Care, WhatsApp & Concierge Chat
        if ($request->has('whatsapp_number')) {
            data_set($settings, 'contact.whatsapp', $request->input('whatsapp_number'));
        }
        if ($request->has('whatsapp_message')) {
            data_set($settings, 'contact.whatsapp_message', $request->input('whatsapp_message'));
        }
        if ($request->has('customer_care_phone')) {
            data_set($settings, 'contact.phone', $request->input('customer_care_phone'));
        }
        if ($request->has('customer_care_email')) {
            data_set($settings, 'contact.email', $request->input('customer_care_email'));
        }
        if ($request->has('chat_label')) {
            data_set($settings, 'contact.chat_label', $request->input('chat_label'));
        }
        data_set($settings, 'contact.chat_enabled', $request->boolean('chat_enabled', true));

        if ($request->has('address')) {
            data_set($settings, 'contact.address', $request->input('address'));
        }
        if ($request->has('opening_hours')) {
            data_set($settings, 'contact.opening_hours', $request->input('opening_hours'));
        }

        // 3. Editorial Badges & Store Byline
        if ($request->has('season_badge')) {
            data_set($settings, 'storefront.season_badge', $request->input('season_badge'));
        }
        if ($request->has('location_badge')) {
            data_set($settings, 'storefront.location_badge', $request->input('location_badge'));
        }
        if ($request->has('lead_name')) {
            data_set($settings, 'storefront.lead_name', $request->input('lead_name'));
        }
        if ($request->has('lead_role')) {
            data_set($settings, 'storefront.lead_role', $request->input('lead_role'));
        }
        if ($request->has('lead_status')) {
            data_set($settings, 'storefront.lead_status', $request->input('lead_status'));
        }
        if ($request->has('about_text')) {
            data_set($settings, 'storefront.about_text', $request->input('about_text'));
        }

        // 4. Hero Section
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
