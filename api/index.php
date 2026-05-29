<?php

// Jalankan migrasi otomatis jika parameter rahasia dipanggil
if (isset($_GET['jalankan_migrasi_rahasia'])) {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->call('migrate', ['--force' => true]);
    echo "<h1>Proses Migrasi Selesai!</h1><p>Status: " . $status . "</p>";
    exit;
}

// Meneruskan request ke index utama Laravel
require __DIR__ . '/../public/index.php';