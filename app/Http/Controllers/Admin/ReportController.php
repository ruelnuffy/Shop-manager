<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\SalesRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // Online sales revenue
        $onlineRevenue = Order::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_amount');

        $onlineOrdersCount = Order::where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // In-store & Digitized sales records
        $manualSalesRevenue = SalesRecord::where('status', 'confirmed')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->sum('amount');

        $manualSalesCount = SalesRecord::where('status', 'confirmed')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->count();

        $combinedRevenue = $onlineRevenue + $manualSalesRevenue;

        // Inventory Valuation
        $variants = ProductVariant::with('product')->where('is_active', true)->get();
        $totalUnitsInStock = $variants->sum('stock_quantity');
        $totalValuation = 0;
        foreach ($variants as $v) {
            $totalValuation += $v->effective_price * $v->stock_quantity;
        }

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'onlineRevenue',
            'onlineOrdersCount',
            'manualSalesRevenue',
            'manualSalesCount',
            'combinedRevenue',
            'totalUnitsInStock',
            'totalValuation'
        ));
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $filename = "jessi_sales_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Type', 'Identifier', 'Date', 'Description / Recipient', 'Amount (NGN)', 'Status']);

            // Online Orders
            $orders = Order::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->get();

            foreach ($orders as $order) {
                fputcsv($file, [
                    'Online Order',
                    $order->order_number,
                    $order->created_at->toDateTimeString(),
                    $order->shipping_name.' ('.$order->shipping_city.')',
                    $order->total_amount,
                    $order->payment_status,
                ]);
            }

            // Sales Records (In-store & OCR)
            $sales = SalesRecord::whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->get();

            foreach ($sales as $sale) {
                fputcsv($file, [
                    ucfirst($sale->source).' Sale',
                    'SR-'.$sale->id,
                    $sale->date->toDateString(),
                    $sale->item_description.' (Qty: '.$sale->quantity.')',
                    $sale->amount,
                    $sale->status,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
