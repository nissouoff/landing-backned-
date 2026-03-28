<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\OrderController;

Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::delete('/user/delete', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');

Route::post('/upload', [UploadController::class, 'upload'])->middleware('auth:sanctum');

Route::get('/shop/{slug}', [LandingController::class, 'publicShow']);
Route::post('/shop/{slug}/view', [LandingController::class, 'trackView']);
Route::post('/shop/{slug}/review', [LandingController::class, 'storeReview']);

Route::post('/shop/{slug}/order', [OrderController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/landings', [LandingController::class, 'index']);
    Route::post('/landings', [LandingController::class, 'store']);
    Route::get('/landings/{landing}', [LandingController::class, 'show']);
    Route::put('/landings/{landing}', [LandingController::class, 'update']);
    Route::delete('/landings/{landing}', [LandingController::class, 'destroy']);
    Route::post('/landings/{landing}/publish', [LandingController::class, 'publish']);
    Route::post('/landings/{landing}/unpublish', [LandingController::class, 'unpublish']);
    
    Route::get('/orders', [OrderController::class, 'index']);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});
