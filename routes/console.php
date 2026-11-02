<?php

use App\Models\OcrImportBatch;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Shared hosting / cPanel worker: executes queue jobs every minute with 50s max-time to prevent process overlap.
// Mutex expires after 10 minutes so a killed worker cannot block the queue for 24 hours.
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping(10);

// Reap OCR import batches stuck in processing for more than 10 minutes
Artisan::command('ocr:reap-stuck-batches', function () {
    $threshold = now()->subMinutes(10);
    $stuckBatches = OcrImportBatch::withoutGlobalScopes()
        ->where('status', 'processing')
        ->where('updated_at', '<', $threshold)
        ->get();

    foreach ($stuckBatches as $batch) {
        $batch->update([
            'status' => 'failed',
            'error_message' => 'OCR processing timed out after 10 minutes. The worker process may have died or stalled.',
        ]);
    }

    $this->info("Reaped {$stuckBatches->count()} stuck OCR import batches.");
})->purpose('Reap OCR import batches stuck in processing for more than 10 minutes');

Schedule::command('ocr:reap-stuck-batches')
    ->everyFiveMinutes()
    ->withoutOverlapping(10);
