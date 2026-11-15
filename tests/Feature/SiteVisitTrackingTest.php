<?php

namespace Tests\Feature;

use App\Models\SiteVisit;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteVisitTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storefront_and_platform_visits_are_recorded_and_shown_on_dashboards(): void
    {
        $tenant = Tenant::factory()->create(['status' => 'active', 'name' => 'Alpha Boutique']);
        $storeAdmin = User::factory()->create([
            'tenant_id' => $tenant->id,
            'role' => 'admin',
        ]);
        $superAdmin = User::factory()->create([
            'tenant_id' => null,
            'role' => 'super_admin',
        ]);

        // Visit storefront home as a regular browser user
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->get('/')
            ->assertOk();

        $this->assertDatabaseHas('site_visits', [
            'path' => '/',
        ]);

        // Record a visit specifically for the tenant & a platform visit
        SiteVisit::create([
            'tenant_id' => $tenant->id,
            'session_id' => 'sess-store-1',
            'ip_address' => '127.0.0.1',
            'path' => '/',
            'visited_on' => now()->toDateString(),
        ]);

        // Store admin sees Store Visitors cloud card and dedicated Storefront Visitors page
        app(CurrentTenant::class)->set($tenant);
        $this->actingAs($storeAdmin)
            ->withSession(['active_tenant_id' => $tenant->id])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Store Visitors')
            ->assertSee('visits today');

        $this->actingAs($storeAdmin)
            ->withSession(['active_tenant_id' => $tenant->id])
            ->get(route('admin.visitors.index'))
            ->assertOk()
            ->assertSee('Storefront Visitors')
            ->assertSee('127.0.0.1');

        // Super admin sees Site Visitors stat and dedicated Site Visitors page
        $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Site Visitors');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.visitors.index'))
            ->assertOk()
            ->assertSee('Site Visitors')
            ->assertSee('Alpha Boutique');
    }
}
