<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PortfolioController;

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

Route::group(['prefix' => 'banner'], function() {
    Route::get('/{banner}', [BannerController::class, 'show']);
    Route::get('/', [BannerController::class, 'index']);

    Route::group(['middleware' => ['auth:sanctum', 'isAdmin']], function() {
        Route::post('/', [BannerController::class, 'store']);
        Route::put('/{banner}', [BannerController::class, 'update']);
        Route::delete('/{banner}', [BannerController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'category'], function() {
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::get('/', [CategoryController::class, 'index']);

    Route::group(['middleware' => ['auth:sanctum', 'isAdmin']], function() {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'blog'], function() {
    Route::get('/{blog}', [BlogController::class, 'show']);
    Route::get('/', [BlogController::class, 'index']);

    Route::group(['middleware' => ['auth:sanctum', 'isAdmin']], function() {
        Route::post('/', [BlogController::class, 'store']);
        Route::put('/{blog}', [BlogController::class, 'update']);
        Route::delete('/{blog}', [BlogController::class, 'destroy']);
    });
});

Route::group(['prefix' => 'portfolio'], function() {
    Route::get('/{portfolio}', [PortfolioController::class, 'show']);
    Route::get('/', [PortfolioController::class, 'index']);

    Route::group(['middleware' => ['auth:sanctum', 'isAdmin']], function() {
        Route::post('/', [PortfolioController::class, 'store']);
        Route::put('/{portfolio}', [PortfolioController::class, 'update']);
        Route::delete('/{portfolio}', [PortfolioController::class, 'destroy']);
    });
});