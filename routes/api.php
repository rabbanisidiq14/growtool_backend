<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProcessController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['Cors'])->group(function() {
    Route::get('/logs', [ProcessController::class, 'getAllLogs']);
    Route::post('/buy', [ProcessController::class, 'buyItem']);
    Route::post('/sell', [ProcessController::class, 'sellItem']);

    Route::get('/portfolios', [ProcessController::class, 'getAllPortfolios']);
    Route::post('/currentPrice', [ProcessController::class, 'changeCurrentPrice']);

    Route::get('/status', [ProcessController::class, 'getStatusNow']);
});