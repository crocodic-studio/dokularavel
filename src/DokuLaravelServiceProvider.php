<?php

namespace crocodicstudio\dokularavel;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class DokuLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {        
        $this->publishes([  __DIR__.'/Config/dokularavel.php' => config_path('dokularavel.php')],'dokularavel_config');
        $this->publishes([  __DIR__.'/Assets' => public_path('vendor/dokularavel')],'dokularavel_assets');
        $this->loadViewsFrom(__DIR__.'/Views', 'dokularavel');
        
        require __DIR__.'/Helpers/Helper.php';
        require __DIR__.'/routes.php';        
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                 

        $this->app['dokularavel'] = $this->app->share(function ()
        {
            return true;
        });
    }
}
