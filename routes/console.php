<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| In Laravel 11+ schedules are registered here instead of app/Console/Kernel.php
*/

// Send medication reminders every minute
Schedule::command('medications:send-reminders')->everyMinute();

// Clean up old failed video analyses daily
Schedule::call(function () {
    \App\Models\VideoAnalysis::where('status', 'failed')
        ->where('created_at', '<', now()->subDays(7))
        ->delete();
})->daily();

// Weekly follow-up for inactive students
Schedule::call(function () {
    \App\Models\User::where('role', 'student')
        ->where('last_assessment_at', '<', now()->subDays(30))
        ->where('is_active', true)
        ->get();
})->weekly();

// Hourly scheduled reports
Schedule::command('reports:send-scheduled')->hourly();