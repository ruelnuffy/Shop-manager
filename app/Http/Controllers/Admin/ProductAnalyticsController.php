<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductAnalyticsController extends Controller
{
    /**
     * Display storefront product link click analytics and best-selling suggestions.
     */
    public function index(): View
    {
        $salesByProduct = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'product_variants.product_id',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.subtotal) as sales_revenue')
            )
            ->groupBy('product_variants.product_id')
            ->get()
            ->keyBy('product_id');

        $products = Product::with(['category', 'variants'])
            ->get()
            ->map(function (Product $product) use ($salesByProduct): array {
                $salesRow = $salesByProduct->get($product->id);
                $clicks = (int) ($product->link_clicks ?? 0);
                $unitsSold = (int) ($salesRow->units_sold ?? 0);
                $revenue = (float) ($salesRow->sales_revenue ?? 0);
                $conversionRate = $clicks > 0 ? round(($unitsSold / $clicks) * 100, 1) : 0.0;
                $score = ($unitsSold * 5) + $clicks;

                $suggestionLabel = match (true) {
                    $unitsSold >= 3 && $clicks >= 5 => 'Top Best Seller',
                    $unitsSold > 0 => 'Proven Seller',
                    $clicks >= 5 => 'High Interest — Promote',
                    $clicks > 0 => 'Getting Clicks',
                    default => 'Needs Promotion',
                };

                return [
                    'product' => $product,
                    'clicks' => $clicks,
                    'units_sold' => $unitsSold,
                    'revenue' => $revenue,
                    'conversion_rate' => $conversionRate,
                    'score' => $score,
                    'suggestion_label' => $suggestionLabel,
                ];
            })
            ->sortByDesc(fn (array $row): array => [$row['score'], $row['clicks'], $row['units_sold']])
            ->values();

        $totalClicks = $products->sum('clicks');
        $totalUnitsSold = $products->sum('units_sold');
        $totalRevenue = $products->sum('revenue');
        $topClickedProduct = $products->sortByDesc('clicks')->first();
        $topSellingProduct = $products->sortByDesc('units_sold')->first();
        $suggestedProducts = $products->take(3);

        return view('admin.products.analytics', compact(
            'products',
            'suggestedProducts',
            'totalClicks',
            'totalUnitsSold',
            'totalRevenue',
            'topClickedProduct',
            'topSellingProduct'
        ));
    }
}
