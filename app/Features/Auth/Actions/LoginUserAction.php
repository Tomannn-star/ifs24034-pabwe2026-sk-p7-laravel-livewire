<?php

namespace App\Features\Auth\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
    /**
     * Execute authentication using username or email and regenerate session.
     */
    public function execute(string $login, string $password, bool $remember = false): bool
    {
        $login = trim($login);
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // Case-insensitive lookup to guarantee compatibility with PostgreSQL, MySQL, and SQLite
        $query = User::query();
        if ($isEmail) {
            $query->whereRaw('LOWER(email) = ?', [strtolower($login)]);
        } else {
            $query->whereRaw('LOWER(username) = ?', [strtolower($login)]);
        }

        $user = $query->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            session()->regenerate();
            return true;
        }

        return false;
    }
}
