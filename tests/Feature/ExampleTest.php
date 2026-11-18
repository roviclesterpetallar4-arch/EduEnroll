<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('class="bg-ncc-maroon text-white border-b-4 border-ncc-gold shadow-md sticky top-0 z-40"', false)
            ->assertSee('<footer', false)
            ->assertSee('images/ncc-campus.jpg')
            ->assertSee('Course Catalog')
            ->assertSee('contact', false);

    }
}
