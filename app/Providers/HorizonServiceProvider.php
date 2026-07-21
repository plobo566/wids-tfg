<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Override del método de autorización nativo de Horizon.
     */
    protected function authorization(): void
    {
        $this->gate();

        Horizon::auth(function ($request) {
            
            if (!auth()->guard('web')->check()) {
                
                session()->put('url.intended', url('/horizon'));
                
                throw new \Illuminate\Auth\AuthenticationException('Unauthenticated.', ['web'], '/admin/login');
            }

            return Gate::check('viewHorizon', [auth()->guard('web')->user()]);
        });
    }

    /**
     * Register the Horizon gate.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user) {

            return $user && $user->email === 'pablo@wids.com'; 
        });
    }
}