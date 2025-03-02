<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Log;
use App\Services\ConfigWebDriverService;
use App\Services\SaveMatchService;
use PHPUnit\Framework\TestCase;
use App\Http\Controllers\FootballDataController;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FetchDataTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test fetchData() returns valid data without inserting data into the database.
     */
    public function test_fetch_data_returns_valid_data(): void
    {
        // Mock the Log facade to prevent real logging during the test
        Log::shouldReceive('info')->once();
        Log::shouldReceive('alert')->once();
        Log::shouldReceive('error')->once(); // Mocking the error method as well
        // Instantiate the controller
        $footballDataController = new FootballDataController(new ConfigWebDriverService, new SaveMatchService);

        try {
            // Act - Call the fetchData() method
            $response = $footballDataController->fetchData();

            // Assert - Check if the response is an array
            $this->assertIsArray($response, "fetchData() did not return an array.");

            // Assert - Check if the response is not empty
            $this->assertNotEmpty($response, "fetchData() returned an empty array.");

            // Assert - Ensure at least one league has matches
            $hasMatches = false;
            foreach ($response as $leagueData) {
                if (!empty($leagueData['betano_matches']) || !empty($leagueData['suberbet_matches']) || !empty($leagueData['casapariurilor_matches'])) {
                    $hasMatches = true;
                    break;
                }
            }

            // Verify that matches exist
            $this->assertTrue($hasMatches, "fetchData() did not return any match data.");
        } catch (\Exception $e) {
            // In case of an error, indicate that the test has failed
            $this->fail("Test failed due to exception: " . $e->getMessage());
        }
    }
}
