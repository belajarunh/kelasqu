<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CloudflareTurnstile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function login(Request $request, CloudflareTurnstile $turnstile): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        if (! $turnstile->verify($credentials['cf-turnstile-response'], 'login', $request->ip())) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Verifikasi Cloudflare gagal. Silakan coba lagi.',
            ]);
        }

        unset($credentials['cf-turnstile-response']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi tidak cocok.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request, CloudflareTurnstile $turnstile): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cf-turnstile-response' => ['required', 'string'],
        ]);

        if (! $turnstile->verify($validated['cf-turnstile-response'], 'register', $request->ip())) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Verifikasi Cloudflare gagal. Silakan coba lagi.',
            ]);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
