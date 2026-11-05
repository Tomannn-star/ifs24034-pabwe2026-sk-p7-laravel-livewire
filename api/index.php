<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

define('LARAVEL_START', microtime(true));

// Setup Vercel serverless environment markers
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// Prepare /tmp directories for Vercel serverless environment
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Database handling on Vercel:
// If DB_HOST is localhost/127.0.0.1 or empty (local MariaDB cannot be reached from cloud),
// use SQLite in /tmp/database.sqlite
$dbHost = getenv('DB_HOST');
if (empty($dbHost) || $dbHost === '127.0.0.1' || $dbHost === 'localhost') {
    $tmpDb = '/tmp/database.sqlite';
    $seedDb = __DIR__ . '/../database/database.sqlite';

    if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
        if (file_exists($seedDb) && filesize($seedDb) > 0) {
            @copy($seedDb, $tmpDb);
        } else {
            @touch($tmpDb);
        }
    }

    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// Register Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel Application
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// If running sqlite and users table is somehow missing, migrate & seed automatically
if (getenv('DB_CONNECTION') === 'sqlite') {
    try {
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        if (!Schema::hasTable('users')) {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
        }
    } catch (\Throwable $e) {
        // Log or silently continue
    }
}

// Handle request
$app->handleRequest(Request::capture());
