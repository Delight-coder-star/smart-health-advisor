<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Assessment;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvisorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $alerts = Alert::where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereNull('assigned_to');
            })
            ->whereIn('status', ['pending', 'acknowledged'])
            ->with(['user', 'assessment'])
            ->orderByRaw("CASE severity WHEN 'critical' THEN 1 WHEN 'warning' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'asc')
            ->get();

        $highRiskStudents = User::students()
            ->atRisk()
            ->with('latestAssessment')
            ->orderBy('risk_score', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_students' => User::students()->count(),
            'high_risk' => User::students()->where('risk_level', 'high')->count(),
            'critical' => User::students()->where('risk_level', 'critical')->count(),
            'pending_alerts' => Alert::where('status', 'pending')->count(),
        ];

        return view('dashboard.advisor', compact('alerts', 'highRiskStudents', 'stats'));
    }

    public function students(Request $request)
    {
        $query = User::students()->with('latestAssessment');

        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('risk_score', 'desc')->paginate(20);

        return view('advisor.students', compact('students'));
    }

    public function studentDetail(User $student)
    {
        if (!$student->isStudent()) {
            abort(404);
        }

        $student->load([
            'assessments' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(10),
            'recommendations' => fn ($q) => $q->orderBy('created_at', 'desc'),
            'alerts' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(10),
        ]);

        $assessmentHistory = $student->assessments()
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get(['created_at', 'total_score', 'sentiment_score', 'risk_level']);

        return view('advisor.student-detail', compact('student', 'assessmentHistory'));
    }

    public function acknowledgeAlert(Alert $alert)
    {
        $alert->acknowledge(Auth::id());

        return back()->with('success', 'Alert acknowledged.');
    }

    public function resolveAlert(Request $request, Alert $alert)
    {
        $validated = $request->validate([
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $alert->resolve($validated['resolution_notes'] ?? null);

        return back()->with('success', 'Alert resolved.');
    }

    public function addRecommendation(Request $request, User $student)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:self_help,counseling,medical,emergency,lifestyle'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'action_items' => ['nullable', 'array'],
            'action_items.*' => ['string', 'max:255'],
        ]);

        Recommendation::create([
            'user_id' => $student->id,
            'assessment_id' => $student->latestAssessment?->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'priority' => $validated['priority'],
            'action_items' => $validated['action_items'] ?? [],
        ]);

        return back()->with('success', 'Recommendation added successfully.');
    }
}