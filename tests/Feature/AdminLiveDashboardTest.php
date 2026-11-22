<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminLiveDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_live_ajax_hooks_and_mobile_bottom_navbar(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $admin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);
        app(CurrentTenant::class)->set($tenant);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('dashboard-live-refresh-btn', false)
            ->assertSee('data-notif-badge', false)
            ->assertSee('data-orders-badge', false);
    }

    public function test_admin_live_dashboard_and_notification_poll_return_live_counts(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $admin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);
        app(CurrentTenant::class)->set($tenant);

        Order::create([
            'tenant_id' => $tenant->id,
            'order_number' => 'SM-LIVE-1001',
            'status' => 'pending',
            'subtotal' => 12000,
            'shipping_fee' => 3500,
            'tax' => 0,
            'total_amount' => 15500,
            'shipping_name' => 'Kemi Buyer',
            'shipping_email' => 'kemi@example.com',
            'shipping_phone' => '08011112222',
            'shipping_address' => '10 Market Road',
            'shipping_city' => 'Lagos',
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
        ]);

        DatabaseNotification::create([
            'id' => (string) Str::uuid(),
            'type' => 'App\\Notifications\\StoreEventNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
            'data' => [
                'title' => 'New Order',
                'message' => 'Order #SM-LIVE-1001 received.',
            ],
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.dashboard.live-data'))
            ->assertOk()
            ->assertJsonPath('today_revenue', 15500)
            ->assertJsonPath('pending_orders_count', 1)
            ->assertJsonPath('total_orders_count', 1)
            ->assertJsonPath('unread_notifications_count', 1)
            ->assertJsonPath('recent_orders.0.order_number', 'SM-LIVE-1001');

        $this->actingAs($admin)
            ->getJson(route('admin.notifications.poll'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('pending_orders_count', 1);
    }

    public function test_customer_cannot_access_admin_live_dashboard_endpoint(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);
        $customer = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'customer',
        ]);

        $this->actingAs($customer)
            ->getJson(route('admin.dashboard.live-data'))
            ->assertForbidden();
    }
}
