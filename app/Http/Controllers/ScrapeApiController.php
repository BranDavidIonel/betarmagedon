<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ScrapeSitesService;
use Illuminate\Support\Facades\Log;

class ScrapeApiController extends Controller
{
    const BASE_DOMAIN_URL_BETANO = 'https://ro.betano.com';
    const BASE_DOMAIN_URL_SUPERBET = 'https://superbet.ro';
    const BASE_DOMAIN_URL_CASAPARIURILOR = 'https://www.casapariurilor.ro';

    private ScrapeSitesService $scrapeSitesService;

    public function __construct(ScrapeSitesService $scrapeSitesService)
    {
        $this->scrapeSitesService = $scrapeSitesService;
    }

    public function scrapeBetanoMatch(Request $request)
    {
        try {
            // Get the dynamic part of the URL from the request
            $urlPath = $request->input('url'); // e.g., "/sport/fotbal/romania/liga-1/17088/"

            // Concatenate the domain with the dynamic URL path
            $urlSearchMatches = self::BASE_DOMAIN_URL_BETANO . $urlPath;

            // Call the scrape method from the service
            $data = $this->scrapeSitesService->scrapeBetanoWithScriptMethod($urlSearchMatches);

            // Return the results as JSON
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return $this->getError($e, 'betano');
        }
    }
    public function scrapeSuperbetMatch(Request $request)
    {
        try {
            // Get the dynamic part of the URL from the request
            $urlPath = $request->input('url'); // e.g., "/pariuri-sportive/fotbal/romania/superliga/toate"

            // Concatenate the domain with the dynamic URL path
            $urlSearchMatches = self::BASE_DOMAIN_URL_SUPERBET . $urlPath;

            // Call the scrape method from the service
            $data = $this->scrapeSitesService->scrapeSuperbetWithClassNameMethod($urlSearchMatches);

            // Return the results as JSON
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return $this->getError($e, 'casapariurilor');
        }
    }
    public function scrapeCasaPariurilorMatch(Request $request)
    {
        try {
            // Get the dynamic part of the URL from the request
            $urlPath = $request->input('url'); // e.g., "/pariuri-online/fotbal/romania-1"

            // Concatenate the domain with the dynamic URL path
            $urlSearchMatches = self::BASE_DOMAIN_URL_CASAPARIURILOR . $urlPath;

            // Call the scrape method from the service
            $data = $this->scrapeSitesService->scrapeCasaPariurilorWithClassNameMethod($urlSearchMatches);

            // Return the results as JSON
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            return $this->getError($e, 'casapariurilor');
        }
    }

    private function getError(\Exception $e, string $typeSite): \Illuminate\Http\JsonResponse
    {
        Log::error("Error during $typeSite scraping", ['exception' => $e->getMessage()]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing the request',
        ], 500);
    }

}
