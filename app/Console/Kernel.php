<?php

namespace App\Console;

use App\Console\Commands\RevokeExpiredAccess;
use App\Console\Commands\SendAccessExpiringReminders;
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
         $schedule->command(RevokeExpiredAccess::class)
                    ->everyMinute()
                    ->withoutOverlapping()
                    ->runInBackground();

        $schedule->command(SendAccessExpiringReminders::class, ['--six-hour-reminders'])
                ->everyMinute()
                ->withoutOverlapping()
                ->runInBackground();

        $schedule->command(SendAccessExpiringReminders::class, ['--twelve-hour-reminders'])
                ->everyMinute()
                ->withoutOverlapping()
                ->runInBackground();

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
