<?php

namespace Tests\Feature;

use App\Models\OcrImportBatch;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhaseFiveOcrLedgerImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@jessiclassiq.com')->first();
        $this->staff = User::where('email', 'staff@jessiclassiq.com')->first();
        $this->customer = User::where('email', 'customer@jessiclassiq.com')->first();
    }

    public function test_ocr_routes_accessible_to_admin_and_staff_forbidden_to_guests_and_customers(): void
    {
        // 1. Guest redirected
        $this->get('/admin/ocr-imports')->assertRedirect('/login');

        // 2. Customer gets 403
        $this->actingAs($this->customer)->get('/admin/ocr-imports')->assertStatus(403);

        // 3. Staff gets 200
        $this->actingAs($this->staff)->get('/admin/ocr-imports')->assertStatus(200);

        // 4. Admin gets 200
        $this->actingAs($this->admin)->get('/admin/ocr-imports')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/ocr-imports/upload')->assertStatus(200);
    }

    public function test_uploading_ledger_photo_creates_processed_batch(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('daybook_sheet_12.jpg', 800, 1200);

        $response = $this->actingAs($this->admin)->post('/admin/ocr-imports', [
            'ledger_image' => $file,
        ]);

        $batch = OcrImportBatch::latest()->first();
        $this->assertNotNull($batch);
        $this->assertEquals('ready', $batch->status);
        $this->assertEquals('daybook_sheet_12.jpg', $batch->original_filename);
        $this->assertNotEmpty($batch->raw_text);
        $this->assertIsArray($batch->parsed_data);
        $this->assertNotEmpty($batch->parsed_data);

        $response->assertRedirect('/admin/ocr-imports/'.$batch->id.'/review');
    }

    public function test_uploading_with_raw_text_override(): void
    {
        $customText = <<<'RAW'
=== Physical Sales Sheet ===
Date: 2026-09-14
1. Blush Silk Evening Gown (S) | Qty: 2 | Price: 48500 | Method: POS
2. Aura Rose Stiletto Pumps (EU 38) | Qty: 1 | Price: 32000 | Method: Cash
RAW;

        $response = $this->actingAs($this->staff)->post('/admin/ocr-imports', [
            'raw_text_override' => $customText,
        ]);

        $batch = OcrImportBatch::latest()->first();
        $this->assertNotNull($batch);
        $this->assertEquals($customText, $batch->raw_text);
        $this->assertCount(2, $batch->parsed_data);

        $response->assertRedirect('/admin/ocr-imports/'.$batch->id.'/review');
    }

    public function test_review_page_renders_extracted_rows(): void
    {
        $batch = OcrImportBatch::create([
            'image_path' => 'ledger_uploads/test.jpg',
            'original_filename' => 'test.jpg',
            'status' => 'processed',
            'raw_text' => '1. Blush Silk Evening Gown | Qty: 1 | Price: 48500',
            'parsed_data' => [
                [
                    'date' => '2026-09-15',
                    'item_description' => 'Blush Silk Evening Gown',
                    'product_variant_id' => null,
                    'quantity' => 1,
                    'unit_price' => 48500,
                    'amount' => 48500,
                    'notes' => 'Imported',
                ],
            ],
            'created_by' => $this->admin->id,
        ]);

        $reviewView = $this->actingAs($this->admin)->get('/admin/ocr-imports/'.$batch->id.'/review');
        $reviewView->assertStatus(200);
        $reviewView->assertSee('Reconcile Paper Ledger');
        $reviewView->assertSee('Blush Silk Evening Gown');
        $reviewView->assertSee('Extracted Ledger Lines');
    }

    public function test_committing_reconciled_batch_creates_sales_records_and_decrements_stock(): void
    {
        $variant = ProductVariant::where('stock_quantity', '>', 5)->first();
        $this->assertNotNull($variant);
        $initialStock = $variant->stock_quantity;
        $qtySold = 2;
        $saleAmount = 97000.00;

        $batch = OcrImportBatch::create([
            'image_path' => 'ledger_uploads/test_reconcile.jpg',
            'original_filename' => 'test_reconcile.jpg',
            'status' => 'processed',
            'raw_text' => '1. Test Boutique Item | Qty: 2',
            'created_by' => $this->admin->id,
        ]);

        $commitResponse = $this->actingAs($this->admin)->post('/admin/ocr-imports/'.$batch->id.'/commit', [
            'rows' => [
                [
                    'date' => '2026-09-15',
                    'item_description' => 'Blush Silk Evening Gown',
                    'product_variant_id' => $variant->id,
                    'quantity' => $qtySold,
                    'unit_price' => 48500.00,
                    'amount' => $saleAmount,
                    'notes' => 'Verified with paper daybook line #4',
                ],
            ],
        ]);

        $commitResponse->assertRedirect('/admin/ocr-imports/'.$batch->id);

        // 1. Verify batch status updated
        $batch->refresh();
        $this->assertEquals('imported', $batch->status);

        // 2. Verify SalesRecord created
        $salesRecord = SalesRecord::where('batch_id', $batch->id)->first();
        $this->assertNotNull($salesRecord);
        $this->assertEquals('ocr_import', $salesRecord->source);
        $this->assertEquals('reconciled', $salesRecord->status);
        $this->assertEquals($variant->id, $salesRecord->product_variant_id);
        $this->assertEquals($qtySold, $salesRecord->quantity);
        $this->assertEquals($saleAmount, $salesRecord->amount);

        // 3. Verify ProductVariant stock decremented
        $variant->refresh();
        $this->assertEquals($initialStock - $qtySold, $variant->stock_quantity);

        // 4. Verify StockMovement audit row created
        $movement = StockMovement::where('product_variant_id', $variant->id)
            ->where('reason', 'ocr_import')
            ->latest()
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('out', $movement->type);
        $this->assertEquals($qtySold, $movement->quantity);
        $this->assertEquals('SalesRecord', $movement->reference_type);
        $this->assertEquals($salesRecord->id, $movement->reference_id);
    }

    public function test_admin_can_view_committed_batch_summary(): void
    {
        $batch = OcrImportBatch::create([
            'image_path' => 'ledger_uploads/test.jpg',
            'original_filename' => 'ledger_sheet_sep15.jpg',
            'status' => 'imported',
            'raw_text' => 'sample text',
            'created_by' => $this->admin->id,
        ]);

        SalesRecord::create([
            'batch_id' => $batch->id,
            'date' => '2026-09-15',
            'item_description' => 'Silk Dress',
            'quantity' => 1,
            'unit_price' => 45000,
            'amount' => 45000,
            'source' => 'ocr_import',
            'status' => 'reconciled',
        ]);

        $showView = $this->actingAs($this->admin)->get('/admin/ocr-imports/'.$batch->id);
        $showView->assertStatus(200);
        $showView->assertSee('Committed Daybook Batch');
        $showView->assertSee('ledger_sheet_sep15.jpg');
        $showView->assertSee('Silk Dress');
        $showView->assertSee('45,000');
    }
}
