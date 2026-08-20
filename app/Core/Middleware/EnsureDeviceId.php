<?php

namespace App\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeviceId
{
    public const COOKIE_NAME = 'cfms_device_id';

    public function handle(Request $request, Closure $next): Response
    {
        $deviceId = $request->cookie(self::COOKIE_NAME);
        $needsCookie = false;

        if (!$deviceId || !preg_match('/^[a-zA-Z0-9_-]{6,64}$/', $deviceId)) {
            $deviceId = 'dev_' . bin2hex(random_bytes(6));
            $needsCookie = true;
        }

        // Set request attribute for easy retrieval across the app
        $request->attributes->set('device_id', $deviceId);

        $response = $next($request);

        if ($needsCookie) {
            // Set permanent cookie (5 years) - not HttpOnly so frontend JS can also read if needed
            $response->headers->setCookie(
                cookie(self::COOKIE_NAME, $deviceId, 60 * 24 * 365 * 5, '/', null, false, false)
            );
        }

        return $response;
    }
}
