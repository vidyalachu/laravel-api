<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

//product mangement
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);

//Order management
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});



//authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


?>