<?php

namespace App\Listeners;

use App\Mail\AccountLoginNoticeMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLoginNoticeEmail
{
    /**
     * Handle the user login event and send an email notice to the platform owner.
     */
    public function handle(Login $event): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        if (session()->has('impersonator_id')) {
            return;
        }

        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $tenant = $user->tenant_id ? Tenant::find($user->tenant_id) : null;

        try {
            Mail::to('ruelnuffy@gmail.com')->send(
                new AccountLoginNoticeMail(
                    user: $user,
                    tenant: $tenant,
                    ipAddress: request()?->ip(),
                    loggedInAt: now()->format('M d, Y g:i A T'),
                )
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to send account login notice email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
