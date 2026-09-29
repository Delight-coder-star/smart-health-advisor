<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
		  $schedule->command('reports:send-scheduled')->hourly();
        // Clean up old failed video analyses
        $schedule->call(function () {
            \App\Models\VideoAnalysis::where('status', 'failed')
                ->where('created_at', '<', now()->subDays(7))
                ->delete();
        })->daily();

        // Send follow-up reminders
        $schedule->call(function () {
            $users = \App\Models\User::students()
                ->where('last_assessment_at', '<', now()->subDays(30))
                ->where('is_active', true)
                ->get();

            foreach ($users as $user) {
                // Could dispatch notification here
            }
        })->weekly();
		
		  $schedule->command('medications:send-reminders')->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
	
}