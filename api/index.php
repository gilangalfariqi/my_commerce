<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Register Composer Autoloader
require __DIR__.'/../vendor/autoload.php';

try {
    // 2. Bootstrap Laravel
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // 3. Customize storage path for Vercel writeable /tmp directory
    $vercelStorage = '/tmp/storage/framework';
    foreach (['views', 'cache', 'sessions'] as $dir) {
        $path = $vercelStorage . '/' . $dir;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    // Set paths & drivers for Vercel
    putenv('VIEW_COMPILED_PATH=' . $vercelStorage . '/views');
    putenv('SESSION_DRIVER=cookie');
    putenv('CACHE_STORE=array');
    putenv('LOG_CHANNEL=stderr');

    // 4. Handle incoming HTTP request
    $app->handleRequest(Request::capture());

} catch (\Throwable $e) {
    // Log the actual root cause clearly to Vercel console stderr
    error_log('CRITICAL BOOTSTRAP ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Output error clearly in browser for immediate diagnostics
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Bootstrap Error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => explode("\n", $e->getTraceAsString())
    ], JSON_PRETTY_PRINT);
    exit(1);
}