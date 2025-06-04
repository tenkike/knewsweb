<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades;
use App\Library\Crud\DataTables;



class AdminViewComposerServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->register(AdminRouteServiceProvider::class);
        $this->app->singleton('datatables', function () {
        return new \App\Library\Crud\DataTables();
    });
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {       
     //       
    }

    
    

}
