<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
// If DB_HOST is localhost/127.0.0.1 or empty (because local MariaDB cannot be reached from cloud),
// automatically initialize and migrate a fast SQLite database in /tmp
$dbHost = getenv('DB_HOST');
$needsMigration = false;

if (empty($dbHost) || $dbHost === '127.0.0.1' || $dbHost === 'localhost') {
    $dbPath = '/tmp/database.sqlite';
    if (!file_exists($dbPath)) {
        @touch($dbPath);
        $needsMigration = true;
    }
    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $dbPath;
}

// Register Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel Application
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Auto-migrate and seed tables on first serverless cold-start
if ($needsMigration) {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        // Silently continue if already initialized
    }
}

// Handle request
$app->handleRequest(Request::capture());
