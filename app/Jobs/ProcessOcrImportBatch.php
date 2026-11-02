<?php

namespace App\Jobs;

use App\Jobs\Concerns\TenantAware;
use App\Models\OcrImportBatch;
use App\Services\CurrentTenant;
use App\Services\OcrService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessOcrImportBatch implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, TenantAware;

    public int $tries = 1;

    public bool $failOnTimeout = true;

    public function __construct(
        public int $batchId,
        public ?string $rawTextOverride = null,
        ?int $tenantId = null
    ) {
        $this->tenantId = $tenantId ?? app(CurrentTenant::class)->id();
    }

    public function handle(OcrService $ocrService): void
    {
        $batch = OcrImportBatch::find($this->batchId);
        if (! $batch) {
            return;
        }

        $batch->update([
            'status' => 'processing',
        ]);

        try {
            $rawText = $this->rawTextOverride;
            if (empty($rawText) && ! empty($batch->image_path)) {
                $fullPath = null;

                if (Storage::disk('private')->exists($batch->image_path)) {
                    $fullPath = Storage::disk('private')->path($batch->image_path);
                } elseif (Storage::disk('local')->exists($batch->image_path)) {
                    $fullPath = Storage::disk('local')->path($batch->image_path);
                } elseif (Storage::disk('public')->exists($batch->image_path)) {
                    // Backwards-compatibility fallback for existing public disk batches
                    $fullPath = Storage::disk('public')->path($batch->image_path);
                }

                if ($fullPath && file_exists($fullPath)) {
                    $rawText = $ocrService->extractTextFromImage($fullPath);
                }
            }

            $rawText = $rawText ?? '';
            $parsedRows = $ocrService->parseLedgerText($rawText);

            $batch->update([
                'status' => 'ready',
                'raw_text' => $rawText,
                'parsed_data' => $parsedRows,
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error("OCR processing failed for batch {$batch->id}: ".$e->getMessage());

            $batch->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function failed(?\Throwable $exception): void
    {
        $batch = OcrImportBatch::find($this->batchId);
        if ($batch) {
            $batch->update([
                'status' => 'failed',
                'error_message' => $exception ? $exception->getMessage() : 'OCR job failed or timed out.',
            ]);
        }
    }
}
