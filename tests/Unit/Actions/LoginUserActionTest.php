<?php

namespace Tests\Unit\Actions;

use App\Features\Auth\Actions\LoginUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginUserActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_email(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'username' => 'testuser',
            'password' => Hash::make('secret123'),
        ]);

        $action = new LoginUserAction();
        $result = $action->execute('user@example.com', 'secret123');

        $this->assertTrue($result);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_login_with_valid_username(): void
    {
        $user = User::factory()->create([
            'email' => 'user2@example.com',
            'username' => 'testuser2',
            'password' => Hash::make('secret123'),
        ]);

        $action = new LoginUserAction();
        $result = $action->execute('testuser2', 'secret123');

        $this->assertTrue($result);
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_login_case_insensitive_lookup(): void
    {
        $user = User::factory()->create([
            'email' => 'CaseSensitive@Example.Com',
            'username' => 'MixCaseUser',
            'password' => Hash::make('secret123'),
        ]);

        $action = new LoginUserAction();

        // Uppercase / lowercase mismatch on email
        $this->assertTrue($action->execute('casesensitive@example.com', 'secret123'));
        Auth::logout();

        // Uppercase / lowercase mismatch on username
        $this->assertTrue($action->execute('mixcaseuser', 'secret123'));
    }

    public function test_login_with_remember_me(): void
    {
        User::factory()->create([
            'email' => 'remember@example.com',
            'username' => 'rememberuser',
            'password' => Hash::make('secret123'),
        ]);

        $action = new LoginUserAction();
        $result = $action->execute('rememberuser', 'secret123', true);

        $this->assertTrue($result);
        $this->assertTrue(Auth::check());
    }

    public function test_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'wrongpass@example.com',
            'username' => 'wrongpassuser',
            'password' => Hash::make('secret123'),
        ]);

        $action = new LoginUserAction();
        $result = $action->execute('wrongpassuser', 'wrong-pass');

        $this->assertFalse($result);
        $this->assertFalse(Auth::check());
    }

    public function test_login_with_nonexistent_user(): void
    {
        $action = new LoginUserAction();
        $result = $action->execute('nonexistent@example.com', 'secret123');

        $this->assertFalse($result);
        $this->assertFalse(Auth::check());
    }
}
