<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CustomerController;

// Admin
Route::post('/register', [UserController::class,'store']);
Route::post('/login', [UserController::class,'login']);

//Route::middleware('auth:sanctum')->group(function (){
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('customers', CustomerController::class);
//});

// Category
Route::get('categories', [CategoryController::class, 'get']);
Route::get('categories/{category:id}', [CategoryController::class, 'show']);

// Product
Route::get('products', [ProductController::class, 'get']);
Route::get('products/{product:id}', [ProductController::class, 'show']);


Route::post('cartItem/create', [CartItemController::class, 'store']);
Route::delete('cartItem/{product:id}', [CartItemController::class, 'destroy']);
Route::get('cartItems', [CartItemController::class, 'getMyCartItem']);

Route::post('order/create', [OrderController::class,'store']);
