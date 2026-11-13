<?php

namespace App\Listeners;

use App\Models\Tenant;
use App\Models\User;
use App\Services\StoreNotificationService;
use Illuminate\Auth\Events\Login;

class NotifyAdminsOnStaffLogin
{
    public function __construct(
        protected StoreNotificationService $notificationService,
    ) {}

    /**
     * Handle the user login event and notify tenant admins if a staff member logs in.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || ! $user->tenant_id) {
            return;
        }

        if (session()->has('impersonator_id')) {
            return;
        }

        if ($user->isSuperAdmin() || $user->role === 'admin' || $user->hasRole('admin')) {
            return;
        }

        $tenant = Tenant::find($user->tenant_id);
        if (! $tenant) {
            return;
        }

        $this->notificationService->notifyStaffLogin($tenant, $user);
    }
}
