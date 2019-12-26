<?php

namespace App\Providers\v1;

use App\Services\v1\FlightService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FlightServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        App::bind(FlightService::class, function($app){
            return new FlightService();
        });
    }
}
