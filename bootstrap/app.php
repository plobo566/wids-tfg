<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\DetectionServiceProvider;


return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        DetectionServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityCollector::class);
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
