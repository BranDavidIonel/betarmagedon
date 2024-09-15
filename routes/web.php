<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;
use App\Http\Controllers\LinksSitesController;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/get-links', [LinksSitesController::class, 'getLinks']);
//Route::get('/', [FootballDataController::class, 'fetchData']);