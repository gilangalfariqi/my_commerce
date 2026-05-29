<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\OrderController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

// Frontend routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products & Catalog
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/autocomplete', [ProductController::class, 'searchAutocomplete'])
    ->name('products.autocomplete')
    ->middleware('throttle:60,1');
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
    // Route checkout lama (payment + form alamat) dinonaktifkan.
    // Digantikan dengan checkout WhatsApp cepat: /checkout/whatsapp-fast


    // Checkout WhatsApp fast: tampilkan cart di halaman /checkout
    Route::get('/checkout', [CheckoutController::class, 'whatsappFast'])->name('checkout.whatsappFast');

    // Buat link WhatsApp redirect (untuk tombol di halaman)
    Route::get('/checkout/link', [CheckoutController::class, 'whatsappFastLink'])->name('checkout.whatsappFastLink');




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

// Bike Fitment API (used by Livewire and JS autocomplete)

Route::prefix('api/fitment')->name('api.fitment.')->group(function () {
    Route::get('/brands', function () {
        return response()->json(
            \App\Models\BikeBrand::active()->ordered()
                ->get(['id', 'name', 'slug'])
        );
    })->name('brands');

    Route::get('/models', function (\Illuminate\Http\Request $request) {
        if (! $request->filled('brand')) {
            return response()->json([]);
        }
        return response()->json(
            \App\Models\BikeModel::active()
                ->whereHas('bikeBrand', fn ($q) => $q->where('slug', $request->brand))
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'years_available'])
        );
    })->name('models');

    Route::get('/years', function (\Illuminate\Http\Request $request) {
        if (! $request->filled('model')) {
            return response()->json([]);
        }
        $bikeModel = \App\Models\BikeModel::where('slug', $request->model)->first();
        return response()->json($bikeModel?->getAllAvailableYears() ?? []);
    })->name('years');
});

// Dynamic Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('sitemap');

require __DIR__.'/auth.php';

// Secure Vercel database migration route (secured via APP_KEY token)
Route::get('/vercel-migrate', function () {
    if (request()->query('token') !== env('APP_KEY')) {
        abort(403, 'Unauthorized token.');
    }
    
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        if (request()->has('seed')) {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $output .= "\n" . \Illuminate\Support\Facades\Artisan::output();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Migrations/Seeders executed successfully.',
            'output' => explode("\n", trim($output))
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
