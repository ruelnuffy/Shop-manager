<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'activeVariants'])
            ->where('status', 'active');

        // Search query
        if ($request->filled('q')) {
            $searchTerm = '%'.trim($request->q).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price filtering
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->max_price);
        }

        // Size filter
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size', $request->size)->where('is_active', true);
            });
        }

        // Color filter
        if ($request->filled('color')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('color', 'like', '%'.$request->color.'%')->where('is_active', true);
            });
        }

        // In-stock only
        if ($request->boolean('in_stock')) {
            $query->whereHas('variants', function ($q) {
                $q->where('stock_quantity', '>', 0)->where('is_active', true);
            });
        }

        // Has video only
        if ($request->boolean('has_video')) {
            $query->whereNotNull('video_url');
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(9)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order')->get();

        // Distinct available sizes and colors for filter badges
        $availableSizes = ProductVariant::where('is_active', true)
            ->whereNotNull('size')
            ->distinct()
            ->pluck('size');

        $availableColors = ProductVariant::where('is_active', true)
            ->whereNotNull('color')
            ->distinct()
            ->pluck('color');

        return view('store.products.index', compact(
            'products',
            'categories',
            'availableSizes',
            'availableColors'
        ));
    }

    public function show(string $slug): View
    {
        $product = Product::with(['category', 'activeVariants'])->where('slug', $slug)->firstOrFail();

        $relatedProducts = Product::with(['category', 'activeVariants'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(3)
            ->get();

        return view('store.products.show', compact('product', 'relatedProducts'));
    }
}
