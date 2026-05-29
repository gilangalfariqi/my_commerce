<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// 1. Register Composer Autoloader
require __DIR__.'/../vendor/autoload.php';

// 2. Bootstrap Laravel application
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// 3. Customize storage path for Vercel read-only filesystem environment
$vercelStorage = '/tmp/storage/framework';
foreach (['views', 'cache', 'sessions'] as $dir) {
    $path = $vercelStorage . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

// Dynamically override storage paths to writeable /tmp directory
$app->useStoragePath('/tmp/storage');

// Force dynamic configurations at runtime to override cached configurations
config([
    'view.compiled' => $vercelStorage . '/views',
    'session.driver' => 'cookie',
    'cache.default' => 'array',
    'logging.default' => 'stderr',
]);

// 4. Handle incoming HTTP request
$app->handleRequest(Request::capture());