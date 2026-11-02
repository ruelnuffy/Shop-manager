<?php

namespace Tests;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected ?Tenant $defaultTenant = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureDefaultTenant();
    }

    protected function ensureDefaultTenant(): ?Tenant
    {
        try {
            $this->defaultTenant = Tenant::firstOrCreate(
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
                            'invoice_header' => "Jessi's ClassiQ Store — Flagship Boutique",
                        ],
                    ],
                    'paystack_public_key' => 'pk_test_placeholder',
                    'paystack_secret_key' => 'sk_test_placeholder',
                    'plan' => 'standard',
                    'status' => 'active',
                ]
            );

            if ($this->defaultTenant && empty($this->defaultTenant->paystack_secret_key)) {
                $this->defaultTenant->update([
                    'paystack_public_key' => 'pk_test_placeholder',
                    'paystack_secret_key' => 'sk_test_placeholder',
                ]);
            }

            app(CurrentTenant::class)->set($this->defaultTenant);

            if (function_exists('setPermissionsTeamId') && $this->defaultTenant) {
                setPermissionsTeamId($this->defaultTenant->id);
            }

            return $this->defaultTenant;
        } catch (\Throwable) {
            return null;
        }
    }
}
