<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;
use App\Http\Controllers\LinksSitesController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/get-links/betano', [LinksSitesController::class, 'getLinksForBetano']);
Route::get('/get-links/suberbet', [LinksSitesController::class, 'getLinksForSuberbet']);
//Route::get('/', [FootballDataController::class, 'fetchData']);