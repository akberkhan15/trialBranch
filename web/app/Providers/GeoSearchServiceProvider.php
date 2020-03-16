<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GeoSearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\GeoSearchService', function ($app) {
            return new GeoSearchService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
