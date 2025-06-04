<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         // DB::whenQueryingForLongerThan(500, function (\Connection $connection) {
           // dd($connection);
         //   Log::warning("Database queries exceeded 5 seconds on {$connection->getName()}");
          //  print_r("Database queries exceeded 5 seconds on {$connection->getName()}");
       // });
    }
}
