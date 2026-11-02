<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CpanelDeployTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_denied_without_key_or_admin_login(): void
    {
        $response = $this->get('/cpanel-deploy');

        $response->assertStatus(403);
    }

    public function test_access_denied_with_invalid_key(): void
    {
        $response = $this->get('/cpanel-deploy?key=wrong-secret-key');

        $response->assertStatus(403);
    }

    public function test_access_granted_with_valid_key(): void
    {
        $validKey = config('app.cpanel_key', env('CPANEL_SETUP_KEY', 'jessi-staging-2026'));

        $response = $this->get("/cpanel-deploy?key={$validKey}");

        $response->assertStatus(200);
        $response->assertSee('Run Migrations');
        $response->assertSee('Run Full Setup Now');
    }

    public function test_authenticated_admin_can_access_without_key(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/cpanel-deploy');

        $response->assertStatus(200);
        $response->assertSee('Run Migrations');
        $response->assertSee('Run Full Setup Now');
    }

    public function test_json_action_execution(): void
    {
        $validKey = config('app.cpanel_key', env('CPANEL_SETUP_KEY', 'jessi-staging-2026'));

        $response = $this->getJson("/cpanel-deploy?key={$validKey}&action=clear_cache&format=json");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'action' => 'clear_cache',
        ]);
    }

    public function test_subfolder_url_generation(): void
    {
        config(['app.url' => 'http://pingme.name.ng/test']);
        app(AppServiceProvider::class, ['app' => app()])->boot();

        $this->assertEquals('http://pingme.name.ng/test/cpanel-deploy', route('cpanel.deploy'));
        $this->assertEquals('http://pingme.name.ng/test/images/logo.png', asset('images/logo.png'));
    }
}
