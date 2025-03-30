<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;
use App\Http\Controllers\ApiTestsController;
use App\Http\Controllers\LinksSitesController;

Route::get('/get-links/betano', [LinksSitesController::class, 'getLinksForBetano']);
Route::get('/get-links/superbet', [LinksSitesController::class, 'getLinksForSuperbet']);
Route::get('/get-links/casa_pariurilor', [LinksSitesController::class, 'getLinksForCasaPariurilor']);
Route::get('/', [FootballDataController::class, 'searchMatchesDataFromDB']);
Route::get('/scraped', [FootballDataController::class, 'searchMatchesDataFromDB']);
Route::get('/scraped-live', [FootballDataController::class, 'fetchData'])->name("scraped-live");

Route::get('/api-tests', [ApiTestsController::class, 'apiTests'])->name('api-tests');
Route::get('/api-tests/betano', [ApiTestsController::class, 'showBetanoData'])->name('api-tests-betano');
Route::get('/api-tests/superbet', [ApiTestsController::class, 'showSuperbetData'])->name('api-tests-superbet');
Route::get('/api-tests/casapariurilor', [ApiTestsController::class, 'showCasapariurilorData'])->name('api-tests-casapariurilor');

