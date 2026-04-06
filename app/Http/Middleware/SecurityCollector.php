<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Services\DetectionEngine;
use App\Services\Rules\RateLimitRule;
use App\Models\SecurityEvent;


class SecurityCollector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        //ejecutar la request
        $response = $next($request);
        $duration = microtime(true) - $start;

        $event = SecurityEvent::create([

            'ip_address'    => $request->ip(),
            'method'        => $request->method(),
            'url'           => $request->fullUrl(),
            'user_agent'    => $request->userAgent(),
            'status_code'   => $response->getStatusCode(),
            'payload'       => json_encode($request->all()),
            

        ]);

        //
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        $engine= app(DetectionEngine::class); //singleton para que no creemos una instancia del engine cada vez que hay una rqeuest
        $results = $engine->evaluate([
            'ip' => $ip,
            'user_agent' => $userAgent,
            'payload' => $request->all(),//lo dejo preparado para mañana añadir la regla de sqli
        ], $event->id);

        //dd($request->all());

        return $response;
    }
}
