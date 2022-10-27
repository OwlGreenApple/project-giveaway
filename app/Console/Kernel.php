<?php

namespace App\Console;

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
        $schedule->command('check:running_events')->everyTenMinutes();
        // $schedule->command('check:membership')->hourly();
        $schedule->command('delete:phone_week')->everyFourHours();
        $schedule->command('check:purchase')->everySixHours();
        $schedule->command('check:membership_terms')->everyFifteenMinutes();
        $schedule->command('check:message')->everyFiveMinutes();
        $schedule->command('running:message')->everyMinute();
        // $schedule->command('reset:counter_send_message')->daily();
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
