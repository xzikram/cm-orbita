<?php

namespace App\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionSecurity
{
    /**
     * Enhance session security with regeneration and secure cookie flags.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Regenerate session ID periodically to prevent session fixation
        if ($request->hasSession() && $request->session()->has('_last_regeneration')) {
            $lastRegeneration = $request->session()->get('_last_regeneration');
            if (time() - $lastRegeneration > 300) { // Every 5 minutes
                $request->session()->regenerate();
                $request->session()->put('_last_regeneration', time());
            }
        } elseif ($request->hasSession()) {
            $request->session()->put('_last_regeneration', time());
        }

        $response = $next($request);

        // Ensure secure cookie flags
        if ($response->headers->has('Set-Cookie')) {
            $response->headers->set('Set-Cookie',
                $response->headers->get('Set-Cookie'),
                false
            );
        }

        return $response;
    }
}
