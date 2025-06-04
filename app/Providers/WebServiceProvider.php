<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Web\WebRoutes;

class WebServiceProvider extends ServiceProvider
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
    public function boot(WebRoutes $WebRoutes)
    {
        $WebRoutes->Routes();
    }
}
