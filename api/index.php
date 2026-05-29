<?php

// Trik pemicu migrasi lewat URL kustom
if (isset($_GET['jalankan_migrasi_rahasia'])) {
    // Memuat aplikasi Laravel
    $app = require __DIR__ . '/../bootstrap/app.php';
    
    // Menjalankan perintah php artisan migrate --force
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->call('migrate', ['--force' => true]);
    
    echo "<h1>Proses Migrasi Selesai!</h1>";
    echo "<p>Status kode: " . $status . "</p>";
    exit;
}

// Meneruskan request normal ke index bawaan Laravel
require __DIR__ . '/../public/index.php';