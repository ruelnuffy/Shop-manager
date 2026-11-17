<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if (! Schema::hasColumn('tenants', 'bank_name')) {
                $table->text('bank_name')->nullable()->after('paystack_secret_key');
            }
            if (! Schema::hasColumn('tenants', 'bank_account_number')) {
                $table->text('bank_account_number')->nullable()->after('bank_name');
            }
            if (! Schema::hasColumn('tenants', 'bank_account_name')) {
                $table->text('bank_account_name')->nullable()->after('bank_account_number');
            }
            if (! Schema::hasColumn('tenants', 'bank_details_signature')) {
                $table->string('bank_details_signature', 64)->nullable()->after('bank_account_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'bank_details_signature',
            ]);
        });
    }
};
