<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule notifications daily at 7 AM
        $schedule->command('notifications:schedule')
                 ->dailyAt('07:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Clean old notifications weekly
        $schedule->call(function () {
            \App\Models\Notification::where('created_at', '<', now()->subDays(30))
                                   ->where('is_read', true)
                                   ->delete();
        })->weekly()->sundays()->at('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
