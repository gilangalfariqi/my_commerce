<?php

// Deteksi otomatis jalur autoload composer di lingkungan serverless Vercel
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

// Jalankan migrasi otomatis jika parameter rahasia dipanggil di URL
if (isset($_GET['jalankan_migrasi_rahasia'])) {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->call('migrate', ['--force' => true]);
    echo "<h1>Proses Migrasi Selesai!</h1><p>Status: " . $status . "</p>";
    exit;
}

// Meneruskan request normal ke index utama Laravel
require __DIR__ . '/../public/index.php';