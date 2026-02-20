<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CategoryController;

//category management
Route::apiResource('categories', CategoryController::class)->except(['update']);
Route::post('/categories/{category}', [CategoryController::class, 'update']);

//coupon management
Route::middleware('auth:sanctum')->post('/coupons', [CouponController::class, 'store']);
Route::get('/coupons', [CouponController::class, 'index']);


//product mangement
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);

//Order management
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/user_history', [OrderController::class, 'userOrderHistroy']);
    Route::post('/orders', [OrderController::class, 'store']);
});

//authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


?>