<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Register Composer Autoloader
require __DIR__.'/../vendor/autoload.php';

try {
    // 2. Customize storage & bootstrap cache paths for Vercel writeable /tmp directory
    $vercelStorage = '/tmp/storage/framework';
    $vercelCache = '/tmp/storage/bootstrap/cache';

    // Ensure all necessary directories exist in writeable /tmp
    foreach ([$vercelStorage . '/views', $vercelStorage . '/cache', $vercelStorage . '/sessions', $vercelCache] as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    // Override Laravel default storage paths and drivers
    putenv('VIEW_COMPILED_PATH=' . $vercelStorage . '/views');
    putenv('SESSION_DRIVER=cookie'); // Serverless should use client-side cookies or DB sessions
    putenv('CACHE_STORE=array');     // Avoid writing file cache to read-only directory
    putenv('LOG_CHANNEL=stderr');    // Redirect logs to Vercel dashboard console

    // Redirect all bootstrap cache files to writeable /tmp directory to bypass read-only filesystem limit
    putenv('APP_SERVICES_CACHE=' . $vercelCache . '/services.php');
    putenv('APP_PACKAGES_CACHE=' . $vercelCache . '/packages.php');
    putenv('APP_CONFIG_CACHE=' . $vercelCache . '/config.php');
    putenv('APP_ROUTES_CACHE=' . $vercelCache . '/routes.php');
    putenv('APP_EVENTS_CACHE=' . $vercelCache . '/events.php');

    // 3. Bootstrap Laravel
    /** @var Application $app */
    $app = require_once __DIR__.'/../bootstrap/app.php';

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