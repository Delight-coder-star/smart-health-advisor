<?php

namespace App\Console\Commands;

use App\Mail\MedicationReminderMail;
use App\Models\MedicationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMedicationReminders extends Command
{
    protected $signature = 'medications:send-reminders {--force : Send regardless of the current minute}';
    protected $description = 'Send scheduled medication reminder emails to users';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $now   = now()->format('H:i:00');

        // Match reminders for the current minute (or all active ones if --force)
        $query = MedicationReminder::with(['medication.user', 'user'])
            ->where('is_active', true);

        if (!$force) {
            $query->where('reminder_time', $now);
        }

        $reminders = $query->get();

        if ($reminders->isEmpty()) {
            $this->info("No reminders due at {$now}.");
            return self::SUCCESS;
        }

        $sent   = 0;
        $failed = 0;

        foreach ($reminders as $reminder) {
            $med  = $reminder->medication;
            $user = $reminder->user;

            // Safety checks
            if (!$med || !$user || !$user->email) {
                Log::warning("Reminder {$reminder->id}: missing medication or user email.");
                $failed++;
                continue;
            }

            if (!$med->is_active) {
                continue;
            }

            try {
                Mail::to($user->email)->send(new MedicationReminderMail($med));

                $reminder->update(['last_sent_at' => now()]);
                $sent++;

                $this->line("✓ Sent to {$user->email} for {$med->name}");
            } catch (\Throwable $e) {
                $failed++;
                Log::error("Reminder {$reminder->id} failed: {$e->getMessage()}");
                $this->error("✗ Failed for {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Done. Sent: {$sent}, Failed: {$failed}.");

        return self::SUCCESS;
    }
}