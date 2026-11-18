<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOGIN_LOCKOUT_SECONDS = 300;

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['email'] = strtolower($credentials['email']);
        $rateLimitKey = $this->rateLimitKey($credentials['email'], $request->ip());
        $lockoutKey = $rateLimitKey.':lockout';

        if (RateLimiter::tooManyAttempts($lockoutKey, 1)) {
            return back()
                ->withErrors(['email' => $this->lockoutMessage($lockoutKey)]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($rateLimitKey, self::LOGIN_LOCKOUT_SECONDS);

            if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_LOGIN_ATTEMPTS)) {
                RateLimiter::hit($lockoutKey, self::LOGIN_LOCKOUT_SECONDS);

                return back()
                    ->withErrors(['email' => $this->lockoutMessage($lockoutKey)]);
            }

            return back()
                ->withErrors(['email' => 'The email address or password is incorrect.']);
        }

        RateLimiter::clear($rateLimitKey);
        $request->session()->regenerate();

        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.enrollment-requests');
        }

        return redirect()->intended(route('student.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function rateLimitKey(string $email, string $ip): string
    {
        return 'login:'.sha1(Str::transliterate(Str::lower($email).'|'.$ip));
    }

    private function lockoutMessage(string $rateLimitKey): string
    {
        $minutes = (int) ceil(RateLimiter::availableIn($rateLimitKey) / 60);

        return "Too many failed sign-in attempts. Try again in {$minutes} minute(s).";
    }
}
