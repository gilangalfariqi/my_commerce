<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * SettingServiceProvider
 *
 * Catatan: View composer untuk $siteSettings sudah didaftarkan
 * di AppServiceProvider::boot() dengan static cache yang lebih baik.
 * Provider ini dipertahankan agar tidak perlu mengubah config/app.php
 * tapi tidak mendaftarkan view composer duplikat.
 */
class SettingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // View composer untuk $siteSettings sudah ditangani oleh AppServiceProvider.
        // Tidak ada registrasi duplikat di sini untuk menghindari double DB query.
    }
}
