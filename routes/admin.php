<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

// Admin routing. Middleware and prefix are applied in bootstrap/app.php
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('products', ProductController::class);
Route::resource('categories', CategoryController::class);
Route::resource('orders', OrderController::class)->only(['index', 'show', 'edit', 'update']);
Route::resource('coupons', CouponController::class);
Route::resource('banners', BannerController::class);
Route::resource('flash-sales', FlashSaleController::class);
Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update']);
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
