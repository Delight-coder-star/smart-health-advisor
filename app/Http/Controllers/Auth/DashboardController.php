<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AssessmentSession;
use App\Models\AssessmentReport;
use App\Models\Recommendation;
use App\Models\HealthTip;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Student dashboard (also handles role-based redirects).
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isAdvisor()) {
            return redirect()->route('advisor.dashboard');
        }

        // Active session (for resume banner)
        $activeSession = AssessmentSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        // Latest completed report
        $latestReport = AssessmentReport::where('user_id', $user->id)
            ->latest('generated_at')
            ->first();

        // Pending recommendations
        $recommendations = Recommendation::where('user_id', $user->id)
            ->where('is_completed', false)
            ->orderByRaw("CASE priority
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
                ELSE 5 END")
            ->limit(6)
            ->get();

        // Health tips
        $healthTips = HealthTip::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->limit(4)
            ->get();

        // Chart data — one point per completed report
        $assessmentHistory = AssessmentReport::where('user_id', $user->id)
            ->orderBy('generated_at', 'asc')
            ->limit(15)
            ->get();

        // Recent sessions
        $recentSessions = AssessmentSession::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Stats
        $totalSessions = AssessmentSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $streakDays = $this->computeStreak($user->id);

        $domainsCovered = AssessmentSession::where('user_id', $user->id)
            ->whereNotNull('domains_covered')
            ->get()
            ->pluck('domains_covered')
            ->flatten()
            ->unique()
            ->count();

        $pendingRecommendations = Recommendation::where('user_id', $user->id)
            ->where('is_completed', false)
            ->count();

        $stats = [
            'total_sessions'          => $totalSessions,
            'streak_days'             => $streakDays,
            'domains_covered'         => $domainsCovered,
            'pending_recommendations' => $pendingRecommendations,
        ];

        return view('dashboard.student', compact(
            'user',
            'activeSession',
            'latestReport',
            'recommendations',
            'healthTips',
            'assessmentHistory',
            'recentSessions',
            'stats'
        ));
    }

    /**
     * Advisor dashboard.
     */
    public function advisorDashboard()
    {
        $user = Auth::user();

        $alerts = Alert::where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            })
            ->whereIn('status', ['pending', 'acknowledged'])
            ->orderByRaw("CASE severity WHEN 'critical' THEN 1 WHEN 'warning' THEN 2 ELSE 3 END")
            ->orderBy('created_at')
            ->limit(20)
            ->get();

        $highRiskStudents = User::where('role', 'student')
            ->whereIn('risk_level', ['high', 'critical'])
            ->orderBy('risk_score', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_students' => User::where('role', 'student')->count(),
            'high_risk'      => User::where('role', 'student')->where('risk_level', 'high')->count(),
            'critical'       => User::where('role', 'student')->where('risk_level', 'critical')->count(),
            'pending_alerts' => Alert::where('status', 'pending')->count(),
        ];

        return view('dashboard.advisor', compact('alerts', 'highRiskStudents', 'stats'));
    }

    /**
     * Admin dashboard.
     */
    public function adminDashboard()
    {
        $stats = [
            'total_users'           => User::count(),
            'total_students'        => User::where('role', 'student')->count(),
            'total_advisors'        => User::where('role', 'advisor')->count(),
            'total_assessments'     => AssessmentSession::count(),
            'assessments_today'     => AssessmentSession::whereDate('created_at', today())->count(),
            'assessments_this_week' => AssessmentSession::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
            'high_risk_count'       => User::where('risk_level', 'high')->count(),
            'critical_count'        => User::where('risk_level', 'critical')->count(),
            'pending_alerts'        => Alert::where('status', 'pending')->count(),
            'resolved_alerts'       => Alert::where('status', 'resolved')->count(),
        ];

        $recentAssessments = AssessmentSession::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $riskDistribution = [
            'low'      => User::where('risk_level', 'low')->count(),
            'moderate' => User::where('risk_level', 'moderate')->count(),
            'high'     => User::where('risk_level', 'high')->count(),
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

    /**
     * Count consecutive days with at least one session.
     */
    protected function computeStreak(int $userId): int
    {
        $dates = AssessmentSession::where('user_id', $userId)
            ->whereIn('status', ['completed', 'active'])
            ->selectRaw('DATE(created_at) as day')
            ->groupBy('day')
            ->orderBy('day', 'desc')
            ->pluck('day')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->all();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $cursor = now()->toDateString();

        if ($dates[0] !== $cursor) {
            $yesterday = now()->subDay()->toDateString();
            if ($dates[0] !== $yesterday) {
                return 0;
            }
            $cursor = $yesterday;
        }

        foreach ($dates as $d) {
            if ($d === $cursor) {
                $streak++;
                $cursor = Carbon::parse($cursor)->subDay()->toDateString();
            } else {
                break;
            }
        }

        return $streak;
    }
}