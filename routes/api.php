<?php

use App\Http\Controllers\API\ScrapeLinksApiSitesController;
use App\Http\Controllers\API\ScrapeMatchesApiController;
use Illuminate\Support\Facades\Route;

Route::get('scrape-betano', [ScrapeMatchesApiController::class, 'scrapeBetanoMatch']);
Route::get('scrape-superbet', [ScrapeMatchesApiController::class, 'scrapeSuperbetMatch']);
Route::get('scrape-casapariurilor', [ScrapeMatchesApiController::class, 'scrapeCasaPariurilorMatch']);

Route::get('scrape-links/betano', [ScrapeLinksApiSitesController::class, 'getLinksForBetano']);
Route::get('scrape-links/superbet', [ScrapeLinksApiSitesController::class, 'getLinksForSuperbet']);
Route::get('scrape-links/casa_pariurilor', [ScrapeLinksApiSitesController::class, 'getLinksForCasaPariurilor']);
