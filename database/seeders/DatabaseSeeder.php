<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'jessis-classiq-store'],
            [
                'name' => "Jessi's ClassiQ Store",
                'subdomain' => 'jessiclassiq',
                'domain' => null,
                'settings' => [
                    'currency' => 'NGN',
                    'currency_symbol' => '₦',
                    'features' => [
                        'storefront' => true,
                        'payroll' => true,
                    ],
                    'branding' => [
                        'name' => "Jessi's ClassiQ Store",
                        'tagline' => 'Classy Looks For Every You • Curated Luxury Boutique',
                        'logo_url' => 'images/logo.png',
                        'palette' => [
                            'primary' => '#c5a880',
                            'secondary' => '#1a1a1a',
                            'accent' => '#e8d8c8',
                        ],
                        'invoice_header' => "Jessi's ClassiQ Store — Flagship Boutique",
                    ],
                ],
                'paystack_public_key' => 'pk_test_placeholder',
                'paystack_secret_key' => 'sk_test_placeholder',
                'plan' => 'standard',
                'status' => 'active',
            ]
        );

        if ($tenant && empty($tenant->paystack_secret_key)) {
            $tenant->update([
                'paystack_public_key' => 'pk_test_placeholder',
                'paystack_secret_key' => 'sk_test_placeholder',
            ]);
        }

        app(CurrentTenant::class)->set($tenant);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($tenant->id);
        }

        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            StoreDataSeeder::class,
            TestStoreDataSeeder::class,
        ]);
    }
}
