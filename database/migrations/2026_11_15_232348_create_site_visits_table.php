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
        if (Schema::hasTable('site_visits')) {
            return;
        }

        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('path', 255);
            $table->string('referer', 500)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->date('visited_on')->index();
            $table->timestamps();

            $table->index(['tenant_id', 'visited_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_visits');
    }
};
