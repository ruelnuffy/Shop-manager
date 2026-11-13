<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SalesRecord;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\StoreActivityNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class StoreNotificationService
{
    /**
     * Notify all store admins of a tenant about a store activity event.
     *
     * @param  array<string, mixed>  $meta
     */
    public function notifyTenantAdmins(
        Tenant $tenant,
        string $eventType,
        string $title,
        string $message,
        string $actionUrl,
        string $actionLabel = 'Open Console',
        array $meta = [],
    ): void {
        $admins = User::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->where(function ($query) {
                $query->where('role', 'admin')
                    ->orWhereHas('roles', fn ($r) => $r->where('name', 'admin'));
            })
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        $storeName = (string) $tenant->setting('branding.name', $tenant->name);
        $meta = array_merge(['store_name' => $storeName], $meta);

        $notification = new StoreActivityNotification(
            eventType: $eventType,
            title: $title,
            message: $message,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            meta: $meta,
        );

        try {
            Notification::send($admins, $notification);
        } catch (\Throwable $e) {
            Log::warning('Store notification dispatch partial/mail failure: '.$e->getMessage(), [
                'tenant_id' => $tenant->id,
                'event_type' => $eventType,
            ]);
        }
    }

    /**
     * Notify store admins when a staff member logs in.
     */
    public function notifyStaffLogin(Tenant $tenant, User $staffUser): void
    {
        $storeName = (string) $tenant->setting('branding.name', $tenant->name);

        $this->notifyTenantAdmins(
            tenant: $tenant,
            eventType: 'staff_login',
            title: "Staff Login: {$staffUser->name}",
            message: "{$staffUser->name} ({$staffUser->email}) just logged into {$storeName}.",
            actionUrl: route('admin.staff.index'),
            actionLabel: 'View Staff',
            meta: [
                'actor_name' => $staffUser->name,
                'actor_email' => $staffUser->email,
                'summary' => 'Logged in at '.now()->format('M d, Y g:i A'),
            ],
        );
    }

    /**
     * Notify store admins when a staff member records a POS / manual sale.
     */
    public function notifyStaffSale(Tenant $tenant, SalesRecord $sale, ?User $actor = null): void
    {
        $actorName = $actor?->name ?? 'Staff';
        $amountFormatted = '₦'.number_format((float) $sale->amount, 2);

        $this->notifyTenantAdmins(
            tenant: $tenant,
            eventType: 'staff_sale',
            title: "New POS Sale ({$amountFormatted})",
            message: "{$actorName} recorded a walk-in sale of {$amountFormatted} for {$sale->item_description}.",
            actionUrl: route('admin.reports.index'),
            actionLabel: 'View Sales Report',
            meta: [
                'actor_name' => $actorName,
                'amount' => (float) $sale->amount,
                'sale_id' => $sale->id,
                'summary' => "Quantity: {$sale->quantity} | Recorded at ".now()->format('M d, Y g:i A'),
            ],
        );
    }

    /**
     * Notify store admins when a customer places a paid order on the storefront.
     */
    public function notifyStorefrontOrder(Tenant $tenant, Order $order): void
    {
        $amountFormatted = '₦'.number_format((float) $order->total_amount, 2);
        $customerName = $order->shipping_name ?: 'A customer';

        $this->notifyTenantAdmins(
            tenant: $tenant,
            eventType: 'storefront_order',
            title: "New Storefront Order #{$order->order_number}",
            message: "{$customerName} placed a storefront order of {$amountFormatted} (paid via ".ucfirst((string) $order->payment_method).').',
            actionUrl: route('admin.orders.index'),
            actionLabel: 'View Order',
            meta: [
                'order_number' => $order->order_number,
                'customer_name' => $customerName,
                'amount' => (float) $order->total_amount,
                'summary' => "Customer: {$customerName} ({$order->shipping_phone}) | City: {$order->shipping_city}",
            ],
        );
    }
}
