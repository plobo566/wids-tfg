<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Services\DetectionEngine;
use App\Services\Rules\RateLimitRule;
use App\Models\SecurityEvent;
use App\Services\DataNormalizer;
use App\Jobs\AnalyzeSecurityEvent;

class SecurityCollector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->is('horizon*') || $request->is('livewire*') || $request->is('admin*')){
            return $next($request);
        }

        $start = microtime(true);

        //ejecutar la request
        $response = $next($request);
        $duration = microtime(true) - $start;


        $rawData = [
            'ip_address'    => $request->ip(),
            'method'        => $request->method(),
            'url'           => $request->fullUrl(),
            'user_agent'    => $request->userAgent(),

            'payload'       => $request->all(),
        ];

        $event = SecurityEvent::create([

            'ip_address'    => $rawData['ip_address'],
            'method'        => $rawData['method'],
            'url'           => $rawData['url'],
            'user_agent'    => $rawData['user_agent'],
            'status_code'   => $response->getStatusCode(),
            'payload'       => $rawData['payload'],
            

        ]);


        $normalizer = app(DataNormalizer::class);
        $normalizedData = $normalizer->normalize($rawData);
        //
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        //$engine= app(DetectionEngine::class); //singleton para que no creemos una instancia del engine cada vez que hay una rqeuest
        AnalyzeSecurityEvent::dispatch([
            'ip' => $normalizedData['ip_address'],
            'user_agent' => $normalizedData['user_agent'],
            'payload' => $normalizedData['payload'],//lo dejo preparado para añadir la regla de sqli
            'path' => $request->path(),
            'method'=>$normalizedData['method'],
            'url' => $normalizedData['url'],
            'referer' => $request->headers->get('referer', 'direct'), //si no hay, ponemos direct
            'origin' => $request->headers->get('origin', 'direct'),

        ], $event->id);

        //dd('request: ', $request);

        //dd($request->all());

        return $response;
    }
}
