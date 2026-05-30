<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share $siteSettings to ALL views automatically
        View::composer('*', function ($view) {
            static $settings = null;

            if ($settings === null) {
                try {
                    $settings = Setting::all()->pluck('value', 'key');
                } catch (\Throwable $e) {
                    // DB not ready (e.g. during migration) — use empty collection
                    $settings = collect();
                }
            }

            $view->with('siteSettings', $settings);
        });
    }
}
