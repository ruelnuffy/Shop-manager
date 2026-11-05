<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CurrentTenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = app(CurrentTenant::class)->get()?->id
            ?? Tenant::first()?->id;

        if ($tenantId && function_exists('setPermissionsTeamId') && getPermissionsTeamId() === null) {
            setPermissionsTeamId($tenantId);
        }

        // 1. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@jessiclassiq.com'],
            [
                'name' => 'Jessi Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+2348011223344',
                'address' => 'Victoria Island, Lagos',
                'city' => 'Lagos',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Staff
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@jessiclassiq.com'],
            [
                'name' => 'Grace Staff',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone' => '+2348022334455',
                'address' => 'Lekki Phase 1, Lagos',
                'city' => 'Lagos',
                'email_verified_at' => now(),
            ]
        );
        $staffUser->syncRoles(['staff']);

        Staff::updateOrCreate(
            ['user_id' => $staffUser->id],
            [
                'position' => 'Store Sales Lead',
                'bank_name' => 'Access Bank',
                'bank_account_number' => '0123456789',
                'bank_code' => '044',
                'recipient_code' => 'RCP_sample123',
                'monthly_salary' => 150000.00,
                'is_active' => true,
            ]
        );

        // 3. Customer
        $customer = User::updateOrCreate(
            ['email' => 'customer@jessiclassiq.com'],
            [
                'name' => 'Amaka Customer',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '+2348033445566',
                'address' => '12 Adeola Odeku St',
                'city' => 'Victoria Island, Lagos',
                'email_verified_at' => now(),
            ]
        );
        $customer->syncRoles(['customer']);

        // Neutral demo accounts for local/testing only
        if (app()->environment(['local', 'testing'])) {
            $superAdmin = User::updateOrCreate(
                ['email' => 'superadmin@shopmanager.test'],
                [
                    'name' => 'ShopManager Super Admin',
                    'password' => Hash::make('password123'),
                    'role' => 'super_admin',
                    'phone' => '+2348000000000',
                    'address' => 'Platform Control Center',
                    'city' => 'Lagos',
                    'email_verified_at' => now(),
                ]
            );

            $platformAdmin = User::updateOrCreate(
                ['email' => 'admin@shopmanager.test'],
                [
                    'name' => 'ShopManager Admin',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'phone' => '+2348000000001',
                    'address' => 'Platform HQ, Victoria Island',
                    'city' => 'Lagos',
                    'email_verified_at' => now(),
                ]
            );
            $platformAdmin->syncRoles(['admin']);

            $platformStaff = User::updateOrCreate(
                ['email' => 'staff@shopmanager.test'],
                [
                    'name' => 'ShopManager Staff',
                    'password' => Hash::make('password123'),
                    'role' => 'staff',
                    'phone' => '+2348000000002',
                    'address' => 'Store Operations, Lekki Phase 1',
                    'city' => 'Lagos',
                    'email_verified_at' => now(),
                ]
            );
            $platformStaff->syncRoles(['staff']);

            Staff::updateOrCreate(
                ['user_id' => $platformStaff->id],
                [
                    'position' => 'Retail Specialist',
                    'bank_name' => 'GTBank',
                    'bank_account_number' => '0123456788',
                    'bank_code' => '058',
                    'recipient_code' => 'RCP_demo123',
                    'monthly_salary' => 175000.00,
                    'is_active' => true,
                ]
            );

            $platformCustomer = User::updateOrCreate(
                ['email' => 'customer@shopmanager.test'],
                [
                    'name' => 'ShopManager Customer',
                    'password' => Hash::make('password123'),
                    'role' => 'customer',
                    'phone' => '+2348000000003',
                    'address' => '10 Innovation Way',
                    'city' => 'Lagos',
                    'email_verified_at' => now(),
                ]
            );
            $platformCustomer->syncRoles(['customer']);
        }
    }
}
