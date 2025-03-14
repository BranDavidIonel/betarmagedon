<?php

namespace Tests\Unit;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrapeBetanoMetchTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_it_can_scrape_betano_with_dynamic_url(): void
    {
        // The dynamic part of the URL to test
        $urlPath = '/sport/fotbal/romania/liga-1/17088/';

        // Make a GET request with the dynamic part of the URL as a query parameter
        $response = $this->getJson('/api/scrape-betano?url=' . urlencode($urlPath));

        // Assert that the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert that the response contains the expected structure
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'team1Name',
                    'team2Name',
                    'odds',
                    'startTime',
                    'isLive',
                    'urlSearch',
                ],
            ],
        ]);
    }

    public function test_it_should_return_error_on_invalid_url(): void
    {
        // Simulate an error with an invalid URL part
        $urlPath = '/invalid/path/12345/';

        // Make a GET request with the invalid dynamic part of the URL
        $response = $this->getJson('/api/scrape-betano?url=' . urlencode($urlPath));

        // Assert that the response status is 500 (Internal Server Error)
        $response->assertStatus(500);

        // Assert that the error message is as expected
        $response->assertJson([
            'success' => false,
            'message' => 'An error occurred while processing the request',
        ]);
    }
//    public function test_the_application_returns_a_successful_response(): void
//    {
//        // Mock the Log::info() method to prevent it from being called more than expected
////        Log::shouldReceive('info')
////            ->times(4)  // Expect it to be called exactly 4 times (adjust this as per your requirement)
////            ->with(Mockery::any());  // This can be replaced with more specific argument checks if needed
////
////        $response = $this->get('/');
////
////        $response->assertStatus(200);
//    }
}
