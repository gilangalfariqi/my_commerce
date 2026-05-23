<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\OrderController;
use App\Services\RajaOngkir\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Frontend routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/autocomplete', [ProductController::class, 'searchAutocomplete'])->name('products.autocomplete');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart AJAX endpoints
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/cities', [CheckoutController::class, 'getCities'])->name('checkout.cities');
    Route::post('/checkout/shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.shipping');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Customer dashboard & order history
    Route::get('/dashboard', function() {
        return redirect()->route('orders.index');
    })->name('dashboard');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order_number}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order_number}/sync', [OrderController::class, 'syncStatus'])->name('orders.sync');
});

// Guest-friendly order tracking
Route::get('/orders/track', [OrderController::class, 'track'])->name('orders.track');

// User profile settings
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// AJAX API endpoints (publicly accessible, cached)
Route::get('/api/cities', function (Request $request, RajaOngkirService $rajaOngkir) {
    $cities = $rajaOngkir->getCities($request->integer('province_id') ?: null);
    return response()->json(['cities' => $cities]);
})->name('api.cities');

require __DIR__.'/auth.php';
