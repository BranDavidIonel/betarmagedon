<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeleniumController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [SeleniumController::class, 'fetchData']);