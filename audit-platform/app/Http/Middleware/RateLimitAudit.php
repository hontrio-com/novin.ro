<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitAudit
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'audit:' . $request->ip();

        // Max 5 audituri pe oră per IP
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => 'Prea multe cereri. Încearcă din nou în ' . ceil($seconds / 60) . ' minute.',
            ], 429);
        }

        RateLimiter::hit($key, 3600); // decay 1 oră

        return $next($request);
    }
}