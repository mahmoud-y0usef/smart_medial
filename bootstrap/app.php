<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\LaravelFlare\Facades\Flare;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Rate limiters for medical platform
        $middleware->throttleApi();
        
        // Trust all proxies (for ngrok/expose tunnels)
        $middleware->trustProxies(at: '*');
        
        // Custom rate limiters will be defined in RouteServiceProvider
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Flare::handles($exceptions);
    })->create();
