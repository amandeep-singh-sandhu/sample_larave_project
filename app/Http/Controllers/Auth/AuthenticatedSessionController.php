<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    // public function create(): View
    // {
    //     return view('auth.login');

    //     $failedLoginAttempts = Redis::get('failed_login_attempts_' . $request->ip());
    //     if ($failedLoginAttempts === null) {
    //         Redis::setex('failed_login_attempts_' . $request->ip(), 300, 1);
    //     } elseif ((int)$failedLoginAttempts < 5) {
    //         Redis::incr('failed_login_attempts_' . $request->ip());
    //     } else {
    //         return back()->withErrors(['email' => 'You have exceeded the maximum number of login attempts.']);
    //     }
    // }

    public function create(Request $request): View
    {
        $failedLoginAttempts = Redis::get('failed_login_attempts_' . $request->ip());
        if ($failedLoginAttempts === null) {
            Redis::setex('failed_login_attempts_' . $request->ip(), 300, 1);
        } elseif ((int)$failedLoginAttempts < 5) {
            Redis::incr('failed_login_attempts_' . $request->ip());
        } else {
            return view('auth.login')->with('errors', ['email' => 'You have exceeded the maximum number of login attempts.']);
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
