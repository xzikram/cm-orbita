<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email', 'ip_address', 'successful', 'user_agent',
    ];

    protected $casts = [
        'successful' => 'boolean',
    ];

    /**
     * Check if the given email/IP is locked out.
     */
    public static function isLockedOut(string $email, string $ip): bool
    {
        $maxAttempts = config('cfms.security.max_login_attempts', 5);
        $lockoutMinutes = config('cfms.security.lockout_minutes', 15);

        $recentFailures = static::where('email', $email)
            ->where('ip_address', $ip)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($lockoutMinutes))
            ->count();

        return $recentFailures >= $maxAttempts;
    }

    /**
     * Clear successful login attempts for the email.
     */
    public static function clearFor(string $email, string $ip): void
    {
        static::where('email', $email)
            ->where('ip_address', $ip)
            ->where('successful', false)
            ->delete();
    }
}
