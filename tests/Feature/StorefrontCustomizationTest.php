<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontCustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->tenant = Tenant::where('slug', 'jessis-classiq-store')->first();
        $this->admin = User::where('email', 'admin@jessiclassiq.com')->first();
        $this->staff = User::where('email', 'staff@jessiclassiq.com')->first();
    }

    public function test_guest_cannot_access_storefront_customization(): void
    {
        $response = $this->get('/admin/storefront-customization');
        $response->assertRedirect('/login');
    }

    public function test_staff_cannot_access_storefront_customization(): void
    {
        $response = $this->actingAs($this->staff)->get('/admin/storefront-customization');
        $response->assertStatus(403);
    }

    public function test_store_admin_can_view_storefront_customization_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/storefront-customization');

        $response->assertStatus(200);
        $response->assertSee('Storefront &amp; Hero Customization', false);
        $response->assertSee('Main Hero Visual Showcase');
        $response->assertSee('Featuring Images &amp; Promo Highlights', false);
    }

    public function test_store_admin_can_update_hero_and_featuring_images_via_url(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/storefront-customization', [
            'brand_name' => 'Luxe Atelier Boutique',
            'tagline' => 'Handcrafted Excellence',
            'hero_title' => 'Spring Runway 2026 Collection',
            'hero_subtitle' => 'Exclusive Ready-to-Wear Pieces',
            'hero_badge' => 'Jos Atelier Exclusive',
            'hero_image_url' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80',

            'featuring_1_title' => 'Signature Evening Gowns',
            'featuring_1_subtitle' => 'Pure silk embroidered gowns',
            'featuring_1_badge' => 'Drop 01',
            'featuring_1_image_url' => 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?auto=format&fit=crop&w=800&q=80',
            'featuring_1_link' => '/products?category=clothes',

            'featuring_2_title' => 'Atelier Leather Bags',
            'featuring_2_subtitle' => 'Hand-stitched leather silhouettes',
            'featuring_2_badge' => 'Drop 02',
            'featuring_2_image_url' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80',
            'featuring_2_link' => '/products?category=bags',
        ]);

        $response->assertRedirect(route('admin.storefront.edit'));
        $response->assertSessionHas('status');

        $this->tenant->refresh();

        $this->assertEquals('Spring Runway 2026 Collection', $this->tenant->setting('storefront.hero_title'));
        $this->assertEquals('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80', $this->tenant->setting('storefront.hero_image'));

        $featuring = $this->tenant->setting('storefront.featuring');
        $this->assertCount(3, $featuring);
        $this->assertEquals('Signature Evening Gowns', $featuring[0]['title']);
        $this->assertEquals('Atelier Leather Bags', $featuring[1]['title']);

        // Check storefront renders these custom hero and featuring images
        $storefrontResponse = $this->actingAs($this->admin)->get('/storefront');
        $storefrontResponse->assertStatus(200);
        $storefrontResponse->assertSee('Spring Runway 2026 Collection');
        $storefrontResponse->assertSee('Signature Evening Gowns');
        $storefrontResponse->assertSee('Atelier Leather Bags');
    }

    public function test_store_admin_can_upload_hero_and_featuring_files(): void
    {
        Storage::fake('public');

        $heroFile = UploadedFile::fake()->image('custom-hero.jpg', 1600, 900);
        $featFile = UploadedFile::fake()->image('custom-feat.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->put('/admin/storefront-customization', [
            'hero_title' => 'Uploaded Hero Banner',
            'hero_image_file' => $heroFile,
            'featuring_1_title' => 'Uploaded Feature Tile',
            'featuring_1_image_file' => $featFile,
        ]);

        $response->assertRedirect(route('admin.storefront.edit'));

        $this->tenant->refresh();

        $heroPath = $this->tenant->setting('storefront.hero_image');
        $this->assertNotNull($heroPath);
        $this->assertStringStartsWith('/storage/tenants/', $heroPath);

        $rawRelativePath = str_replace('/storage/', '', $heroPath);
        Storage::disk('public')->assertExists($rawRelativePath);
    }
}
