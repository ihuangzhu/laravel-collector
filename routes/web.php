<?php

use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 主后台
Route::prefix('admin')->group(function () {
    // 登录
    Route::controller(LoginController::class)->group(function () {
        Route::match(['get', 'post'], '/login', 'login');
        Route::post('/logout', 'logout');
    });

    // 首页
    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index');

    });

    // 游戏
    Route::controller(GameController::class)->prefix('game')->group(function () {
        Route::get('/', 'index');
        Route::get('/query', 'query');
        Route::get('/refresh', 'refresh');
        Route::match(['get', 'post'], '/edit', 'edit');

    });

});