<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tenant-owned tables requiring tenant_id.
     *
     * @var list<string>
     */
    protected array $tables = [
        'users',
        'categories',
        'products',
        'product_variants',
        'orders',
        'payments',
        'wallet_transactions',
        'staff',
        'salary_payments',
        'stock_movements',
        'sales_records',
        'ocr_import_batches',
        'shop_locations',
    ];

    public function up(): void
    {
        // 1. Add nullable tenant_id foreign key to each table
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();
                });
            }
        }

        // 2. Create the default tenant ("Jessi's ClassiQ Store")
        $defaultTenant = DB::table('tenants')->where('slug', 'jessis-classiq-store')->first();
        if (! $defaultTenant) {
            $tenantId = DB::table('tenants')->insertGetId([
                'name' => "Jessi's ClassiQ Store",
                'slug' => 'jessis-classiq-store',
                'subdomain' => 'jessiclassiq',
                'domain' => null,
                'settings' => json_encode([]),
                'plan' => 'standard',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $tenantId = $defaultTenant->id;
        }

        // 3. Backfill all existing rows with the default tenant
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $tenantId]);
            }
        }

        // 4. Replace global unique indexes with composite ones including tenant_id
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $t) {
                $t->dropUnique(['email']);
                $t->unique(['tenant_id', 'email']);
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $t) {
                $t->dropUnique(['slug']);
                $t->unique(['tenant_id', 'slug']);
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $t) {
                $t->dropUnique(['slug']);
                $t->unique(['tenant_id', 'slug']);
            });
        }

        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $t) {
                $t->dropUnique(['sku']);
                $t->unique(['tenant_id', 'sku']);
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->dropUnique(['order_number']);
                $t->unique(['tenant_id', 'order_number']);
            });
        }

        if (Schema::hasTable('shop_locations')) {
            Schema::table('shop_locations', function (Blueprint $t) {
                $t->dropUnique(['slug']);
                $t->unique(['tenant_id', 'slug']);
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $t) {
                $t->dropUnique(['paystack_reference']);
                $t->unique(['tenant_id', 'paystack_reference']);
            });
        }

        // 5. Ensure Spatie permission team support is enabled for existing schemas
        if (Schema::hasTable('roles') && ! Schema::hasColumn('roles', 'team_id')) {
            Schema::table('roles', function (Blueprint $t) {
                $t->unsignedBigInteger('team_id')->nullable()->after('id');
                $t->index('team_id', 'roles_team_foreign_key_index');
            });
        }

        if (Schema::hasTable('model_has_roles') && ! Schema::hasColumn('model_has_roles', 'team_id')) {
            Schema::table('model_has_roles', function (Blueprint $t) use ($tenantId) {
                $t->unsignedBigInteger('team_id')->default($tenantId)->after('role_id');
                $t->index('team_id', 'model_has_roles_team_foreign_key_index');
            });
        }

        if (Schema::hasTable('model_has_permissions') && ! Schema::hasColumn('model_has_permissions', 'team_id')) {
            Schema::table('model_has_permissions', function (Blueprint $t) use ($tenantId) {
                $t->unsignedBigInteger('team_id')->default($tenantId)->after('permission_id');
                $t->index('team_id', 'model_has_permissions_team_foreign_key_index');
            });
        }

        // 6. Make tenant_id non-nullable across all tenant-owned tables
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('tenant_id')->nullable(false)->change();
                });
            }
        }
    }

    public function down(): void
    {
        // Revert composite unique constraints back to global unique
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'email']);
                $t->unique(['email']);
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'slug']);
                $t->unique(['slug']);
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'slug']);
                $t->unique(['slug']);
            });
        }

        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'sku']);
                $t->unique(['sku']);
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'order_number']);
                $t->unique(['order_number']);
            });
        }

        if (Schema::hasTable('shop_locations')) {
            Schema::table('shop_locations', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'slug']);
                $t->unique(['slug']);
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $t) {
                $t->dropUnique(['tenant_id', 'paystack_reference']);
                $t->unique(['paystack_reference']);
            });
        }

        // Drop tenant_id foreign keys and columns
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropConstrainedForeignId('tenant_id');
                });
            }
        }
    }
};
