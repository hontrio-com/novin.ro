<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Nu aplica pe responses binare (PDF, imagini)
        if (!method_exists($response, 'header')) {
            return $response;
        }

        // ── Strict Transport Security ──────────────────────────────────
        // Forțează HTTPS pentru 1 an, inclusiv subdomenii
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // ── Clickjacking Protection ────────────────────────────────────
        $response->header('X-Frame-Options', 'SAMEORIGIN');

        // ── MIME Sniffing Protection ───────────────────────────────────
        $response->header('X-Content-Type-Options', 'nosniff');

        // ── XSS Protection (legacy browsers) ──────────────────────────
        $response->header('X-XSS-Protection', '1; mode=block');

        // ── Referrer Policy ────────────────────────────────────────────
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── Permissions Policy ─────────────────────────────────────────
        // Dezactivează funcționalități browser nefolosite
        $response->header('Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), ' .
            'accelerometer=(), gyroscope=(), magnetometer=(), midi=()'
        );

        // ── Content Security Policy ────────────────────────────────────
        // Permite: self, Google Fonts, Stripe, Google Analytics, CDN
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://js.stripe.com https://www.googletagmanager.com https://www.google-analytics.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://api.stripe.com https://www.google-analytics.com https://analytics.google.com https://stats.g.doubleclick.net",
            "frame-src https://js.stripe.com https://hooks.stripe.com",
            "form-action 'self' https://checkout.stripe.com",
            "base-uri 'self'",
            "object-src 'none'",
            "upgrade-insecure-requests",
        ]);
        $response->header('Content-Security-Policy', $csp);

        // ── Remove fingerprinting headers ──────────────────────────────
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}