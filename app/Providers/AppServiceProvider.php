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
        // Inject $siteSettings into EVERY view automatically.
        // Uses a static cache so DB is only queried once per request.
        View::composer('*', function ($view) {
            static $cache = null;
            if ($cache === null) {
                try {
                    $cache = Setting::all()->pluck('value', 'key')->reject(fn($val) => $val === null || $val === '');
                } catch (\Throwable $e) {
                    $cache = collect();
                }
            }
            $view->with('siteSettings', $cache);
        });
    }
}

