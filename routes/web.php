<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FootballDataController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [FootballDataController::class, 'fetchData']);