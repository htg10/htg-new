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
        // ReminderPlan schedule
        $schedule->command('send:expiry')->daily()->withoutOverlapping();

        // ExpiredPlan schedule
        $schedule->command('send:expired')->daily()->withoutOverlapping();

        // Unified reminder system (processes all active rules)
        $schedule->command('send:reminders')->dailyAt('09:00')->withoutOverlapping();

        // Balance payment reminders (weekly on Monday)
        $schedule->command('send:balance-reminders')->weeklyOn(1, '10:00')->withoutOverlapping();
    }


    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
