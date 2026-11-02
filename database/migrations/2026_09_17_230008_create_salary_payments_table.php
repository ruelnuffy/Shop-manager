<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('period', 7)->index(); // YYYY-MM
            $table->string('paystack_transfer_code')->nullable()->index();
            $table->string('paystack_reference')->nullable()->index();
            $table->string('status')->default('pending')->index(); // pending, processing, success, failed, reversed
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
