<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->company().' Boutique';
        $slug = Str::slug($name).'-'.fake()->unique()->numberBetween(100, 9999);

        return [
            'name' => $name,
            'slug' => $slug,
            'subdomain' => 'store-'.fake()->unique()->numberBetween(100, 9999),
            'domain' => null,
            'settings' => [
                'currency' => 'NGN',
                'currency_symbol' => '₦',
            ],
            'paystack_public_key' => 'pk_test_'.Str::random(24),
            'paystack_secret_key' => 'sk_test_'.Str::random(24),
            'plan' => 'standard',
            'status' => 'active',
        ];
    }
}
