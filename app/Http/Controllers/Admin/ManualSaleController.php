<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManualSaleController extends Controller
{
    public function create(): View
    {
        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        $recentSales = SalesRecord::with(['variant.product', 'creator'])
            ->where('source', 'manual')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.sales.create', compact('variants', 'recentSales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,pos_terminal,transfer',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);
        $qty = (int) $request->quantity;

        if ($variant->stock_quantity < $qty) {
            return redirect()->back()->withInput()->with('error', "Only {$variant->stock_quantity} available in stock.");
        }

        $saleId = null;
        DB::transaction(function () use ($variant, $request, $qty, &$saleId) {
            $prev = $variant->stock_quantity;
            $newStock = $prev - $qty;

            $variant->update(['stock_quantity' => $newStock]);

            // Create Sales Record for walk-in ledger
            $sale = SalesRecord::create([
                'product_variant_id' => $variant->id,
                'date' => now()->toDateString(),
                'item_description' => "{$variant->product->name} ({$variant->size} - {$variant->color})",
                'quantity' => $qty,
                'unit_price' => $request->amount / $qty,
                'amount' => $request->amount,
                'source' => 'manual',
                'status' => 'confirmed',
                'created_by' => Auth::id(),
                'notes' => 'Walk-in sale paid via '.strtoupper($request->payment_method).($request->customer_name ? " to {$request->customer_name}" : '').($request->notes ? " | {$request->notes}" : ''),
            ]);

            $saleId = $sale->id;

            // Record stock movement
            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => 'out',
                'quantity' => $qty,
                'previous_stock' => $prev,
                'new_stock' => $newStock,
                'reason' => 'manual_walkin_sale',
                'reference_type' => 'SalesRecord',
                'reference_id' => $sale->id,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.sales.create')
            ->with('success', "In-store boutique sale recorded for {$variant->product->name}. Stock updated.")
            ->with('receipt_sale_id', $saleId);
    }
}
