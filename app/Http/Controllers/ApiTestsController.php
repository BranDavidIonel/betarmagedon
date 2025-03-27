<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ScrapeSitesService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ApiTestsController extends Controller
{
    public function apiTests(Request $request)
    {
        return view('api-tests', [
            'betanoData' => [],
            'betanoJson' => ''
        ]);
    }
    public function showBetanoData()
    {
        // Simulate a request to ScrapeApiController
        $requestBetanoLigue1 = Request::create('/api/scrape-betano', 'GET', [
            'url' => '/sport/fotbal/romania/liga-1/17088'
        ]);

        // Instantiate the ScrapeApiController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeApiController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->scrapeBetanoMatch($requestBetanoLigue1);
        $data = $response->getData(true); // Convert JsonResponse to an array

        // Define a static API structure documentation
        $apiStructure = json_encode([
            "match_name" => [ // Example: "FC Unirea 2004 Slobozia - Petrolul Ploiești"
                "team1Name" => "string",
                "team2Name" => "string",
                "odds" => [
                    "1" => "float",
                    "x" => "float",
                    "2" => "float"
                ],
                "startTime" => "string (format: dd-mm-yyyy hh:mm)",
                "isLive" => "boolean",
                "urlSearch" => "string (URL)"
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return view('api-tests', [
            'betanoData' => $data['data'] ?? [],
            'betanoJson' => $apiStructure
        ]);
    }

}
