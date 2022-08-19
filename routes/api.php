<?php

use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\SubscribeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(GameController::class)->group(function () {
    Route::get('/game', 'index');
    Route::get('/game/mode', 'mode');
    Route::get('/game/matches', 'matches');

});

Route::controller(SubscribeController::class)->group(function () {
    Route::any('/subscribe', 'index');
    Route::any('/subscribe/test', 'test');

});
