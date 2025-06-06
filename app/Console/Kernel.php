<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Binafy\LaravelUserMonitoring\Commands\RemoveVisitMonitoringRecordsCommand;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\ClearSchemaCache::class,
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
         $schedule->command(RemoveVisitMonitoringRecordsCommand::class)->hourly();
    }
    

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
