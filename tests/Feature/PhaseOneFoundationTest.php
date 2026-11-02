<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ShopLocation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_welcome_page_renders_with_brand_identity(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Jessi&#039;s ClassiQ Store', false);
        $response->assertSee('Clothes');
        $response->assertSee('Bags');
        $response->assertSee('Shoes');
    }

    public function test_admin_user_can_authenticate_and_access_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@jessiclassiq.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $admin = User::where('email', 'admin@jessiclassiq.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());

        $dashboardResponse = $this->actingAs($admin)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('admin');
        $dashboardResponse->assertSee($admin->name);
    }

    public function test_new_customer_can_register(): void
    {
        $email = 'newcustomer_'.uniqid().'@example.com';

        $response = $this->post('/register', [
            'name' => 'Chioma Eze',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
    }

    public function test_database_models_and_relationships(): void
    {
        $this->assertGreaterThan(0, Category::count());
        $this->assertGreaterThan(0, Product::count());
        $this->assertGreaterThan(0, ShopLocation::count());

        $product = Product::with(['category', 'variants'])->first();
        $this->assertNotNull($product);
        $this->assertNotNull($product->category);
        $this->assertGreaterThan(0, $product->variants->count());
        $this->assertGreaterThan(0, $product->totalStock());
    }
}
