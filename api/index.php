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
// Check if an external remote database (e.g. Supabase / Neon PostgreSQL, TiDB / Aiven MySQL) is configured
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
$dbPort = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? ''));
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? ($_SERVER['DB_CONNECTION'] ?? ''));
$dbUrl = getenv('DATABASE_URL') ?: (getenv('DB_URL') ?: ($_ENV['DATABASE_URL'] ?? ($_ENV['DB_URL'] ?? ($_SERVER['DATABASE_URL'] ?? ($_SERVER['DB_URL'] ?? '')))));

$hasRemoteDb = (!empty($dbHost) && $dbHost !== '127.0.0.1' && $dbHost !== 'localhost') || !empty($dbUrl);

if ($hasRemoteDb) {
    // If connecting to Supabase / PostgreSQL host or port, ensure DB_CONNECTION is pgsql
    if (empty($dbConn) || $dbConn === 'sqlite') {
        if ($dbPort === '5432' || $dbPort === '6543' || str_contains($dbHost, 'supabase') || str_contains($dbHost, 'pooler') || str_contains($dbHost, 'neon') || str_starts_with($dbUrl, 'postgres')) {
            putenv("DB_CONNECTION=pgsql");
            $_ENV['DB_CONNECTION'] = 'pgsql';
            $_SERVER['DB_CONNECTION'] = 'pgsql';
        }
    }
} else {
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

// Auto-run migrations and seeds if users table is missing on the current database
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

// Handle request
$app->handleRequest(Request::capture());
