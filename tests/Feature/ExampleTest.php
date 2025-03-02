<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Mockery;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Mock the Log::info() method to prevent it from being called more than expected
//        Log::shouldReceive('info')
//            ->times(4)  // Expect it to be called exactly 4 times (adjust this as per your requirement)
//            ->with(Mockery::any());  // This can be replaced with more specific argument checks if needed
//
//        $response = $this->get('/');
//
//        $response->assertStatus(200);
    }
}
