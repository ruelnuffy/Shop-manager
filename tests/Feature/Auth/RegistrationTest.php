<?php

namespace Tests\Feature\Auth;

use App\Mail\NewAccountRegisteredMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_notify_admin_with_whatsapp_prompt(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'store_name' => 'Ramah Atelier',
            'plan' => 'pro',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+234 801 234 5678',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $response->assertSessionHas('whatsapp_onboarding', function (array $onboarding): bool {
            return $onboarding['store_name'] === 'Ramah Atelier'
                && $onboarding['plan'] === 'pro'
                && $onboarding['phone'] === '+234 801 234 5678'
                && str_contains($onboarding['whatsapp_url'], 'https://wa.me/2349046686176');
        });

        Mail::assertSent(NewAccountRegisteredMail::class, function (NewAccountRegisteredMail $mail): bool {
            return $mail->hasTo('ruelnuffy@gmail.com')
                && $mail->user->phone === '+234 801 234 5678'
                && $mail->tenant->name === 'Ramah Atelier';
        });
    }

    public function test_merchant_registration_requires_phone_number(): void
    {
        $response = $this->post('/register', [
            'store_name' => 'Ramah Atelier',
            'plan' => 'starter',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('phone');
        $this->assertGuest();
    }

    public function test_pending_tenant_features_are_disabled_until_activated(): void
    {
        Mail::fake();

        $this->post('/register', [
            'store_name' => 'Locked Store',
            'plan' => 'starter',
            'name' => 'Test Owner',
            'email' => 'owner@example.com',
            'phone' => '09046686176',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'owner@example.com')->firstOrFail();
        $this->assertSame('pending', $user->tenant->status);

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Plan Pending Activation');
        $this->get(route('admin.products.index'))->assertRedirect(route('admin.dashboard'));
        $this->get(route('admin.sales.create'))->assertRedirect(route('admin.dashboard'));

        $user->tenant->update(['status' => 'active']);

        $this->get(route('admin.products.index'))->assertOk();
    }
}
