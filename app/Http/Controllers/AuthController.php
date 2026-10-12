<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View
    {
        return view('auth.login', [
            'captchaQuestion' => $this->createCaptchaQuestion($request),
        ]);
    }

    public function showRegister(Request $request): View
    {
        return view('auth.register', [
            'captchaQuestion' => $this->createCaptchaQuestion($request),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $captchaAnswer = $request->session()->pull('auth_captcha_answer');

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha_answer' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, Closure $fail) use ($captchaAnswer): void {
                    if ($captchaAnswer === null || (string) $value !== (string) $captchaAnswer) {
                        $fail('Jawaban verifikasi tidak tepat.');
                    }
                },
            ],
        ]);
        unset($credentials['captcha_answer']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi tidak cocok.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(Request $request): RedirectResponse
    {
        $captchaAnswer = $request->session()->pull('auth_captcha_answer');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha_answer' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, Closure $fail) use ($captchaAnswer): void {
                    if ($captchaAnswer === null || (string) $value !== (string) $captchaAnswer) {
                        $fail('Jawaban verifikasi tidak tepat.');
                    }
                },
            ],
        ]);

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

    private function createCaptchaQuestion(Request $request): string
    {
        $firstNumber = random_int(2, 9);
        $secondNumber = random_int(1, 9);
        $request->session()->put('auth_captcha_answer', $firstNumber + $secondNumber);

        return "Berapakah {$firstNumber} + {$secondNumber}?";
    }
}