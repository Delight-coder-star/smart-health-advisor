<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationController extends Controller
{
       public function index()
    {
        $medications = Medication::where('user_id', Auth::id())
            ->with('reminders')
            ->latest()
            ->get();

        $todayLogs = MedicationLog::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->get();

        // Pre-built reminders array for the browser notification script
        $browserReminders = $medications
            ->where('is_active', true)
            ->flatMap(function ($m) {
                return $m->reminders->map(function ($r) use ($m) {
                    return [
                        'time'   => substr($r->reminder_time, 0, 5),
                        'name'   => $m->name,
                        'dosage' => $m->dosage,
                    ];
                });
            })
            ->values()
            ->all();

        return view('medications.index', compact('medications', 'todayLogs', 'browserReminders'));
    }

    public function create()
    {
        return view('medications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dosage' => ['nullable', 'string', 'max:100'],
            'frequency' => ['nullable', 'string', 'max:100'],
            'condition' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reminder_times' => ['nullable', 'array'],
            'reminder_times.*' => ['nullable', 'date_format:H:i'],
        ]);

        $med = Medication::create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'dosage' => $data['dosage'] ?? null,
            'frequency' => $data['frequency'] ?? null,
            'condition' => $data['condition'] ?? null,
            'start_date' => $data['start_date'] ?? now()->toDateString(),
            'end_date' => $data['end_date'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach (($data['reminder_times'] ?? []) as $time) {
            if ($time) {
                MedicationReminder::create([
                    'medication_id' => $med->id,
                    'user_id' => Auth::id(),
                    'reminder_time' => $time . ':00',
                    'day_of_week' => 'daily',
                ]);
            }
        }

        return redirect()->route('medications.index')
            ->with('success', 'Medication added with reminders.');
    }

    public function log(Request $request, Medication $medication)
    {
        abort_unless($medication->user_id === Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:taken,missed,skipped,snoozed'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        MedicationLog::create([
            'medication_id' => $medication->id,
            'user_id' => Auth::id(),
            'status' => $data['status'],
            'scheduled_for' => now(),
            'taken_at' => $data['status'] === 'taken' ? now() : null,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Medication logged.');
    }

    public function destroy(Medication $medication)
    {
        abort_unless($medication->user_id === Auth::id(), 403);
        $medication->delete();
        return back()->with('success', 'Medication removed.');
    }
}