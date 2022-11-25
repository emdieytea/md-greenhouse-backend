<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        
        $date = Carbon::now()->format('Y-m-d');
        
        // $schedule
        //     ->command('command:seed_data')
        //     ->hourly();
        //     // ->appendOutputTo(storage_path('logs/seed-' . $date . '.log'))
        //     // ->withoutOverlapping();

        $schedule
            ->command('command:check_batch_status')
            ->everyMinute()
            ->appendOutputTo(storage_path('logs/check-status-' . $date . '.log'))
            ->withoutOverlapping();
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
