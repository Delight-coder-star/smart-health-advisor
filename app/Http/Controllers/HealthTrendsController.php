<?php

namespace App\Http\Controllers;

use App\Models\AssessmentSession;
use App\Models\AssessmentReport;
use App\Models\SymptomQuery;
use App\Models\MedicationLog;
use Illuminate\Support\Facades\Auth;

class HealthTrendsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $sessions = AssessmentSession::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->get();

        $queries = SymptomQuery::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy(fn ($q) => $q->created_at->toDateString())
            ->map->count();

        $categories = SymptomQuery::where('user_id', $userId)
            ->whereNotNull('category')
            ->get()
            ->groupBy('category')
            ->map->count();

        $logs = MedicationLog::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $taken = $logs->where('status', 'taken')->count();
        $missed = $logs->where('status', 'missed')->count();
        $adherence = ($taken + $missed) > 0 ? round(($taken / ($taken + $missed)) * 100) : 0;

        $latestReport = AssessmentReport::where('user_id', $userId)
            ->latest('generated_at')
            ->first();

        return view('trends.index', compact(
            'sessions',
            'queries',
            'categories',
            'taken',
            'missed',
            'adherence',
            'latestReport'
        ));
    }
}