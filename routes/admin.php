<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductImportController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.login');
        });
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
     Route::resource('categories', CategoryController::class);
    Route::post('categories/sort-order', [CategoryController::class, 'updateSortOrder'])->name('categories.sort-order');
    
    // Product Management
    Route::resource('products', ProductController::class);
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('products/{product}/images/{image}/primary', [ProductController::class, 'setPrimaryImage'])->name('products.images.primary');
    Route::get('products-export', [ProductController::class, 'export'])->name('products.export');
    Route::get('products-low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    
    // Product Import/Export
    Route::get('products-import', [ProductImportController::class, 'showImportForm'])->name('products.import');
    Route::post('products-import', [ProductImportController::class, 'import'])->name('products.import.process');
    Route::get('products-import-template', [ProductImportController::class, 'downloadTemplate'])->name('products.import.template');


       // Coupon Management
    Route::resource('coupons', CouponController::class);
    Route::post('coupons/bulk-action', [CouponController::class, 'bulkAction'])->name('coupons.bulk-action');
    Route::post('coupons/{coupon}/duplicate', [CouponController::class, 'duplicate'])->name('coupons.duplicate');
    Route::get('coupons-export', [CouponController::class, 'export'])->name('coupons.export');
    Route::get('generate-coupon-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
    Route::post('validate-coupon-code', [CouponController::class, 'validateCode'])->name('coupons.validate-code');

});
});