<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CustomerController;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     version="1.0.0",
 *     description="API documentation using Swagger"
 * )
 */

/**
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */

// Admin
Route::post('/register', [UserController::class,'register']);
Route::post('/login', [UserController::class,'login']);

Route::middleware('auth:sanctum')->group(function (){
    // Categories
    Route::post('category/create', [CategoryController::class, 'store']);
    Route::get('categories/{category:id}', [CategoryController::class, 'show']);
    Route::patch('categories/{category:id}', [CategoryController::class, 'update']);
    Route::delete('categories/{category:id}', [CategoryController::class, 'destroy']);
    // Products
    Route::post('product/create', [ProductController::class, 'store']);
    Route::get('products/{product:id}', [ProductController::class, 'show']);
    Route::patch('products/{product:id}', [ProductController::class, 'update']);
    Route::delete('products/{product:id}', [ProductController::class, 'destroy']);
    // Orders
    Route::get('orders', [OrderController::class, 'get']);
    Route::patch('orders/{order:id}/complete', [OrderController::class, 'complete']);
    Route::patch('orders/{order:id}/cancel', [OrderController::class, 'cancel']);
    Route::delete('orders/{order:id}', [OrderController::class, 'destroy']);
});

// User
Route::get('getToken', [CustomerController::class, 'getToken']);

Route::get('categories', [CategoryController::class, 'get']);
Route::get('products', [ProductController::class, 'get']);

Route::post('cartItem/create', [CartItemController::class, 'store']);
Route::delete('cartItem/{product:id}', [CartItemController::class, 'destroy']);

Route::post('order/create', [OrderController::class,'store']);
