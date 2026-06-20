<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\DetectionServiceProvider;
use App\Providers\HorizonServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        DetectionServiceProvider::class,
        HorizonServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        //trust cloudflare        
        $middleware->trustProxies(at: '*');
        $middleware->trustHosts(at: ['*']);
        
        $middleware->append(\App\Http\Middleware\SecurityCollector::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
           
            if ($e->getStatusCode() === 403 && $request->is('horizon*')) {
                session()->put('wants_horizon', true);
                return redirect('/admin');
            }
        });
        
    })->create();
