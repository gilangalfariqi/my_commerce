<?php

// Vercel serverless environment has a read-only filesystem except for /tmp.
// We redirect compiled views, sessions, and cache files to /tmp at runtime.
$vercelStorage = '/tmp/storage/framework';
foreach (['views', 'cache', 'sessions'] as $dir) {
    $path = $vercelStorage . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// Override Laravel default cache, view compiled, and log paths for serverless environment
putenv('VIEW_COMPILED_PATH=' . $vercelStorage . '/views');
putenv('SESSION_DRIVER=cookie'); // Serverless should use client-side cookies or DB sessions
putenv('CACHE_STORE=array');     // Avoid writing file cache to read-only directory
putenv('LOG_CHANNEL=stderr');    // Redirect logs to Vercel dashboard console

require __DIR__ . '/../public/index.php';