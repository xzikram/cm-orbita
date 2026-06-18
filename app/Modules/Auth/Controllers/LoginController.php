<?php

namespace App\Modules\Auth\Controllers;

use App\Core\Services\AuditLogService;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Check if locked out
        if (LoginAttempt::isLockedOut($request->email, $request->ip())) {
            $lockoutMinutes = config('cfms.security.lockout_minutes', 15);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$lockoutMinutes} menit.",
            ]);
        }

        // Rate limiting
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Silakan coba lagi nanti.',
            ]);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                ]);
            }

            // Update last login info
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Log successful login
            LoginAttempt::create([
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'successful' => true,
                'user_agent' => $request->userAgent(),
            ]);

            LoginAttempt::clearFor($request->email, $request->ip());
            RateLimiter::clear($key);

            $this->auditLogService->logLogin($user->id);

            return redirect()->intended(route('dashboard'));
        }

        // Log failed attempt
        LoginAttempt::create([
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'successful' => false,
            'user_agent' => $request->userAgent(),
        ]);

        RateLimiter::hit($key, 60);

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        $this->auditLogService->logLogout();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
