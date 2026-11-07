<?php

use App\Features\Admin\Livewire\AdminUserStockList;
use App\Features\Admin\Livewire\UserList;
use App\Features\Auth\Livewire\Login;
use App\Features\Auth\Livewire\Register;
use App\Features\Profile\Livewire\Profile;
use App\Features\Stock\Livewire\StockDetail;
use App\Features\Stock\Livewire\StockList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:1000-per-second'])->group(function () {
    // Diagnostic database info
    Route::get('/debug-db', function () {
        try {
            return response()->json([
                'status' => 'connected',
                'driver' => \Illuminate\Support\Facades\DB::connection()->getDriverName(),
                'database' => \Illuminate\Support\Facades\DB::connection()->getDatabaseName(),
                'host' => \Illuminate\Support\Facades\DB::connection()->getConfig('host'),
                'config_user' => \Illuminate\Support\Facades\DB::connection()->getConfig('username'),
                'env_db_host' => getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? 'NOT_SET')),
                'env_db_conn' => getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? ($_SERVER['DB_CONNECTION'] ?? 'NOT_SET')),
                'env_db_user' => getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? ($_SERVER['DB_USERNAME'] ?? 'NOT_SET')),
                'user_count' => \App\Models\User::count(),
                'users' => \App\Models\User::select('id', 'name', 'username', 'email', 'role')->get(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'config_user' => config('database.connections.pgsql.username'),
                'env_db_host' => getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? 'NOT_SET')),
                'env_db_conn' => getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? ($_SERVER['DB_CONNECTION'] ?? 'NOT_SET')),
                'env_db_user' => getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? ($_SERVER['DB_USERNAME'] ?? 'NOT_SET')),
            ], 500);
        }
    });

    // Root Redirection
    Route::get('/', function () {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.users')
                : redirect()->route('stocks.index');
        }

        return redirect()->route('login');
    });

    // Guest Authentication Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', Login::class)->name('login');
        Route::get('/register', Register::class)->name('register');
    });

    // Authenticated Routes
    Route::middleware('auth')->group(function () {
        Route::post('/logout', function (Request $request) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        })->name('logout');

        // Member Stock Routes protected by ownership middleware
        Route::get('/stocks', StockList::class)
            ->middleware('owns.stock')
            ->name('stocks.index');
        Route::get('/stocks/{stock}', StockDetail::class)
            ->middleware('owns.stock')
            ->name('stocks.show');

        // User Profile Route
        Route::get('/profile', Profile::class)->name('profile');

        // Admin Routes protected by Role middleware
        Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
            Route::get('/users', UserList::class)->name('users');
            Route::get('/users/{user}/stocks', AdminUserStockList::class)->name('users.stocks');
        });
    });
});
