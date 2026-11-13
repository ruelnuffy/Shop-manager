<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\StoreActivityNotification;
use App\Services\CurrentTenant;
use App\Services\StoreNotificationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StoreNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Notif Boutique',
            'slug' => 'notif-boutique',
            'subdomain' => 'notif',
            'status' => 'active',
            'plan' => 'pro',
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $this->admin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'admin',
            'email' => 'owner@notif.test',
        ]);

        $this->staff = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'role' => 'staff',
            'email' => 'cashier@notif.test',
        ]);
    }

    public function test_staff_login_sends_database_and_mail_notification_to_store_admin(): void
    {
        Notification::fake();

        event(new Login('web', $this->staff, false));

        Notification::assertSentTo(
            $this->admin,
            StoreActivityNotification::class,
            function (StoreActivityNotification $notification, array $channels) {
                return $notification->eventType === 'staff_login'
                    && in_array('database', $channels, true)
                    && in_array('mail', $channels, true);
            }
        );

        Notification::assertNotSentTo($this->staff, StoreActivityNotification::class);
    }

    public function test_admin_login_does_not_trigger_staff_login_notification(): void
    {
        Notification::fake();

        event(new Login('web', $this->admin, false));

        Notification::assertNothingSent();
    }

    public function test_staff_pos_sale_sends_notification_to_store_admin(): void
    {
        Notification::fake();

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Bags',
            'slug' => 'bags',
        ]);

        $product = Product::create([
            'tenant_id' => $this->tenant->id,
            'category_id' => $category->id,
            'name' => 'Silk Tote',
            'slug' => 'silk-tote',
            'base_price' => 25000,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'tenant_id' => $this->tenant->id,
            'product_id' => $product->id,
            'sku' => 'SKU-NOTIF-1',
            'size' => 'One Size',
            'color' => 'Black',
            'price' => 25000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->staff)->post(route('admin.sales.store'), [
            'variant_id' => $variant->id,
            'quantity' => 1,
            'amount' => 25000,
            'payment_method' => 'cash',
            'customer_name' => 'Walk-in Buyer',
        ]);

        $response->assertRedirect(route('admin.sales.create'));

        Notification::assertSentTo(
            $this->admin,
            StoreActivityNotification::class,
            function (StoreActivityNotification $notification, array $channels) {
                return $notification->eventType === 'staff_sale'
                    && in_array('database', $channels, true)
                    && in_array('mail', $channels, true);
            }
        );
    }

    public function test_storefront_order_notification_is_persisted_and_accessible_in_admin_ui_and_poll(): void
    {
        $order = Order::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => null,
            'order_number' => 'ORD-TEST-999',
            'status' => 'processing',
            'subtotal' => 45000,
            'shipping_fee' => 0,
            'tax' => 0,
            'total_amount' => 45000,
            'shipping_name' => 'Ada Lovelace',
            'shipping_email' => 'ada@example.com',
            'shipping_phone' => '08012345678',
            'shipping_address' => '12 Marina Street',
            'shipping_city' => 'Lagos',
            'payment_status' => 'paid',
            'payment_method' => 'paystack',
        ]);

        // Store database notification directly without external mail transport dependency
        $this->admin->notifyNow(new StoreActivityNotification(
            eventType: 'storefront_order',
            title: "New Storefront Order #{$order->order_number}",
            message: 'Ada Lovelace placed a storefront order of ₦45,000.00.',
            actionUrl: route('admin.orders.index'),
            actionLabel: 'View Order',
        ), ['database']);

        $this->actingAs($this->admin)
            ->getJson(route('admin.notifications.poll'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.event_type', 'storefront_order');

        $this->actingAs($this->admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('ORD-TEST-999');

        $this->actingAs($this->admin)
            ->post(route('admin.notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $this->admin->fresh()->unreadNotifications()->count());
    }

    public function test_store_notification_service_formats_storefront_order_notification(): void
    {
        Notification::fake();

        $order = Order::create([
            'tenant_id' => $this->tenant->id,
            'order_number' => 'ORD-SVC-101',
            'status' => 'processing',
            'subtotal' => 15000,
            'shipping_fee' => 0,
            'tax' => 0,
            'total_amount' => 15000,
            'shipping_name' => 'Chidi Obi',
            'shipping_email' => 'chidi@example.com',
            'shipping_phone' => '08098765432',
            'shipping_address' => '5 Main Road',
            'shipping_city' => 'Abuja',
            'payment_status' => 'paid',
            'payment_method' => 'wallet',
        ]);

        app(StoreNotificationService::class)->notifyStorefrontOrder($this->tenant, $order);

        Notification::assertSentTo(
            $this->admin,
            StoreActivityNotification::class,
            fn (StoreActivityNotification $n) => $n->eventType === 'storefront_order'
                && str_contains($n->title, 'ORD-SVC-101')
        );
    }
}
