<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage inventory',
            'manage orders',
            'manage sales',
            'manage staff',
            'manage payroll',
            'import ledger',
            'view reports',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Create global roles (team_id = null) and assign permissions
        $previousTeamId = function_exists('getPermissionsTeamId') ? getPermissionsTeamId() : null;
        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId(null);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'manage inventory',
            'manage orders',
            'manage sales',
        ]);

        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        if (function_exists('setPermissionsTeamId')) {
            setPermissionsTeamId($previousTeamId);
        }
    }
}
