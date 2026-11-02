<?php

namespace App\Console\Commands;

use App\Models\OcrImportBatch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateLedgersToPrivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ocr:migrate-ledgers-to-private {--dry-run : Simulate migration without modifying files or database records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy public disk OCR ledger sheets to tenant-isolated private disk';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Running in --dry-run mode. No files will be moved or database records updated.');
        }

        $batches = OcrImportBatch::withoutGlobalScopes()->get();

        $totalScanned = $batches->count();
        $migratedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $this->info("Scanning {$totalScanned} OCR import batches for public disk ledger files...");

        foreach ($batches as $batch) {
            $path = $batch->image_path;

            if (empty($path)) {
                $skippedCount++;

                continue;
            }

            // Check if file exists on public disk
            $existsOnPublic = Storage::disk('public')->exists($path);
            $tenantId = $batch->tenant_id ?? 'global';
            $filename = basename($path);
            $targetPath = "tenants/{$tenantId}/ledger_uploads/{$filename}";

            if (! $existsOnPublic) {
                // If it already lives on private disk, it's already migrated (idempotency)
                if (Storage::disk('private')->exists($path) || Storage::disk('private')->exists($targetPath)) {
                    $skippedCount++;

                    continue;
                }

                // If not found on either, skip
                $skippedCount++;

                continue;
            }

            $fileSize = Storage::disk('public')->size($path);

            if ($dryRun) {
                $this->line("[DRY RUN] Would copy '{$path}' ({$fileSize} bytes) from public to '{$targetPath}' on private disk for Batch #{$batch->id}.");
                $migratedCount++;

                continue;
            }

            try {
                // 1. Copy to private disk
                $content = Storage::disk('public')->get($path);
                Storage::disk('private')->put($targetPath, $content);

                // 2. Verify the copy
                if (! Storage::disk('private')->exists($targetPath) || Storage::disk('private')->size($targetPath) !== $fileSize) {
                    $this->error("Failed to verify copy for Batch #{$batch->id} at {$targetPath}. Skipping deletion.");
                    $errorCount++;

                    continue;
                }

                // 3. Update the stored path on the batch record
                $batch->update([
                    'image_path' => $targetPath,
                ]);

                // 4. Delete the public file
                Storage::disk('public')->delete($path);

                $this->info("Migrated Batch #{$batch->id}: '{$path}' -> '{$targetPath}'");
                $migratedCount++;
            } catch (\Throwable $e) {
                $this->error("Error migrating Batch #{$batch->id}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Scanned', $totalScanned],
                ['Migrated', $migratedCount],
                ['Skipped (Already on private/absent)', $skippedCount],
                ['Errors', $errorCount],
                ['Dry Run Mode', $dryRun ? 'Yes' : 'No'],
            ]
        );

        return $errorCount > 0 ? self::FAILURE : self::SUCCESS;
    }
}
