<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ocr_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('original_filename')->nullable();
            $table->string('status')->default('pending')->index(); // pending, processed, reviewed, imported, failed
            $table->longText('raw_text')->nullable();
            $table->json('parsed_data')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_import_batches');
    }
};
