<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@jessiclassiq.com')->first();
        $this->category = Category::first();
    }

    public function test_admin_can_create_product_with_uploaded_pictures_and_gallery(): void
    {
        Storage::fake('public');

        $coverFile = UploadedFile::fake()->image('emerald_gown.jpg', 800, 1000);
        $detailFile1 = UploadedFile::fake()->image('detail_back.jpg', 800, 1000);
        $detailFile2 = UploadedFile::fake()->image('detail_fabric.jpg', 800, 1000);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Emerald Satin Slip Dress',
            'category_id' => $this->category->id,
            'base_price' => 58000,
            'description' => 'Pure heavy silk bias-cut slip dress in deep emerald.',
            'featured_image_file' => $coverFile,
            'gallery_images' => [$detailFile1, $detailFile2],
            'status' => 'active',
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Emerald',
                    'sku' => 'EMR-SLIP-M',
                    'stock' => 8,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Emerald Satin Slip Dress')->first();
        $this->assertNotNull($product);

        // Assert featured image stored and begins with /storage/tenants/{tenant_id}/products/
        $this->assertStringStartsWith('/storage/tenants/'.$product->tenant_id.'/products/', $product->featured_image);

        // Extract relative storage path
        $featuredStoragePath = str_replace('/storage/', '', $product->featured_image);
        Storage::disk('public')->assertExists($featuredStoragePath);

        // Assert images array contains featured image and 2 gallery images
        $this->assertCount(3, $product->images);

        foreach ($product->images as $img) {
            $storagePath = str_replace('/storage/', '', $img);
            Storage::disk('public')->assertExists($storagePath);
        }
    }

    public function test_admin_can_update_product_picture_and_manage_gallery(): void
    {
        Storage::fake('public');

        $initialCover = UploadedFile::fake()->image('initial_cover.jpg');
        $initialGallery = UploadedFile::fake()->image('initial_gallery.jpg');

        $pathCover = $initialCover->store('products', 'public');
        $pathGallery = $initialGallery->store('products/gallery', 'public');

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Rose Silk Trench Coat',
            'slug' => 'rose-silk-trench-coat',
            'base_price' => 95000,
            'featured_image' => '/storage/'.$pathCover,
            'images' => ['/storage/'.$pathCover, '/storage/'.$pathGallery],
            'status' => 'active',
        ]);

        // Upload replacement cover and remove old gallery photo
        $newCover = UploadedFile::fake()->image('new_trench_cover.jpg');
        $newGallery = UploadedFile::fake()->image('trench_belt_close_up.jpg');

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Rose Silk Trench Coat - Extended Cut',
            'category_id' => $this->category->id,
            'base_price' => 110000,
            'status' => 'active',
            'featured_image_file' => $newCover,
            'gallery_images' => [$newGallery],
            'remove_images' => ['/storage/'.$pathGallery],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Rose Silk Trench Coat - Extended Cut', $product->name);
        $this->assertNotEquals('/storage/'.$pathCover, $product->featured_image);
        $this->assertStringStartsWith('/storage/tenants/'.$product->tenant_id.'/products/', $product->featured_image);

        // The old gallery item should be removed
        $this->assertNotContains('/storage/'.$pathGallery, $product->images);
    }
}
