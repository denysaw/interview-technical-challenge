<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureSpotExists;
use App\Http\Middleware\EnsureSpotIsFree;
use App\Http\Controllers\ParkingController;
use App\Http\Middleware\EnsureSessionExists;
use App\Http\Middleware\EnsureSpotHasProperType;
use App\Http\Middleware\EnsureSessionIsNotYetStarted;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(ParkingController::class)->group(function () {
    Route::get('/parking-lot', 'getSpotsState');

    Route::get('/parking-spot/{id}', 'getSingleSpotInfo')->middleware([
        EnsureSpotExists::class
    ]);

    Route::get('/parking-spot/{id}/sessions', 'getSpotSessions')->middleware([
        EnsureSpotExists::class
    ]);

    Route::post('/parking-spot/{id}/park', 'startSession')->middleware([
        EnsureSessionIsNotYetStarted::class,
        EnsureSpotExists::class,
        EnsureSpotHasProperType::class,
        EnsureSpotIsFree::class,
    ]);

    Route::post('/parking-spot/{id}/unpark', 'stopSession')->middleware([
        EnsureSpotExists::class,
        EnsureSessionExists::class,
    ]);
});
