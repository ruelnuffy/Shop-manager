<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $movementsQuery = StockMovement::with(['variant.product', 'creator'])->latest();

        if ($request->filled('type')) {
            $movementsQuery->where('type', $request->type);
        }

        if ($request->filled('reason')) {
            $movementsQuery->where('reason', $request->reason);
        }

        $movements = $movementsQuery->paginate(15)->withQueryString();

        $allVariants = ProductVariant::with('product')->where('is_active', true)->get();

        return view('admin.stock.index', compact('movements', 'allVariants'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:100',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);
        $prev = $variant->stock_quantity;
        $qty = (int) $request->quantity;

        if ($request->type === 'out' && $qty > $prev) {
            return redirect()->back()->with('error', "Cannot remove {$qty} units. Current stock is only {$prev}.");
        }

        $newStock = match ($request->type) {
            'in' => $prev + $qty,
            'out' => $prev - $qty,
            'adjustment' => $qty, // absolute reset
        };

        DB::transaction(function () use ($variant, $request, $qty, $prev, $newStock) {
            $variant->update(['stock_quantity' => $newStock]);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => $request->type,
                'quantity' => $qty,
                'previous_stock' => $prev,
                'new_stock' => $newStock,
                'reason' => $request->reason,
                'reference_type' => 'Manual',
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.stock.index')->with('success', "Stock updated for SKU: {$variant->sku}. New quantity: {$newStock}.");
    }
}
