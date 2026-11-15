<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiryNoticeMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckSubscriptionExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send heads-up reminders for 1-month subscriptions nearing expiry and pause expired stores.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $reminderCutoff = $now->copy()->addDays(3);

        $expiringTenants = Tenant::query()
            ->where('status', 'active')
            ->whereNotNull('subscription_expires_at')
            ->whereNull('expiry_reminder_sent_at')
            ->where('subscription_expires_at', '>', $now)
            ->where('subscription_expires_at', '<=', $reminderCutoff)
            ->get();

        $remindersSent = 0;

        foreach ($expiringTenants as $tenant) {
            $daysRemaining = max(1, (int) $now->diffInDays($tenant->subscription_expires_at, false));
            $this->sendNotice($tenant, 'expiring_soon', $daysRemaining);
            $tenant->update(['expiry_reminder_sent_at' => $now]);
            $remindersSent++;
        }

        $expiredTenants = Tenant::query()
            ->where('status', 'active')
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', $now)
            ->get();

        $expiredCount = 0;

        foreach ($expiredTenants as $tenant) {
            $tenant->update(['status' => 'pending']);
            $this->sendNotice($tenant, 'expired', 0);
            $expiredCount++;
        }

        $this->info("Sent {$remindersSent} heads-up reminder(s) and expired {$expiredCount} subscription(s).");

        return self::SUCCESS;
    }

    /**
     * Deliver subscription notice email to store admins and platform owner.
     */
    private function sendNotice(Tenant $tenant, string $noticeType, int $daysRemaining): void
    {
        $adminEmails = User::withoutTenancy()
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->pluck('email')
            ->filter()
            ->push('ruelnuffy@gmail.com')
            ->unique()
            ->values()
            ->all();

        foreach ($adminEmails as $email) {
            try {
                Mail::to($email)->send(new SubscriptionExpiryNoticeMail($tenant, $noticeType, $daysRemaining));
            } catch (\Throwable $e) {
                Log::warning('Failed to send subscription expiry notice email', [
                    'tenant_id' => $tenant->id,
                    'email' => $email,
                    'notice_type' => $noticeType,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
