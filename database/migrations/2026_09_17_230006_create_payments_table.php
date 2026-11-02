<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->string('paystack_reference')->unique()->index();
            $table->string('status')->default('pending')->index(); // pending, successful, failed, abandoned
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('NGN');
            $table->string('channel')->nullable(); // card, bank, ussd, qr, etc.
            $table->string('customer_email')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
