<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\CurrentTenant;
use App\Services\StoreNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ManualSaleController extends Controller
{
    public function create(): View
    {
        $tenantId = app(CurrentTenant::class)->id();

        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        $recentSales = SalesRecord::with(['variant.product', 'creator'])
            ->where('source', 'manual')
            ->latest()
            ->take(10)
            ->get();

        // Customer profiles for auto-fill / quick lookup
        $customers = User::where('tenant_id', $tenantId)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'name', 'phone')
            ->take(50)
            ->get();

        // End-of-Day Register Takings (Z-Report)
        $todaySales = SalesRecord::where('source', 'manual')
            ->whereDate('date', today())
            ->get();

        $cashTotal = 0.0;
        $transferTotal = 0.0;
        $cardTotal = 0.0;

        foreach ($todaySales as $s) {
            $notes = strtoupper($s->notes ?? '');
            if (str_contains($notes, 'SPLIT')) {
                if (preg_match('/CASH:\s*₦?([0-9,\.]+)/i', $notes, $m)) {
                    $cashTotal += (float) str_replace(',', '', $m[1]);
                }
                if (preg_match('/TRANSFER:\s*₦?([0-9,\.]+)/i', $notes, $m)) {
                    $transferTotal += (float) str_replace(',', '', $m[1]);
                }
                if (preg_match('/(POS CARD|POS_TERMINAL):\s*₦?([0-9,\.]+)/i', $notes, $m)) {
                    $cardTotal += (float) str_replace(',', '', $m[1]);
                }
            } elseif (str_contains($notes, 'CASH')) {
                $cashTotal += (float) $s->amount;
            } elseif (str_contains($notes, 'TRANSFER')) {
                $transferTotal += (float) $s->amount;
            } elseif (str_contains($notes, 'POS_TERMINAL') || str_contains($notes, 'POS CARD')) {
                $cardTotal += (float) $s->amount;
            }
        }

        $dailyTakings = [
            'date' => today()->format('d M Y'),
            'transactions_count' => $todaySales->count(),
            'units_sold' => (int) $todaySales->sum('quantity'),
            'total_amount' => (float) $todaySales->sum('amount'),
            'cash_total' => $cashTotal,
            'transfer_total' => $transferTotal,
            'card_total' => $cardTotal,
        ];

        return view('admin.sales.create', compact('variants', 'recentSales', 'customers', 'dailyTakings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,pos_terminal,transfer,split',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'notes' => 'nullable|string|max:500',
            'password' => 'nullable|string',
            'split_cash_amount' => 'nullable|numeric|min:0',
            'split_transfer_amount' => 'nullable|numeric|min:0',
        ]);

        if ($request->filled('password')) {
            $userPasswordValid = Hash::check($request->password, $request->user()->password);
            $tenant = app(CurrentTenant::class)->get();
            $posPin = $tenant?->setting('pos.pin');
            $pinValid = $posPin && $request->password === (string) $posPin;

            if (! $userPasswordValid && ! $pinValid) {
                return redirect()->back()->withInput()->with('error', 'Incorrect cashier password. Transaction not authorized.');
            }
        }

        $variant = ProductVariant::with('product')->findOrFail($request->variant_id);
        $qty = (int) $request->quantity;

        if ($variant->stock_quantity < $qty) {
            return redirect()->back()->withInput()->with('error', "Only {$variant->stock_quantity} available in stock.");
        }

        $saleId = null;
        $recordedSale = null;
        DB::transaction(function () use ($variant, $request, $qty, &$saleId, &$recordedSale) {
            $prev = $variant->stock_quantity;
            $newStock = $prev - $qty;

            $variant->update(['stock_quantity' => $newStock]);

            // Construct payment method label
            $pm = strtoupper($request->payment_method);
            if ($request->payment_method === 'split') {
                $cashPart = number_format((float) ($request->split_cash_amount ?? 0));
                $transPart = number_format((float) ($request->split_transfer_amount ?? 0));
                $pm = "SPLIT (Cash: ₦{$cashPart}, Transfer: ₦{$transPart})";
            }

            // Customer info format
            $custInfo = '';
            if ($request->filled('customer_name') && $request->filled('customer_phone')) {
                $custInfo = " to {$request->customer_name} ({$request->customer_phone})";
            } elseif ($request->filled('customer_name')) {
                $custInfo = " to {$request->customer_name}";
            } elseif ($request->filled('customer_phone')) {
                $custInfo = " to ({$request->customer_phone})";
            }

            $notes = "Walk-in sale paid via {$pm}{$custInfo}".($request->notes ? " | {$request->notes}" : '');

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
                'notes' => $notes,
            ]);

            $saleId = $sale->id;
            $recordedSale = $sale;

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

        $tenant = app(CurrentTenant::class)->get();
        if ($tenant && $recordedSale) {
            app(StoreNotificationService::class)->notifyStaffSale($tenant, $recordedSale, Auth::user());
        }

        return redirect()->route('admin.sales.create')
            ->with('success', "In-store boutique sale recorded for {$variant->product->name}. Stock updated.")
            ->with('receipt_sale_id', $saleId);
    }
}
