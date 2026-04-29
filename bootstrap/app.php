<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Modules\MediaConverter\Providers\MediaConverterServiceProvider::class,
        App\Modules\VideoConversion\Providers\VideoConversionServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Nothing
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Nothing
    })
    ->create();
