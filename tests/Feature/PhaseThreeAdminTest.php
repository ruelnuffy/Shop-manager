<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseThreeAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@jessiclassiq.com')->first();
        $this->staff = User::where('email', 'staff@jessiclassiq.com')->first();
        $this->customer = User::where('email', 'customer@jessiclassiq.com')->first();
    }

    public function test_admin_portal_forbidden_for_guests_and_customers(): void
    {
        // Guest is redirected to login
        $guestResponse = $this->get('/admin');
        $guestResponse->assertRedirect('/login');

        // Customer gets 403 Forbidden
        $customerResponse = $this->actingAs($this->customer)->get('/admin');
        $customerResponse->assertStatus(403);

        $customerStockResponse = $this->actingAs($this->customer)->get('/admin/stock');
        $customerStockResponse->assertStatus(403);

        $customerReportsResponse = $this->actingAs($this->customer)->get('/admin/reports');
        $customerReportsResponse->assertStatus(403);
    }

    public function test_admin_and_staff_can_access_dashboard_and_kpis(): void
    {
        $adminResponse = $this->actingAs($this->admin)->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Atelier Overview');
        $adminResponse->assertSee("Today's Revenue", false);
        $adminResponse->assertSee('Inventory Valuation');

        $staffResponse = $this->actingAs($this->staff)->get('/admin');
        $staffResponse->assertStatus(200);
        $staffResponse->assertSee('Atelier Overview');
    }

    public function test_admin_can_view_products_and_create_product_with_variants(): void
    {
        $productsView = $this->actingAs($this->admin)->get('/admin/products');
        $productsView->assertStatus(200);
        $productsView->assertSee('Curated Catalog &amp; Collections', false);

        $category = Category::first();

        $createResponse = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Rose Velvet Blazer',
            'category_id' => $category->id,
            'description' => 'Tailored double-breasted velvet blazer in rich rose.',
            'base_price' => 125000,
            'is_active' => '1',
            'is_featured' => '1',
            'variants' => [
                [
                    'sku' => 'BLZ-VEL-ROSE-S',
                    'size' => 'S',
                    'color' => 'Rose',
                    'stock_quantity' => 15,
                    'price_override' => null,
                ],
                [
                    'sku' => 'BLZ-VEL-ROSE-M',
                    'size' => 'M',
                    'color' => 'Rose',
                    'stock_quantity' => 20,
                    'price_override' => null,
                ],
            ],
        ]);

        $createResponse->assertRedirect('/admin/products');

        $createdProduct = Product::where('name', 'Rose Velvet Blazer')->first();
        $this->assertNotNull($createdProduct);
        $this->assertEquals(2, $createdProduct->variants()->count());
        $this->assertEquals(35, $createdProduct->totalStock());
    }

    public function test_admin_stock_adjustment_updates_quantity_and_logs_movement(): void
    {
        $variant = ProductVariant::first();
        $initialStock = $variant->stock_quantity;
        $restockQty = 12;

        $response = $this->actingAs($this->admin)->post('/admin/stock/adjust', [
            'variant_id' => $variant->id,
            'type' => 'in',
            'quantity' => $restockQty,
            'reason' => 'Shipment from Paris runway workshop',
        ]);

        $response->assertRedirect('/admin/stock');
        $variant->refresh();
        $this->assertEquals($initialStock + $restockQty, $variant->stock_quantity);

        $movement = StockMovement::where('product_variant_id', $variant->id)
            ->where('type', 'in')
            ->latest('id')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals($restockQty, $movement->quantity);
        $this->assertEquals($this->admin->id, $movement->created_by);
    }

    public function test_admin_can_view_orders_and_update_fulfillment_status(): void
    {
        $ordersView = $this->actingAs($this->admin)->get('/admin/orders');
        $ordersView->assertStatus(200);

        // Create a test order
        $order = Order::create([
            'order_number' => 'ORD-TEST-FULFILL',
            'customer_id' => $this->customer->id,
            'shipping_name' => 'Zainab Bello',
            'shipping_email' => 'zainab@example.com',
            'shipping_phone' => '+2348099887766',
            'shipping_address' => '5 Admiralty Way',
            'shipping_city' => 'Lekki Phase 1, Lagos',
            'subtotal' => 85000,
            'shipping_fee' => 3500,
            'total_amount' => 88500,
            'payment_method' => 'paystack',
            'payment_status' => 'paid',
            'payment_reference' => 'ref_fulfillment_test',
            'status' => 'processing',
        ]);

        $detailView = $this->actingAs($this->admin)->get('/admin/orders/'.$order->id);
        $detailView->assertStatus(200);
        $detailView->assertSee('ORD-TEST-FULFILL');
        $detailView->assertSee('Zainab Bello');

        $updateResponse = $this->actingAs($this->admin)->patch('/admin/orders/'.$order->id.'/status', [
            'status' => 'shipped',
        ]);

        $updateResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('shipped', $order->status);
    }

    public function test_admin_manual_pos_sale_decrements_stock_and_creates_sales_record(): void
    {
        $variant = ProductVariant::where('stock_quantity', '>', 5)->first();
        $initialStock = $variant->stock_quantity;
        $saleQty = 2;
        $saleAmount = 90000;

        $createSaleView = $this->actingAs($this->admin)->get('/admin/sales/create');
        $createSaleView->assertStatus(200);
        $createSaleView->assertSee('Direct In-Store POS Sale');

        $response = $this->actingAs($this->admin)->post('/admin/sales', [
            'variant_id' => $variant->id,
            'quantity' => $saleQty,
            'amount' => $saleAmount,
            'payment_method' => 'pos_terminal',
            'customer_name' => 'Lady Ifeoma',
            'notes' => 'Purchased at Victoria Island boutique fitting room',
        ]);

        $response->assertRedirect('/admin/sales/create');

        // Check SalesRecord
        $salesRecord = SalesRecord::where('product_variant_id', $variant->id)
            ->where('source', 'manual')
            ->latest()
            ->first();

        $this->assertNotNull($salesRecord);
        $this->assertEquals($saleAmount, $salesRecord->amount);
        $this->assertEquals($saleQty, $salesRecord->quantity);
        $this->assertEquals('confirmed', $salesRecord->status);
        $this->assertEquals($this->admin->id, $salesRecord->created_by);

        // Check stock decrement
        $variant->refresh();
        $this->assertEquals($initialStock - $saleQty, $variant->stock_quantity);

        // Check StockMovement
        $movement = StockMovement::where('product_variant_id', $variant->id)
            ->where('type', 'out')
            ->where('reference_type', 'SalesRecord')
            ->latest()
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals($saleQty, $movement->quantity);
    }

    public function test_pos_charge_validates_cashier_password(): void
    {
        $variant = ProductVariant::first();
        $this->assertNotNull($variant);

        // Incorrect password should fail with error flash
        $responseFail = $this->actingAs($this->admin)->from('/admin/sales/create')->post('/admin/sales', [
            'variant_id' => $variant->id,
            'quantity' => 1,
            'amount' => 10000,
            'payment_method' => 'cash',
            'password' => 'wrong-pass',
        ]);
        $responseFail->assertRedirect('/admin/sales/create');
        $responseFail->assertSessionHas('error');

        // Correct user password should succeed
        $responseSuccess = $this->actingAs($this->admin)->from('/admin/sales/create')->post('/admin/sales', [
            'variant_id' => $variant->id,
            'quantity' => 1,
            'amount' => 10000,
            'payment_method' => 'cash',
            'password' => 'password123',
        ]);
        // Split payment with customer phone should record properly
        $responseSplit = $this->actingAs($this->admin)->from('/admin/sales/create')->post('/admin/sales', [
            'variant_id' => $variant->id,
            'quantity' => 1,
            'amount' => 25000,
            'payment_method' => 'split',
            'split_cash_amount' => 10000,
            'split_transfer_amount' => 15000,
            'customer_name' => 'Chioma Ade',
            'customer_phone' => '08099887766',
            'password' => 'password123',
        ]);
        $responseSplit->assertRedirect('/admin/sales/create');
        $responseSplit->assertSessionHas('success');

        $splitRecord = SalesRecord::where('source', 'manual')->latest('id')->first();
        $this->assertStringContainsString('SPLIT (Cash: ₦10,000, Transfer: ₦15,000)', $splitRecord->notes);
        $this->assertStringContainsString('Chioma Ade (08099887766)', $splitRecord->notes);
    }

    public function test_admin_reports_and_csv_export(): void
    {
        $reportsView = $this->actingAs($this->admin)->get('/admin/reports');
        $reportsView->assertStatus(200);
        $reportsView->assertSee('Financial &amp; Sales Reports', false);
        $reportsView->assertSee('Total Net Sales');
        $reportsView->assertSee('Inventory Valuation');

        $csvExport = $this->actingAs($this->admin)->get('/admin/reports/export');
        $csvExport->assertStatus(200);
        $this->assertMatchesRegularExpression('/^text\/csv;\s*charset=utf-8$/i', $csvExport->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename="jessi_sales_report_', $csvExport->headers->get('Content-Disposition'));
    }

    public function test_staff_cannot_create_edit_update_or_delete_products(): void
    {
        $product = Product::firstOrFail();

        // 1. Staff can view the products catalog in read-only mode
        $indexResponse = $this->actingAs($this->staff)->get('/admin/products');
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('+ Add New Product');
        $indexResponse->assertSee('Read Only');

        // 2. Staff cannot access the create product page
        $createResponse = $this->actingAs($this->staff)->get('/admin/products/create');
        $createResponse->assertStatus(403);

        // 3. Staff cannot store a new product
        $storeResponse = $this->actingAs($this->staff)->post('/admin/products', [
            'name' => 'Illegal Staff Product',
            'category_id' => $product->category_id,
            'base_price' => 5000,
        ]);
        $storeResponse->assertStatus(403);

        // 4. Staff cannot access the edit product page
        $editResponse = $this->actingAs($this->staff)->get("/admin/products/{$product->id}/edit");
        $editResponse->assertStatus(403);

        // 5. Staff cannot update a product
        $updateResponse = $this->actingAs($this->staff)->put("/admin/products/{$product->id}", [
            'name' => 'Hacked Name',
            'category_id' => $product->category_id,
            'base_price' => 1000,
            'status' => 'active',
        ]);
        $updateResponse->assertStatus(403);

        // 6. Staff cannot delete a product
        $deleteResponse = $this->actingAs($this->staff)->delete("/admin/products/{$product->id}");
        $deleteResponse->assertStatus(403);
    }

    public function test_staff_cannot_adjust_stock(): void
    {
        $variant = ProductVariant::firstOrFail();
        $originalStock = $variant->stock_quantity;

        // 1. Staff can view stock audit log in read-only mode
        $stockIndexResponse = $this->actingAs($this->staff)->get('/admin/stock');
        $stockIndexResponse->assertStatus(200);
        $stockIndexResponse->assertSee('Read Only');
        $stockIndexResponse->assertDontSee('Record Stock Adjustment');

        // 2. Staff cannot submit stock adjustments
        $adjustResponse = $this->actingAs($this->staff)->post('/admin/stock/adjust', [
            'variant_id' => $variant->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'restock',
        ]);
        $adjustResponse->assertStatus(403);

        // Verify stock quantity did not change
        $this->assertEquals($originalStock, $variant->fresh()->stock_quantity);
    }

    public function test_admin_can_view_stores_and_subscription_tiers_on_dashboard_and_directory(): void
    {
        // 1. Admin sees store admins and subscription breakdown on /admin dashboard
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Store Admin Accounts');
        $response->assertSee('Subscribed Users');
        $response->assertSee('Free');

        // 2. Admin can visit dedicated /admin/stores directory
        $directoryResponse = $this->actingAs($this->admin)->get('/admin/stores');
        $directoryResponse->assertStatus(200);
        $directoryResponse->assertSee('Store Admin Accounts');
        $directoryResponse->assertSee('Subscribed Users');
        $directoryResponse->assertSee('Free &amp; Trial Users', false);

        // 3. Staff cannot access stores directory
        $staffResponse = $this->actingAs($this->staff)->get('/admin/stores');
        $staffResponse->assertStatus(403);
    }
}
