<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Restore global uniqueness on payments.paystack_reference
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                // Ensure tenant_id has its own index for the foreign key before dropping composite unique
                $table->index('tenant_id');
            });

            Schema::table('payments', function (Blueprint $table) {
                // Drop tenant-composite unique index
                $table->dropUnique(['tenant_id', 'paystack_reference']);
                // Restore global unique index
                $table->unique('paystack_reference');
            });
        }

        // 2. Ensure global uniqueness on wallet_transactions.reference
        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                // Drop standard index if present
                $table->dropIndex(['reference']);
                // Add global unique index
                $table->unique('reference');
            });
        }

        // 3. Add error_message column to ocr_import_batches
        if (Schema::hasTable('ocr_import_batches') && ! Schema::hasColumn('ocr_import_batches', 'error_message')) {
            Schema::table('ocr_import_batches', function (Blueprint $table) {
                $table->text('error_message')->nullable()->after('parsed_data');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropUnique(['paystack_reference']);
                $table->unique(['tenant_id', 'paystack_reference']);
            });
        }

        if (Schema::hasTable('wallet_transactions')) {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->dropUnique(['reference']);
                $table->index('reference');
            });
        }

        if (Schema::hasTable('ocr_import_batches') && Schema::hasColumn('ocr_import_batches', 'error_message')) {
            Schema::table('ocr_import_batches', function (Blueprint $table) {
                $table->dropColumn('error_message');
            });
        }
    }
};
