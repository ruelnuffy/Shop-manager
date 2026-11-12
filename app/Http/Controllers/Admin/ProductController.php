<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\CurrentTenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'variants'])->latest();

        if ($request->filled('q')) {
            $term = '%'.trim($request->q).'%';
            $query->where('name', 'like', $term);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Adding products is restricted to store administrators.');
        }

        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId && Category::where('tenant_id', $tenantId)->count() === 0) {
            $defaultCategories = ['General', 'Clothing', 'Shoes', 'Accessories', 'Bags'];
            foreach ($defaultCategories as $catName) {
                Category::firstOrCreate([
                    'tenant_id' => $tenantId,
                    'name' => $catName,
                ], [
                    'slug' => Str::slug($catName).'-'.$tenantId,
                    'is_active' => true,
                ]);
            }
        }

        $categories = Category::where('is_active', true)->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Adding products is restricted to store administrators.');
        }

        $tenantId = app(CurrentTenant::class)->id();

        if ($request->filled('new_category_name') || (! is_numeric($request->input('category_id')) && $request->filled('category_id'))) {
            $rawName = (string) ($request->input('new_category_name') ?: $request->input('category_id'));
            $categoryName = trim(preg_replace('/^new:\s*/i', '', $rawName));
            $category = Category::firstOrCreate([
                'tenant_id' => $tenantId,
                'name' => $categoryName,
            ], [
                'slug' => Str::slug($categoryName).'-'.Str::random(4),
                'is_active' => true,
            ]);
            $request->merge(['category_id' => $category->id]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'featured_image' => 'nullable|string|max:500',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'video_url' => 'nullable|string|max:500',
            'brand_tags' => 'nullable|string',
            'status' => 'nullable|in:active,draft,archived',
            'variants' => 'required|array|min:1',
            'variants.*.size' => 'required|string|max:50',
            'variants.*.color' => 'required|string|max:50',
            'variants.*.sku' => [
                'required',
                'string',
                'max:100',
                'distinct',
                Rule::unique('product_variants', 'sku')->where('tenant_id', app(CurrentTenant::class)->id()),
            ],
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.price_override' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->name);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-'.Str::random(5);
            }

            $tags = null;
            if ($request->filled('brand_tags')) {
                $tags = array_map('trim', explode(',', $request->brand_tags));
            }

            $tenantId = app(CurrentTenant::class)->id() ?? 'default';

            $image = null;
            if ($request->hasFile('featured_image_file')) {
                $path = $request->file('featured_image_file')->store("tenants/{$tenantId}/products", 'public');
                $image = '/storage/'.$path;
            } elseif ($request->filled('featured_image')) {
                $image = $request->featured_image;
            } else {
                $image = 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=800&q=80';
            }

            $galleryImages = [$image];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $galleryPath = $file->store("tenants/{$tenantId}/products/gallery", 'public');
                    $galleryImages[] = '/storage/'.$galleryPath;
                }
            }

            $status = $request->filled('status') ? $request->status : ($request->boolean('is_active', true) ? 'active' : 'draft');

            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'category_id' => $request->category_id,
                'base_price' => $request->base_price,
                'description' => $request->description,
                'featured_image' => $image,
                'video_url' => $request->video_url,
                'images' => array_values(array_unique($galleryImages)),
                'brand_tags' => $tags,
                'status' => $status,
                'is_featured' => $request->boolean('is_featured'),
            ]);

            foreach ($request->variants as $variantData) {
                $stock = (int) ($variantData['stock'] ?? $variantData['stock_quantity'] ?? 0);
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variantData['size'],
                    'color' => $variantData['color'],
                    'sku' => strtoupper($variantData['sku']),
                    'stock_quantity' => $stock,
                    'price_override' => ! empty($variantData['price_override']) ? $variantData['price_override'] : null,
                    'is_active' => true,
                ]);

                if ($stock > 0) {
                    StockMovement::create([
                        'product_variant_id' => $variant->id,
                        'type' => 'in',
                        'quantity' => $stock,
                        'previous_stock' => 0,
                        'new_stock' => $stock,
                        'reason' => 'initial_product_creation',
                        'reference_type' => 'Manual',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully with pictures and variants.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Error creating product: '.$e->getMessage());
        }
    }

    public function edit(Product $product): View
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Editing products is restricted to store administrators.');
        }

        $categories = Category::all();
        $product->load('variants');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Editing products is restricted to store administrators.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'featured_image' => 'nullable|string|max:500',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
            'video_url' => 'nullable|string|max:500',
            'status' => 'required|in:active,draft,archived',
        ]);

        $tags = null;
        if ($request->filled('brand_tags')) {
            $tags = array_map('trim', explode(',', $request->brand_tags));
        }

        $tenantId = app(CurrentTenant::class)->id() ?? ($product->tenant_id ?? 'default');

        $featuredImage = $product->featured_image;
        if ($request->hasFile('featured_image_file')) {
            $path = $request->file('featured_image_file')->store("tenants/{$tenantId}/products", 'public');
            $featuredImage = '/storage/'.$path;
        } elseif ($request->filled('featured_image')) {
            $featuredImage = $request->featured_image;
        }

        $currentImages = $product->images ?: [$featuredImage];

        if ($request->filled('remove_images')) {
            $currentImages = array_values(array_diff($currentImages, $request->remove_images));
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $galleryPath = $file->store("tenants/{$tenantId}/products/gallery", 'public');
                $currentImages[] = '/storage/'.$galleryPath;
            }
        }

        if (! in_array($featuredImage, $currentImages)) {
            array_unshift($currentImages, $featuredImage);
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'base_price' => $request->base_price,
            'description' => $request->description,
            'featured_image' => $featuredImage,
            'images' => array_values(array_unique($currentImages)),
            'video_url' => $request->has('video_url') ? $request->video_url : $product->video_url,
            'brand_tags' => $tags ?? $product->brand_tags,
            'status' => $request->status,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Unauthorized. Deleting products is restricted to store administrators.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
