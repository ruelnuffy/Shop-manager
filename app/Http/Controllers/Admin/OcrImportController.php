<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessOcrImportBatch;
use App\Models\OcrImportBatch;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use App\Services\CurrentTenant;
use App\Services\OcrService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OcrImportController extends Controller
{
    public function __construct(protected OcrService $ocrService) {}

    public function index(): View
    {
        $batches = OcrImportBatch::with(['creator', 'salesRecords'])
            ->latest()
            ->paginate(10);

        $totalBatches = OcrImportBatch::count();
        $importedRecordsCount = SalesRecord::where('source', 'ocr_import')->count();
        $totalDigitizedRevenue = SalesRecord::where('source', 'ocr_import')->sum('amount');

        return view('admin.ocr.index', compact(
            'batches',
            'totalBatches',
            'importedRecordsCount',
            'totalDigitizedRevenue'
        ));
    }

    public function create(): View
    {
        return view('admin.ocr.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ledger_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'raw_text_override' => 'nullable|string',
        ]);

        if (! $request->hasFile('ledger_image') && ! $request->filled('raw_text_override')) {
            return redirect()->back()->with('error', 'Please upload a ledger photo or provide raw handwritten ledger text.');
        }

        $tenantId = app(CurrentTenant::class)->id() ?? 'default';
        $imagePath = "tenants/{$tenantId}/ledger_uploads/default_ledger_sheet.png";
        $originalFilename = 'manual_ledger_entry.txt';

        if ($request->hasFile('ledger_image')) {
            $file = $request->file('ledger_image');
            $originalFilename = $file->getClientOriginalName();
            $imagePath = $file->store("tenants/{$tenantId}/ledger_uploads", 'private');
        }

        $rawTextOverride = $request->input('raw_text_override');

        $batch = OcrImportBatch::create([
            'image_path' => $imagePath,
            'original_filename' => $originalFilename,
            'status' => 'pending',
            'raw_text' => $rawTextOverride,
            'parsed_data' => null,
            'created_by' => Auth::id(),
        ]);

        ProcessOcrImportBatch::dispatch($batch->id, $rawTextOverride, is_numeric($tenantId) ? (int) $tenantId : null);

        return redirect()->route('admin.ocr.review', $batch->id)
            ->with('success', 'Paper ledger digitized! Please review and confirm the extracted entries.');
    }

    public function review(OcrImportBatch $batch): View
    {
        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->get();

        return view('admin.ocr.review', compact('batch', 'variants'));
    }

    public function commit(Request $request, OcrImportBatch $batch): RedirectResponse
    {
        $request->validate([
            'rows' => 'required|array|min:1',
            'rows.*.date' => 'required|date',
            'rows.*.item_description' => 'required|string|max:255',
            'rows.*.quantity' => 'required|integer|min:1',
            'rows.*.unit_price' => 'nullable|numeric|min:0',
            'rows.*.amount' => 'required|numeric|min:0',
            'rows.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'rows.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->rows as $row) {
                $variantId = ! empty($row['product_variant_id']) ? (int) $row['product_variant_id'] : null;
                $quantity = (int) $row['quantity'];
                $amount = (float) $row['amount'];
                $unitPrice = ! empty($row['unit_price']) ? (float) $row['unit_price'] : ($amount / max(1, $quantity));

                $salesRecord = SalesRecord::create([
                    'batch_id' => $batch->id,
                    'product_variant_id' => $variantId,
                    'date' => $row['date'],
                    'item_description' => $row['item_description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'source' => 'ocr_import',
                    'status' => 'reconciled',
                    'created_by' => Auth::id(),
                    'notes' => $row['notes'] ?? 'Reconciled from paper ledger',
                ]);

                // If tied to inventory variant, decrement stock & log movement
                if ($variantId) {
                    $variant = ProductVariant::find($variantId);
                    if ($variant) {
                        $prevStock = $variant->stock_quantity;
                        $newStock = max(0, $prevStock - $quantity);
                        $variant->update(['stock_quantity' => $newStock]);

                        StockMovement::create([
                            'product_variant_id' => $variant->id,
                            'type' => 'out',
                            'quantity' => $quantity,
                            'previous_stock' => $prevStock,
                            'new_stock' => $newStock,
                            'reason' => 'ocr_import',
                            'reference_type' => 'SalesRecord',
                            'reference_id' => $salesRecord->id,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            }

            $batch->update([
                'status' => 'imported',
                'parsed_data' => $request->rows,
            ]);

            DB::commit();

            return redirect()->route('admin.ocr.show', $batch->id)
                ->with('success', 'Ledger entries committed! Inventory stock adjusted and sales ledger records created.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Failed to commit ledger: '.$e->getMessage());
        }
    }

    public function show(OcrImportBatch $batch): View
    {
        $batch->load(['creator', 'salesRecords.variant.product']);

        return view('admin.ocr.show', compact('batch'));
    }

    public function image(OcrImportBatch $batch): Response
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($batch->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized access to this ledger batch image.');
        }

        $path = $batch->image_path;
        if (! empty($path)) {
            if (Storage::disk('private')->exists($path)) {
                return Storage::disk('private')->response($path);
            }
            if (Storage::disk('local')->exists($path)) {
                return Storage::disk('local')->response($path);
            }
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->response($path);
            }
        }

        abort(404, 'Ledger image file not found.');
    }
}
