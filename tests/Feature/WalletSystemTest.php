<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_wallet_balance_and_statement_on_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'wallet_balance' => 50000,
        ]);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 50000,
            'balance_after' => 50000,
            'description' => 'Initial VIP deposit',
            'reference' => 'TX-TEST-001',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('50,000');
        $response->assertSee('Initial VIP deposit');
        $response->assertSee('TX-TEST-001');
        $response->assertSee('Top Up Boutique Wallet');
    }

    public function test_customer_can_test_top_up_wallet(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'wallet_balance' => 10000,
        ]);

        $response = $this->actingAs($user)->post(route('wallet.test-topup'), [
            'amount' => 25000,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals(35000, $user->fresh()->wallet_balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 25000,
            'balance_after' => 35000,
            'status' => 'completed',
        ]);
    }

    public function test_customer_can_fund_wallet_via_paystack(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'wallet_balance' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('wallet.fund'), [
            'amount' => 50000,
        ]);

        // PaystackService intercepts in test mode and redirects to simulated page
        $response->assertRedirect();
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 50000,
            'status' => 'pending',
        ]);
    }

    public function test_customer_can_checkout_using_wallet_balance(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'wallet_balance' => 100000,
        ]);

        $category = Category::create([
            'name' => 'Silk Dresses',
            'slug' => 'silk-dresses',
        ]);

        $product = Product::create([
            'name' => 'Royal Satin Midi Dress',
            'slug' => 'royal-satin-midi-dress',
            'description' => 'Pure silk midi dress.',
            'base_price' => 35000,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Champagne',
            'size' => 'M',
            'sku' => 'SILK-CHAMP-M',
            'price' => 35000,
            'stock_quantity' => 5,
        ]);

        // Put item in cart session
        $cart = [
            $variant->id => [
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'name' => $product->name,
                'variant_name' => 'Champagne - M',
                'size' => 'M',
                'color' => 'Champagne',
                'price' => 35000,
                'quantity' => 1,
                'image' => null,
            ],
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.process'), [
                'shipping_name' => 'VIP Duchess',
                'shipping_email' => 'vip@jessiclassiq.com',
                'shipping_phone' => '08012345678',
                'shipping_address' => '10 Platinum Crescent',
                'shipping_city' => 'Jos',
                'shipping_state' => 'Plateau',
                'payment_method' => 'wallet',
            ]);

        $response->assertRedirect();
        // 35,000 subtotal + 3,500 shipping = 38,500 total
        $this->assertEquals(61500, $user->fresh()->wallet_balance);
        $this->assertEquals(4, $variant->fresh()->stock_quantity);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'wallet',
            'payment_status' => 'paid',
            'total_amount' => 38500,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => 38500,
            'balance_after' => 61500,
            'status' => 'completed',
        ]);
    }

    public function test_customer_cannot_checkout_using_wallet_with_insufficient_funds(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'wallet_balance' => 5000,
        ]);

        $category = Category::create([
            'name' => 'Italian Bags',
            'slug' => 'italian-bags',
        ]);

        $product = Product::create([
            'name' => 'Crocodile Leather Tote',
            'slug' => 'crocodile-leather-tote',
            'description' => 'Luxury handcrafted leather bag.',
            'base_price' => 85000,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Burgundy',
            'size' => 'Standard',
            'sku' => 'BAG-BUR-01',
            'price' => 85000,
            'stock_quantity' => 3,
        ]);

        $cart = [
            $variant->id => [
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'name' => $product->name,
                'variant_name' => 'Burgundy - Standard',
                'size' => 'Standard',
                'color' => 'Burgundy',
                'price' => 85000,
                'quantity' => 1,
                'image' => null,
            ],
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->from(route('checkout.index'))
            ->post(route('checkout.process'), [
                'shipping_name' => 'VIP Duchess',
                'shipping_email' => 'vip@jessiclassiq.com',
                'shipping_phone' => '08012345678',
                'shipping_address' => '10 Platinum Crescent',
                'shipping_city' => 'Jos',
                'shipping_state' => 'Plateau',
                'payment_method' => 'wallet',
            ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHas('error');
        $this->assertEquals(5000, $user->fresh()->wallet_balance);
        $this->assertEquals(3, $variant->fresh()->stock_quantity);
    }
}
