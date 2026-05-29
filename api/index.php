<?php

// 1. WAJIB: Muat autoloader Composer agar semua class Laravel dikenali oleh Vercel
require __DIR__ . '/../vendor/autoload.php';

// 2. Jalankan migrasi otomatis jika parameter rahasia dipanggil di URL
if (isset($_GET['jalankan_migrasi_rahasia'])) {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->call('migrate', ['--force' => true]);
    echo "<h1>Proses Migrasi Selesai!</h1><p>Status: " . $status . "</p>";
    exit;
}

// 3. Meneruskan request normal ke index utama Laravel
require __DIR__ . '/../public/index.php';