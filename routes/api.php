<?php
use App\Http\Controllers\ScrapeApiController;
use Illuminate\Support\Facades\Route;
Route::get('scrape-betano', [ScrapeApiController::class, 'scrapeBetanoMatch']);
Route::get('scrape-superbet', [ScrapeApiController::class, 'scrapeSuperbetMatch']);
Route::get('scrape-casapariurilor', [ScrapeApiController::class, 'scrapeCasaPariurilorMatch']);
