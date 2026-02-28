<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // CSRF exceptii
        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);

        // Security headers pe toate request-urile web
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Aliases
        $middleware->alias([
            'admin'           => \App\Http\Middleware\AdminMiddleware::class,
            'rate.audit'      => \App\Http\Middleware\RateLimitAudit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();