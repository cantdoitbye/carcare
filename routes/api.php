<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ContactController;

// Authentication APIs
Route::middleware('api')->group(function () {

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

// Banner/Slider APIs
Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class, 'index']);
});

// Categories, Subcategories, Brands, Navigation APIs
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/subcategories', [CategoryController::class, 'subcategories']);
    Route::get('/brands', [CategoryController::class, 'brands']);
    Route::get('/nav-links', [CategoryController::class, 'navLinks']);
});

// Product APIs
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/filter', [ProductController::class, 'filter']);
    Route::get('/{slug}/related', [ProductController::class, 'related']);
});

// Cart and Checkout APIs (Protected)
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::post('/', [CartController::class, 'add']);
    Route::get('/', [CartController::class, 'index']);
    Route::post('/checkout', [CartController::class, 'checkout']);
});

// Wishlist APIs (Protected)
Route::middleware('auth:sanctum')->prefix('wishlist')->group(function () {
    Route::post('/', [WishlistController::class, 'add']);
    Route::get('/', [WishlistController::class, 'index']);
});

// Contact API
Route::post('contact', [ContactController::class, 'store']);

});