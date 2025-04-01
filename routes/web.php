<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;
use App\Http\Controllers\TestsApiController;

Route::get('/', [FootballDataController::class, 'searchMatchesDataFromDB']);
Route::get('/scraped', [FootballDataController::class, 'searchMatchesDataFromDB']);
Route::get('/scraped-live', [FootballDataController::class, 'fetchData'])->name("scraped-live");

//test api matches
Route::get('/api-tests', [TestsApiController::class, 'apiTests'])->name('api-tests');
Route::get('/api-tests/matches/betano', [TestsApiController::class, 'showBetanoMatches'])->name('api-tests-matches-betano');
Route::get('/api-tests/matches/superbet', [TestsApiController::class, 'showSuperbetMatches'])->name('api-tests-matches-superbet');
Route::get('/api-tests/matches/casapariurilor', [TestsApiController::class, 'showCasapariurilorMatches'])->name('api-tests-matches-casapariurilor');
//test api links
Route::get('/api-tests/links/betano', [TestsApiController::class, 'showBetanoLinksData'])->name('api-tests-links-betano');
Route::get('/api-tests/links/superbet', [TestsApiController::class, 'showSuperbetLinksData'])->name('api-tests-links-superbet');
Route::get('/api-tests/links/casapariurilor', [TestsApiController::class, 'showCasapariurilorLinksData'])->name('api-tests-links-casapariurilor');




