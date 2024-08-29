<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BannerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'auth'], function() {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('/me', [AuthController::class, 'me']);    
        Route::put('/update', [AuthController::class, 'update']);    
        Route::post('/logout', [AuthController::class, 'logout']);    
    });
});

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::group(['prefix' => 'banner'], function() {
        Route::post('/', [BannerController::class, 'store']);
    });
});