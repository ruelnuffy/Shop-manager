<?php

namespace Tests\Feature;

use App\Mail\SubscriptionExpiryNoticeMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SubscriptionExpirationCronTest extends TestCase
{
    use RefreshDatabase;

    public function test_activating_store_sets_one_month_subscription_window(): void
    {
        $superAdmin = User::factory()->create([
            'tenant_id' => null,
            'role' => 'super_admin',
        ]);
        $tenant = Tenant::factory()->create(['status' => 'pending']);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.tenants.toggle-status', $tenant))
            ->assertRedirect();

        $tenant->refresh();
        $this->assertSame('active', $tenant->status);
        $this->assertNotNull($tenant->subscription_started_at);
        $this->assertNotNull($tenant->subscription_expires_at);
        $this->assertTrue($tenant->subscription_expires_at->greaterThan(now()->addDays(25)));
    }

    public function test_cron_sends_heads_up_before_expiry_and_pauses_expired_stores(): void
    {
        Mail::fake();

        $expiringTenant = Tenant::factory()->create([
            'status' => 'active',
            'plan' => 'starter',
            'subscription_started_at' => now()->subDays(28),
            'subscription_expires_at' => now()->addDays(2),
        ]);
        $expiringAdmin = User::factory()->create([
            'tenant_id' => $expiringTenant->id,
            'role' => 'admin',
            'email' => 'expiring@example.com',
        ]);

        $expiredTenant = Tenant::factory()->create([
            'status' => 'active',
            'plan' => 'pro',
            'subscription_started_at' => now()->subMonth()->subDay(),
            'subscription_expires_at' => now()->subHour(),
        ]);
        $expiredAdmin = User::factory()->create([
            'tenant_id' => $expiredTenant->id,
            'role' => 'admin',
            'email' => 'expired@example.com',
        ]);

        $this->artisan('subscriptions:check-expirations')
            ->assertExitCode(0);

        $expiringTenant->refresh();
        $expiredTenant->refresh();

        $this->assertNotNull($expiringTenant->expiry_reminder_sent_at);
        $this->assertSame('active', $expiringTenant->status);
        $this->assertSame('pending', $expiredTenant->status);

        Mail::assertSent(SubscriptionExpiryNoticeMail::class, function (SubscriptionExpiryNoticeMail $mail) use ($expiringAdmin) {
            return $mail->hasTo($expiringAdmin->email) && $mail->noticeType === 'expiring_soon';
        });

        Mail::assertSent(SubscriptionExpiryNoticeMail::class, function (SubscriptionExpiryNoticeMail $mail) use ($expiredAdmin) {
            return $mail->hasTo($expiredAdmin->email) && $mail->noticeType === 'expired';
        });
    }
}
