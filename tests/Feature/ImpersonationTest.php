<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_super_admin_can_impersonate_store_admin(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->firstOrFail();
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->firstOrFail();
        $targetAdmin = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->firstOrFail();

        $response = $this->actingAs($superAdmin)
            ->post("/super-admin/tenants/{$tenant->id}/impersonate-admin");

        $response->assertRedirect('/admin?tenant='.$tenant->slug);
        $this->assertEquals($targetAdmin->id, Auth::id());
        $this->assertEquals($superAdmin->id, session('impersonator_id'));
        $this->assertEquals('super_admin', session('impersonator_role'));
    }

    public function test_super_admin_can_impersonate_store_staff(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->firstOrFail();
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->firstOrFail();
        $targetStaff = User::withoutTenancy()->where('email', 'staff@jessiclassiq.com')->firstOrFail();

        $response = $this->actingAs($superAdmin)
            ->post("/super-admin/tenants/{$tenant->id}/impersonate-staff");

        $response->assertRedirect('/admin/sales/create?tenant='.$tenant->slug);
        $this->assertEquals($targetStaff->id, Auth::id());
        $this->assertEquals($superAdmin->id, session('impersonator_id'));
        $this->assertEquals('super_admin', session('impersonator_role'));
    }

    public function test_store_admin_can_impersonate_staff(): void
    {
        $storeAdmin = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->firstOrFail();
        $targetStaff = User::withoutTenancy()->where('email', 'staff@jessiclassiq.com')->firstOrFail();

        $response = $this->actingAs($storeAdmin)
            ->withSession(['active_tenant_id' => $storeAdmin->tenant_id])
            ->post('/admin/impersonate-staff', ['user_id' => $targetStaff->id]);

        $response->assertRedirect('/admin/sales/create');
        $this->assertEquals($targetStaff->id, Auth::id());
        $this->assertEquals($storeAdmin->id, session('impersonator_id'));
        $this->assertEquals('admin', session('impersonator_role'));
    }

    public function test_can_leave_impersonation_and_return_to_super_admin(): void
    {
        $superAdmin = User::withoutTenancy()->where('email', 'superadmin@shopmanager.test')->firstOrFail();
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->firstOrFail();
        $targetStaff = User::withoutTenancy()->where('email', 'staff@jessiclassiq.com')->firstOrFail();

        // 1. Enter impersonation as staff
        $this->actingAs($superAdmin)
            ->post("/super-admin/tenants/{$tenant->id}/impersonate-staff");

        $this->assertEquals($targetStaff->id, Auth::id());

        // 2. Leave impersonation
        $response = $this->post('/impersonate/leave');

        $response->assertRedirect('/super-admin/tenants');
        $this->assertEquals($superAdmin->id, Auth::id());
        $this->assertNull(session('impersonator_id'));
    }

    public function test_can_leave_impersonation_and_return_to_store_admin(): void
    {
        $storeAdmin = User::withoutTenancy()->where('email', 'admin@jessiclassiq.com')->firstOrFail();
        $targetStaff = User::withoutTenancy()->where('email', 'staff@jessiclassiq.com')->firstOrFail();

        // 1. Enter staff view as admin
        $this->actingAs($storeAdmin)
            ->withSession(['active_tenant_id' => $storeAdmin->tenant_id])
            ->post('/admin/impersonate-staff');

        $this->assertEquals($targetStaff->id, Auth::id());

        // 2. Leave staff view and return to store admin
        $response = $this->post('/impersonate/leave');

        $response->assertRedirect('/admin');
        $this->assertEquals($storeAdmin->id, Auth::id());
        $this->assertNull(session('impersonator_id'));
    }

    public function test_regular_customer_cannot_impersonate(): void
    {
        $customer = User::withoutTenancy()->where('email', 'customer@jessiclassiq.com')->firstOrFail();
        $tenant = Tenant::where('slug', 'jessis-classiq-store')->firstOrFail();

        $response = $this->actingAs($customer)
            ->post("/super-admin/tenants/{$tenant->id}/impersonate-admin");
        $response->assertStatus(403);

        $responseAdmin = $this->actingAs($customer)
            ->post('/admin/impersonate-staff');
        $responseAdmin->assertStatus(403);
    }
}
