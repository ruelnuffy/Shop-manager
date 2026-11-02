<?php

use App\Models\ShopLocation;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tenantSlug = config('tenancy.local_default_tenant_slug', 'jessis-classiq-store');
        $tenant = Tenant::where('slug', $tenantSlug)->first();

        if ($tenant) {
            $existing = $tenant->settings ?? [];

            $branding = [
                'name' => "Jessi's ClassiQ Store",
                'tagline' => 'Classy Looks For Every You • Curated Luxury Boutique',
                'logo_url' => 'images/logo.png',
                'palette' => [
                    'primary' => '#c5a880',
                    'secondary' => '#1a1a1a',
                    'accent' => '#e8d8c8',
                ],
                'invoice_header' => "Jessi's ClassiQ Store — Flagship Boutique",
            ];

            $features = [
                'storefront' => true,
                'payroll' => true,
            ];

            $tenant->settings = array_merge($existing, [
                'branding' => array_merge($existing['branding'] ?? [], $branding),
                'features' => array_merge($existing['features'] ?? [], $features),
            ]);

            if (empty($tenant->paystack_secret_key)) {
                $tenant->paystack_secret_key = 'sk_test_placeholder';
            }
            if (empty($tenant->paystack_public_key)) {
                $tenant->paystack_public_key = 'pk_test_placeholder';
            }

            $tenant->save();

            // Move Lagos location into ShopLocation row for this tenant
            ShopLocation::withoutGlobalScopes()->updateOrCreate(
                ['slug' => 'jessi-boutique-lagos'],
                [
                    'tenant_id' => $tenant->id,
                    'name' => "Jessi's ClassiQ Store — Lagos Showroom",
                    'address' => 'Plot 14, Admiralty Way, Lekki Phase 1',
                    'city' => 'Lagos',
                    'state' => 'Lagos State',
                    'country' => 'Nigeria',
                    'latitude' => 6.4474000,
                    'longitude' => 3.4723000,
                    'phone' => '+234 801 234 5679',
                    'email' => 'lagos@jessiclassiq.com',
                    'opening_hours' => 'Mon - Sat: 10:00 AM - 7:00 PM',
                    'is_primary' => false,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tenantSlug = config('tenancy.local_default_tenant_slug', 'jessis-classiq-store');
        $tenant = Tenant::where('slug', $tenantSlug)->first();

        if ($tenant) {
            ShopLocation::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('slug', 'jessi-boutique-lagos')
                ->delete();

            $existing = $tenant->settings ?? [];
            unset($existing['branding']);
            unset($existing['features']);

            $tenant->settings = $existing;
            $tenant->save();
        }
    }
};
