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
            $table->timestamp('subscription_started_at')->nullable()->after('status');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_started_at');
            $table->timestamp('expiry_reminder_sent_at')->nullable()->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_started_at',
                'subscription_expires_at',
                'expiry_reminder_sent_at',
            ]);
        });
    }
};
