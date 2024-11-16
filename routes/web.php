<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;
use App\Http\Controllers\LinksSitesController;

Route::get('/get-links/betano', [LinksSitesController::class, 'getLinksForBetano']);
Route::get('/get-links/superbet', [LinksSitesController::class, 'getLinksForSuperbet']);
Route::get('/get-links/casa_pariurilor', [LinksSitesController::class, 'getLinksForCasaPariurilor']);
//Route::get('/', [FootballDataController::class, 'fetchData']);
Route::get('/test', [FootballDataController::class, 'searchMatchesDataFromDB']);
