<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DetectionEngine;
//use App\Services\Rules\RateLimitRule;

class DetectionServiceProvider extends ServiceProvider{
    public function register(): void{

        //creamos una sola instancia para toda la app

        $this->app->singleton(DetectionEngine::class, function ($app){

            $engine = new DetectionEngine();

            //dd('prueba: el service provider que he creado funciona');
            //$engine->addRule(new RateLimitRule(10));

            return $engine;

        });

    }
}