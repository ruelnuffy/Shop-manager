<?php

namespace Tests\Feature;

use App\Services\CurrentTenant;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        // Explicitly enable HTTP request context so BelongsToTenant scope is enforced
        app(CurrentTenant::class)->setHttpRequestContext(true);
    }

    public function test_public_can_view_stores_directory_page(): void
    {
        $response = $this->get('/stores');

        $response->assertStatus(200);
        $response->assertSee('Stores on');
        $response->assertSee("Jessi's ClassiQ Store");
        $response->assertSee('Visit Store');
        $response->assertDontSee('Admin &rarr;', false);
    }

    public function test_public_can_search_stores_by_name(): void
    {
        $response = $this->get('/stores?q=Jessi');

        $response->assertStatus(200);
        $response->assertSee("Jessi's ClassiQ Store");
    }

    public function test_empty_search_shows_no_stores_found_state(): void
    {
        $response = $this->get('/stores?q=NonExistentStore12345');

        $response->assertStatus(200);
        $response->assertSee('No stores found');
    }
}
