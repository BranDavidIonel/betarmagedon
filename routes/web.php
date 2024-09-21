<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;
use App\Http\Controllers\LinksSitesController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/get-links/betano', [LinksSitesController::class, 'getLinksForBetano']);
Route::get('/get-links/superbet', [LinksSitesController::class, 'getLinksForSuperbet']);
//Route::get('/', [FootballDataController::class, 'fetchData']);
