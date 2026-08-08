<?php

namespace App\Providers;

use App\Models\Setting;
use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\Telescope::class)) {
            $this->app->register(\App\Providers\TelescopeServiceProvider::class);
        }
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // ── Global View Composer ──
        // Inject $siteSettings ke setiap view secara otomatis.
        // Menggunakan static cache agar DB hanya di-query sekali per request lifecycle.
        // CATATAN: reject(null|'') dihapus — nilai empty string bisa jadi valid
        // (contoh: footer_copyright yang sengaja dikosongkan oleh admin).
        View::composer('*', function ($view) {
            static $cache = null;
            if ($cache === null) {
                try {
                    $cache = Setting::all()->pluck('value', 'key');
                } catch (\Throwable $e) {
                    // DB belum siap (misal saat migrasi) — gunakan koleksi kosong
                    $cache = collect();
                }
            }
            $view->with('siteSettings', $cache);
        });
    }
}

