<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CorsMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add the CORS middleware to global middleware stack
        $middleware->append(CorsMiddleware::class);
        $middleware->validateCsrfTokens(except: [
            'menu', // <-- exclude this route
            'users',
            'orders'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
