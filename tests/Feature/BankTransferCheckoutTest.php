<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankTransferCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_admin_requires_password_to_set_bank_account_and_tampering_invalidates_signature(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $admin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);
        app(CurrentTenant::class)->set($tenant);

        // Wrong password rejected
        $this->actingAs($admin)
            ->put(route('admin.storefront.bank-account'), [
                'bank_name' => 'GTBank',
                'bank_account_number' => '0123456789',
                'bank_account_name' => 'Decency Ventures',
                'current_password' => 'wrong-password',
            ])
            ->assertSessionHasErrors('current_password');

        // Correct password saves encrypted + HMAC-signed details
        $this->actingAs($admin)
            ->put(route('admin.storefront.bank-account'), [
                'bank_name' => 'GTBank',
                'bank_account_number' => '0123456789',
                'bank_account_name' => 'Decency Ventures',
                'current_password' => 'password',
            ])
            ->assertRedirect(route('admin.storefront.edit'));

        $tenant->refresh();
        $this->assertTrue($tenant->hasVerifiedBankDetails());
        $this->assertSame('0123456789', $tenant->bank_account_number);

        // Tampering with bank_account_number invalidates HMAC signature
        $tenant->update(['bank_account_number' => '9999999999']);
        $this->assertFalse($tenant->fresh()->hasVerifiedBankDetails());
    }

    public function test_bank_transfer_checkout_creates_order_and_links_to_store_whatsapp(): void
    {
        $tenant = Tenant::factory()->create([
            'status' => 'active',
            'settings' => [
                'contact' => ['whatsapp' => '08107584591'],
            ],
        ]);
        $tenant->update([
            'bank_name' => 'OPay',
            'bank_account_number' => '8107584591',
            'bank_account_name' => 'Decency Ventures',
            'bank_details_signature' => $tenant->computeBankDetailsSignature('OPay', '8107584591', 'Decency Ventures'),
        ]);
        app(CurrentTenant::class)->set($tenant);

        $category = Category::create([
            'tenant_id' => $tenant->id,
            'name' => 'Bags',
            'slug' => 'bags-'.$tenant->id,
        ]);
        $product = Product::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'name' => 'Leather Tote',
            'slug' => 'leather-tote-'.$tenant->id,
            'base_price' => 20000,
            'status' => 'active',
        ]);
        $variant = ProductVariant::create([
            'tenant_id' => $tenant->id,
            'product_id' => $product->id,
            'sku' => 'TOTE-1',
            'size' => 'Standard',
            'color' => 'Black',
            'price_override' => 20000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $cart = [
            $variant->id => [
                'variant_id' => $variant->id,
                'product_name' => $product->name,
                'size' => 'Standard',
                'color' => 'Black',
                'price' => 20000,
                'quantity' => 1,
                'image' => null,
            ],
        ];

        $response = $this->withSession(['cart' => $cart, 'active_tenant_id' => $tenant->id])
            ->post(route('checkout.process'), [
                'shipping_name' => 'Ada Buyer',
                'shipping_email' => 'ada@example.com',
                'shipping_phone' => '08012345678',
                'shipping_address' => '12 Main Street',
                'shipping_city' => 'Lagos',
                'payment_method' => 'bank_transfer',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'tenant_id' => $tenant->id,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
        ]);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('Confirm Your Transfer on WhatsApp')
            ->assertSee('https://wa.me/2348107584591');
    }
}
