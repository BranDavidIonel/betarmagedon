<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\ScrapeLinksApiSitesController;
use App\Http\Controllers\API\ScrapeMatchesApiController;
use Illuminate\Http\Request;

class TestsApiController extends Controller
{
    public function apiTests(Request $request)
    {
        return view('api-tests', [
            'scrapedData' => [],
            'apiStructureJson' => ''
        ]);
    }
    //region scraped matches
    public function showBetanoMatches()
    {
        // Simulate a request to ScrapeMatchesApiController
        $requestBetanoLigue1 = Request::create('/api/scrape-betano', 'GET', [
            'url' => '/sport/fotbal/romania/liga-1/17088/?bt=matchresult'
        ]);


        // Instantiate the ScrapeMatchesApiController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeMatchesApiController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->scrapeBetanoMatch($requestBetanoLigue1);
        $data = $response->getData(true); // Convert JsonResponse to an array
        $apiStructure = $this->getStructureScrapeMatches();

        return view('api-tests', [
            'scrapedData' => $data['data'] ?? [],
            'apiStructureJson' => $apiStructure
        ]);
    }
    public function showSuperbetMatches()
    {
        // Simulate a request to ScrapeMatchesApiController
        $requestBetanoLigue1 = Request::create('/api/scrape-superbet', 'GET', [
            'url' => '/pariuri-sportive/fotbal/romania/superliga/toate?ct=m'
        ]);

        // Instantiate the ScrapeMatchesApiController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeMatchesApiController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->scrapeSuperbetMatch($requestBetanoLigue1);
        $data = $response->getData(true); // Convert JsonResponse to an array
        $apiStructure = $this->getStructureScrapeMatches();

        return view('api-tests', [
            'scrapedData' => $data['data'] ?? [],
            'apiStructureJson' => $apiStructure
        ]);
    }
    public function showCasapariurilorMatches()
    {
        // Simulate a request to ScrapeMatchesApiController
        $requestBetanoLigue1 = Request::create('/api/scrape-casapariurilor', 'GET', [
            'url' => '/pariuri-online/fotbal/romania-3/romania-1?filter=all&tab=matches'
        ]);

        // Instantiate the ScrapeMatchesApiController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeMatchesApiController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->scrapeCasaPariurilorMatch($requestBetanoLigue1);
        $data = $response->getData(true); // Convert JsonResponse to an array
        $apiStructure = $this->getStructureScrapeMatches();


        return view('api-tests', [
            'scrapedData' => $data['data'] ?? [],
            'apiStructureJson' => $apiStructure
        ]);
    }

    /**
     * @return false|string
     */
    private function getStructureScrapeMatches(): string|false
    {
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
        return $apiStructure;
    }
    //endregion
    //region scraped links
    public function showBetanoLinksData()
    {
        // Simulate a request to ScrapeLinksApiController
        $requestBetano = Request::create('/api/get-links/betano');

        // Instantiate the ScrapeLinksApiSitesController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeLinksApiSitesController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->getLinksForBetano($requestBetano);
        $data = $this->getJsonDecodeDataForLinksScraped($response);
        $apiStructure = $this->getStructureScrapeLinks();

        return view('api-tests', [
            'scrapedData' => $data['data'] ?? [],
            'apiStructureJson' => $apiStructure
        ]);
    }
    public function showSuperbetLinksData()
    {
        // Simulate a request to ScrapeLinksApiController
        $requestBetano = Request::create('/api/get-links/superbet');

        // Instantiate the ScrapeLinksApiSitesController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeLinksApiSitesController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->getLinksForSuperbet($requestBetano);
        $data = $this->getJsonDecodeDataForLinksScraped($response);
        $apiStructure = $this->getStructureScrapeLinks();

        return view('api-tests', [
            'scrapedData' => $data['data'] ?? [],
            'apiStructureJson' => $apiStructure
        ]);
    }
    public function showCasapariurilorLinksData()
    {
        // Simulate a request to ScrapeLinksApiController
        $requestBetano = Request::create('/api/get-links/casapariurilor');

        // Instantiate the ScrapeLinksApiSitesController with dependency injection
        $controllerScrapeAPI = app()->make(ScrapeLinksApiSitesController::class);

        // Call the method and get the JSON response
        $response = $controllerScrapeAPI->getLinksForCasapariurilor($requestBetano);
        $data = $this->getJsonDecodeDataForLinksScraped($response);
        $apiStructure = $this->getStructureScrapeLinks();

        return view('api-tests', [
            'scrapedData' => $data['data'] ?? [],
            'apiStructureJson' => $apiStructure
        ]);
    }
    private function getStructureScrapeLinks(): string|false
    {
        // Define a static API structure documentation
        $apiStructure = json_encode([
            [
                "leagueName" => "string",
                "link" => "string (format: https://site-name/..)",
                "countryName" => "string"
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $apiStructure;
    }
    private function getJsonDecodeDataForLinksScraped($response): mixed
    {
        $data = json_decode(str_replace('\/', '/', json_encode($response->getData(true))), true);
        return $data; // Convert JsonResponse to an array
    }


    //endregion


}
