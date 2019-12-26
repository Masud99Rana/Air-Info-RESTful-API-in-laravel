<?php

namespace App\Providers\v1;

use App\Services\v1\FlightService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
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
        App::bind(FlightService::class, function($app){
            return new FlightService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        Validator::extend('flightstatus', function ($attribute, $value, $parameters, $validator) {
            return $value == 'ontime' || $value == 'delayed'; 
        });
    }
}
