<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;

    protected Tenant $tenantB;

    protected User $adminA;

    protected User $adminB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        // Create Tenant A
        $this->tenantA = Tenant::create([
            'name' => 'Boutique Alpha',
            'slug' => 'boutique-alpha',
            'subdomain' => 'alpha',
            'domain' => 'alpha.test',
            'plan' => 'standard',
            'status' => 'active',
        ]);

        // Create Tenant B
        $this->tenantB = Tenant::create([
            'name' => 'Boutique Beta',
            'slug' => 'boutique-beta',
            'subdomain' => 'beta',
            'domain' => 'beta.test',
            'plan' => 'standard',
            'status' => 'active',
        ]);

        // Create Admin for Tenant A
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);
        $this->adminA = User::create([
            'name' => 'Admin Alpha',
            'email' => 'admin@alpha.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tenant_id' => $this->tenantA->id,
            'email_verified_at' => now(),
        ]);
        $this->adminA->assignRole('admin');

        // Create Admin for Tenant B
        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);
        $this->adminB = User::create([
            'name' => 'Admin Beta',
            'email' => 'admin@beta.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'tenant_id' => $this->tenantB->id,
            'email_verified_at' => now(),
        ]);
        $this->adminB->assignRole('admin');
    }

    public function test_tenant_a_cannot_see_tenant_b_products(): void
    {
        // Create Category & Product in Tenant A
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);

        $catA = Category::create([
            'name' => 'Bags Alpha',
            'slug' => 'bags-alpha',
            'tenant_id' => $this->tenantA->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $prodA = Product::create([
            'category_id' => $catA->id,
            'name' => 'Silk Handbag Alpha',
            'slug' => 'silk-handbag-alpha',
            'base_price' => 25000.00,
            'tenant_id' => $this->tenantA->id,
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $prodA->id,
            'size' => 'Standard',
            'color' => 'Gold',
            'sku' => 'SKU-ALPHA-01',
            'stock_quantity' => 10,
            'tenant_id' => $this->tenantA->id,
            'is_active' => true,
        ]);

        // Create Category & Product in Tenant B
        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);

        $catB = Category::create([
            'name' => 'Shoes Beta',
            'slug' => 'shoes-beta',
            'tenant_id' => $this->tenantB->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $prodB = Product::create([
            'category_id' => $catB->id,
            'name' => 'Stiletto Heels Beta',
            'slug' => 'stiletto-heels-beta',
            'base_price' => 45000.00,
            'tenant_id' => $this->tenantB->id,
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $prodB->id,
            'size' => '39',
            'color' => 'Ruby Red',
            'sku' => 'SKU-BETA-01',
            'stock_quantity' => 5,
            'tenant_id' => $this->tenantB->id,
            'is_active' => true,
        ]);

        // Request Tenant A storefront
        $responseA = $this->withHeaders(['X-Tenant' => 'alpha'])
            ->get('/products');

        $responseA->assertStatus(200);
        $responseA->assertSee('Silk Handbag Alpha');
        $responseA->assertDontSee('Stiletto Heels Beta');

        // Request Tenant B product slug from Tenant A returns 404
        $detailResponse = $this->withHeaders(['X-Tenant' => 'alpha'])
            ->get('/products/stiletto-heels-beta');
        $detailResponse->assertStatus(404);

        // Request Tenant B storefront
        $responseB = $this->withHeaders(['X-Tenant' => 'beta'])
            ->get('/products');

        $responseB->assertStatus(200);
        $responseB->assertSee('Stiletto Heels Beta');
        $responseB->assertDontSee('Silk Handbag Alpha');
    }

    public function test_tenant_a_cannot_see_tenant_b_orders(): void
    {
        // Tenant A Order
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);

        $orderA = Order::create([
            'tenant_id' => $this->tenantA->id,
            'order_number' => 'ORD-ALPHA-1001',
            'status' => 'pending',
            'subtotal' => 20000.00,
            'shipping_fee' => 2000.00,
            'tax' => 0.00,
            'total_amount' => 22000.00,
            'shipping_name' => 'Customer Alpha',
            'shipping_email' => 'client.alpha@test.com',
            'shipping_phone' => '+2348011112222',
            'shipping_address' => '12 Alpha Way',
            'shipping_city' => 'Lagos',
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        // Tenant B Order
        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);

        $orderB = Order::create([
            'tenant_id' => $this->tenantB->id,
            'order_number' => 'ORD-BETA-2002',
            'status' => 'pending',
            'subtotal' => 50000.00,
            'shipping_fee' => 3000.00,
            'tax' => 0.00,
            'total_amount' => 53000.00,
            'shipping_name' => 'Customer Beta',
            'shipping_email' => 'client.beta@test.com',
            'shipping_phone' => '+2348033334444',
            'shipping_address' => '45 Beta Road',
            'shipping_city' => 'Abuja',
            'payment_status' => 'paid',
            'payment_method' => 'card',
        ]);

        // Admin A visits Tenant A admin orders
        $response = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'alpha'])
            ->get('/admin/orders');

        $response->assertStatus(200);
        $response->assertSee('ORD-ALPHA-1001');
        $response->assertDontSee('ORD-BETA-2002');

        // Admin A tries to view Tenant B order details on Tenant A
        $orderDetailResponse = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'alpha'])
            ->get("/admin/orders/{$orderB->id}");

        $orderDetailResponse->assertStatus(404);
    }

    public function test_tenant_a_cannot_see_tenant_b_staff(): void
    {
        // Tenant A Staff
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);

        $staffUserA = User::create([
            'tenant_id' => $this->tenantA->id,
            'name' => 'Fatima Staff Alpha',
            'email' => 'fatima@alpha.test',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);
        $staffUserA->assignRole('staff');

        $staffA = Staff::create([
            'tenant_id' => $this->tenantA->id,
            'user_id' => $staffUserA->id,
            'position' => 'Sales Lead Alpha',
            'bank_name' => 'First Bank',
            'bank_account_number' => '1234567890',
            'bank_code' => '011',
            'recipient_code' => 'RCP_alpha',
            'monthly_salary' => 120000.00,
            'is_active' => true,
        ]);

        // Tenant B Staff
        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);

        $staffUserB = User::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Zainab Staff Beta',
            'email' => 'zainab@beta.test',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);
        $staffUserB->assignRole('staff');

        $staffB = Staff::create([
            'tenant_id' => $this->tenantB->id,
            'user_id' => $staffUserB->id,
            'position' => 'Store Manager Beta',
            'bank_name' => 'Zenith Bank',
            'bank_account_number' => '0987654321',
            'bank_code' => '057',
            'recipient_code' => 'RCP_beta',
            'monthly_salary' => 180000.00,
            'is_active' => true,
        ]);

        // Admin A lists staff on Tenant A
        $response = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'alpha'])
            ->get('/admin/staff');

        $response->assertStatus(200);
        $response->assertSee('Fatima Staff Alpha');
        $response->assertDontSee('Zainab Staff Beta');

        // Admin A tries to edit Tenant B staff
        $editResponse = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'alpha'])
            ->get("/admin/staff/{$staffB->id}/edit");

        $editResponse->assertStatus(404);
    }

    public function test_tenant_a_cannot_see_tenant_b_stock(): void
    {
        // Tenant A Product & Stock
        app(CurrentTenant::class)->set($this->tenantA);
        setPermissionsTeamId($this->tenantA->id);

        $catA = Category::create([
            'tenant_id' => $this->tenantA->id,
            'name' => 'Apparel Alpha',
            'slug' => 'apparel-alpha',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $prodA = Product::create([
            'tenant_id' => $this->tenantA->id,
            'category_id' => $catA->id,
            'name' => 'Emerald Kaftan Alpha',
            'slug' => 'emerald-kaftan-alpha',
            'base_price' => 30000.00,
            'status' => 'active',
        ]);

        $varA = ProductVariant::create([
            'tenant_id' => $this->tenantA->id,
            'product_id' => $prodA->id,
            'size' => 'L',
            'color' => 'Emerald',
            'sku' => 'KAFTAN-A-01',
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        StockMovement::create([
            'tenant_id' => $this->tenantA->id,
            'product_variant_id' => $varA->id,
            'type' => 'in',
            'quantity' => 15,
            'previous_stock' => 0,
            'new_stock' => 15,
            'reason' => 'initial_intake_alpha',
            'reference_type' => 'Manual',
            'created_by' => $this->adminA->id,
        ]);

        // Tenant B Product & Stock
        app(CurrentTenant::class)->set($this->tenantB);
        setPermissionsTeamId($this->tenantB->id);

        $catB = Category::create([
            'tenant_id' => $this->tenantB->id,
            'name' => 'Apparel Beta',
            'slug' => 'apparel-beta',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $prodB = Product::create([
            'tenant_id' => $this->tenantB->id,
            'category_id' => $catB->id,
            'name' => 'Sapphire Jumpsuit Beta',
            'slug' => 'sapphire-jumpsuit-beta',
            'base_price' => 60000.00,
            'status' => 'active',
        ]);

        $varB = ProductVariant::create([
            'tenant_id' => $this->tenantB->id,
            'product_id' => $prodB->id,
            'size' => 'M',
            'color' => 'Sapphire',
            'sku' => 'JUMPSUIT-B-01',
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        StockMovement::create([
            'tenant_id' => $this->tenantB->id,
            'product_variant_id' => $varB->id,
            'type' => 'in',
            'quantity' => 8,
            'previous_stock' => 0,
            'new_stock' => 8,
            'reason' => 'initial_intake_beta',
            'reference_type' => 'Manual',
            'created_by' => $this->adminB->id,
        ]);

        // Admin A checks stock on Tenant A
        $response = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'alpha'])
            ->get('/admin/stock');

        $response->assertStatus(200);
        $response->assertSee('Emerald Kaftan Alpha');
        $response->assertSee('KAFTAN-A-01');
        $response->assertDontSee('Sapphire Jumpsuit Beta');
        $response->assertDontSee('JUMPSUIT-B-01');
    }

    public function test_same_email_can_register_in_two_tenants(): void
    {
        $email = 'shared.customer@example.com';

        // 1. Register on Tenant A
        $responseA = $this->withHeaders(['X-Tenant' => 'alpha'])
            ->post('/register', [
                'name' => 'Shared Customer on Alpha',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $responseA->assertRedirect(route('dashboard', absolute: false));

        Auth::logout();

        // 2. Register with same email on Tenant B
        $responseB = $this->withHeaders(['X-Tenant' => 'beta'])
            ->post('/register', [
                'name' => 'Shared Customer on Beta',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $responseB->assertRedirect(route('dashboard', absolute: false));

        // 3. Verify both user records exist with distinct tenant_ids
        $userA = User::withoutGlobalScope('tenant')
            ->where('email', $email)
            ->where('tenant_id', $this->tenantA->id)
            ->first();

        $userB = User::withoutGlobalScope('tenant')
            ->where('email', $email)
            ->where('tenant_id', $this->tenantB->id)
            ->first();

        $this->assertNotNull($userA);
        $this->assertNotNull($userB);
        $this->assertNotEquals($userA->id, $userB->id);
        $this->assertEquals($this->tenantA->id, $userA->tenant_id);
        $this->assertEquals($this->tenantB->id, $userB->tenant_id);

        // 4. Duplicate registration on same tenant should fail validation
        Auth::logout();

        $duplicateResponse = $this->withHeaders(['X-Tenant' => 'alpha'])
            ->post('/register', [
                'name' => 'Duplicate Attempt',
                'email' => $email,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $duplicateResponse->assertSessionHasErrors('email');
    }

    public function test_admin_of_tenant_a_cannot_access_tenant_b_routes(): void
    {
        // Admin of Tenant A attempts to access Tenant B admin dashboard
        $response = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'beta'])
            ->get('/admin');

        $response->assertStatus(403);

        // Admin of Tenant A attempts to access Tenant B products management
        $responseProducts = $this->actingAs($this->adminA)
            ->withHeaders(['X-Tenant' => 'beta'])
            ->get('/admin/products');

        $responseProducts->assertStatus(403);
    }
}
