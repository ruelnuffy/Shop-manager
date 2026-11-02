<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShopLocation;
use App\Models\StockMovement;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoStorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_catalog_page_displays_products_and_categories(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Curated Atelier Catalog');
        $response->assertSee('Filter by Category');

        $product = Product::latest('id')->first();
        $response->assertSee($product->name);
    }

    public function test_catalog_filters_by_category_and_search(): void
    {
        $category = Category::where('slug', 'clothes')->first();
        $response = $this->get('/products?category='.$category->slug);

        $response->assertStatus(200);
        $response->assertSee($category->name);

        $searchResponse = $this->get('/products?search=Silk');
        $searchResponse->assertStatus(200);
    }

    public function test_product_detail_page_loads_with_variants(): void
    {
        $product = Product::with('variants')->first();
        $response = $this->get('/products/'.$product->slug);

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Add to Shopping Bag');

        $firstVariant = $product->variants->first();
        $response->assertSee($firstVariant->sku);
    }

    public function test_cart_operations_add_update_remove_clear(): void
    {
        $variant = ProductVariant::first();
        $this->assertNotNull($variant);

        // 1. Add to cart
        $response = $this->post('/cart', [
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect('/cart');
        $this->assertEquals(2, session('cart')[$variant->id]['quantity']);

        // 2. View cart page
        $cartPage = $this->get('/cart');
        $cartPage->assertStatus(200);
        $cartPage->assertSee($variant->product->name);
        $cartPage->assertSee('₦');
        $cartPage->assertSee('<img', false);

        // 3. Update quantity
        $updateResponse = $this->patch('/cart/'.$variant->id, [
            'quantity' => 4,
        ]);
        $updateResponse->assertRedirect('/cart');
        $this->assertEquals(4, session('cart')[$variant->id]['quantity']);

        // 4. Remove single variant
        $deleteResponse = $this->delete('/cart/'.$variant->id);
        $deleteResponse->assertRedirect('/cart');
        $this->assertArrayNotHasKey($variant->id, session('cart', []));

        // 5. Add back and clear entire cart
        $this->post('/cart', ['variant_id' => $variant->id, 'quantity' => 1]);
        $this->assertNotEmpty(session('cart'));

        $clearResponse = $this->post('/cart/clear');
        $clearResponse->assertRedirect('/cart');
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_flow_and_paystack_simulation_decrements_stock_and_creates_order(): void
    {
        $variant = ProductVariant::where('stock_quantity', '>', 5)->first();
        $this->assertNotNull($variant);

        $initialStock = $variant->stock_quantity;
        $qtyToBuy = 2;

        // Add to cart
        $this->post('/cart', [
            'variant_id' => $variant->id,
            'quantity' => $qtyToBuy,
        ]);

        // Visit checkout page
        $checkoutView = $this->get('/checkout');
        $checkoutView->assertStatus(200);
        $checkoutView->assertSee('Delivery &amp; Contact Details', false);

        // Submit checkout form
        $checkoutResponse = $this->post('/checkout', [
            'shipping_name' => 'Adanna Okonkwo',
            'shipping_email' => 'adanna@example.com',
            'shipping_phone' => '+2348011223344',
            'shipping_address' => 'Plot 12, Adeola Odeku Street',
            'shipping_city' => 'Victoria Island, Lagos',
            'notes' => 'Ring gate bell on delivery.',
        ]);

        // Because keys are test placeholders, it redirects to Paystack simulation
        $checkoutResponse->assertRedirect();
        $redirectUrl = $checkoutResponse->headers->get('Location');
        $this->assertStringContainsString('/checkout/simulate-paystack?reference=', $redirectUrl);

        // Parse reference from URL
        $parsedUrl = parse_url($redirectUrl);
        parse_str($parsedUrl['query'], $queryParams);
        $reference = $queryParams['reference'];
        $this->assertNotEmpty($reference);

        // Visit simulation page
        $simView = $this->get('/checkout/simulate-paystack?reference='.$reference);
        $simView->assertStatus(200);
        $simView->assertSee('Paystack Sandbox');
        $simView->assertSee($reference);

        // Process callback verification
        $callbackResponse = $this->get('/checkout/callback?reference='.$reference);
        $callbackResponse->assertRedirect();
        $confirmationUrl = $callbackResponse->headers->get('Location');
        $this->assertStringContainsString('/orders/confirmation/', $confirmationUrl);

        // Verify Order in DB
        $order = Order::whereHas('payments', function ($q) use ($reference) {
            $q->where('paystack_reference', $reference);
        })->first();
        $this->assertNotNull($order);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertEquals('Adanna Okonkwo', $order->shipping_name);
        $this->assertEquals('Victoria Island, Lagos', $order->shipping_city);

        // Verify Order Items
        $this->assertCount(1, $order->items);
        $this->assertEquals($variant->id, $order->items->first()->product_variant_id);
        $this->assertEquals($qtyToBuy, $order->items->first()->quantity);

        // Verify Stock Decrement
        $variant->refresh();
        $this->assertEquals($initialStock - $qtyToBuy, $variant->stock_quantity);

        // Verify StockMovement audit row
        $movement = StockMovement::where('product_variant_id', $variant->id)
            ->where('type', 'out')
            ->where('reason', 'sale')
            ->where('reference_id', $order->id)
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals($qtyToBuy, $movement->quantity);

        // Verify Order Confirmation Page
        $confirmationPage = $this->get('/orders/confirmation/'.$order->order_number);
        $confirmationPage->assertStatus(200);
        $confirmationPage->assertSee($order->order_number);
        $confirmationPage->assertSee('ORDER CONFIRMED');
        $confirmationPage->assertSee('Adanna Okonkwo');
    }

    public function test_store_location_page_renders_with_osm_coordinates(): void
    {
        $response = $this->get('/store-location');

        $response->assertStatus(200);
        $response->assertSee('VISIT OUR BOUTIQUE');
        $response->assertSee('OpenStreetMap');

        $location = ShopLocation::where('is_primary', true)->first();
        $response->assertSee($location->name);
        $response->assertSee($location->address);
    }
}
