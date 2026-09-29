<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Assessment;
use App\Models\AssessmentSession;
use App\Models\HealthTip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::students()->count(),
            'total_advisors' => User::advisors()->count(),
            'total_assessments' => AssessmentSession::count(),
            'assessments_today' => AssessmentSession::whereDate('created_at', today())->count(),
            'assessments_this_week' => AssessmentSession::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
            'high_risk_count' => User::where('risk_level', 'high')->count(),
            'critical_count' => User::where('risk_level', 'critical')->count(),
            'pending_alerts' => Alert::where('status', 'pending')->count(),
            'resolved_alerts' => Alert::where('status', 'resolved')->count(),
        ];

        $recentAssessments = AssessmentSession::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $riskDistribution = [
            'low' => User::where('risk_level', 'low')->count(),
            'moderate' => User::where('risk_level', 'moderate')->count(),
            'high' => User::where('risk_level', 'high')->count(),
            'critical' => User::where('risk_level', 'critical')->count(),
        ];

        $assessmentTrend = AssessmentSession::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'recentAssessments',
            'riskDistribution',
            'assessmentTrend'
        ));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

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

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'student_id' => ['nullable', 'string', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:student,advisor,admin'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'student_id' => $validated['student_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'student_id' => ['nullable', 'string', 'unique:users,student_id,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'in:student,advisor,admin'],
            'is_active' => ['boolean'],
        ]);

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function assessments(Request $request)
    {
        $query = AssessmentSession::with('user');

        if ($request->filled('risk_level')) {
            $query->where('current_risk_level', $request->risk_level);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $assessments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.assessments', compact('assessments'));
    }

    public function alerts(Request $request)
    {
        $query = Alert::with(['user', 'assessment', 'assignedTo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $alerts = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.alerts', compact('alerts'));
    }

    public function healthTips()
    {
        $tips = HealthTip::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.tips', compact('tips'));
    }

    public function createHealthTip(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'tags' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'priority' => ['integer', 'min:0', 'max:100'],
        ]);

        HealthTip::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'icon' => $validated['icon'] ?? null,
            'priority' => $validated['priority'] ?? 0,
        ]);

        return back()->with('success', 'Health tip created successfully.');
    }

    public function updateHealthTip(Request $request, HealthTip $tip)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'tags' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'priority' => ['integer', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        $tip->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : [],
            'icon' => $validated['icon'] ?? null,
            'priority' => $validated['priority'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Health tip updated successfully.');
    }

    public function deleteHealthTip(HealthTip $tip)
    {
        $tip->delete();

        return back()->with('success', 'Health tip deleted successfully.');
    }
}