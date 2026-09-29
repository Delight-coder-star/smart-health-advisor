<?php

namespace App\Http\Controllers;

use App\Models\SymptomQuery;
use App\Models\AssessmentSession;
use App\Models\AssessmentReport;
use App\Models\MedicationLog;
use App\Services\SentimentAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct(
        protected SentimentAnalysisService $sentiment
    ) {
    }

    /**
     * Show the report hub.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $type   = $request->input('type', 'weekly');
        [$start, $end] = $this->range($type, $request);

        $report = $this->buildReport($userId, $type, $start, $end);

        return view('reports.show', compact('report'));
    }

    /**
     * AJAX: run sentiment analysis on the period.
     */
    public function sentiment(Request $request)
    {
        $userId = Auth::id();
        $type   = $request->input('type', 'weekly');
        [$start, $end] = $this->range($type, $request);

        $queries = SymptomQuery::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->with('response')
            ->get();

        $sessions = AssessmentSession::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $reports = AssessmentReport::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $result = $this->analyzeSentiment($queries, $sessions, $reports);

        return response()->json($result);
    }

    /**
     * Export the report as CSV, or redirect for PDF (browser print).
     */
    public function export(Request $request, string $format)
    {
        $userId = Auth::id();
        $type   = $request->input('type', 'weekly');
        [$start, $end] = $this->range($type, $request);
        $report = $this->buildReport($userId, $type, $start, $end);

        if ($format === 'csv') {
            $filename = "report-{$type}-" . now()->format('Y-m-d') . ".csv";
            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($report) {
                $f = fopen('php://output', 'w');

                // Header
                fputcsv($f, ['Smart Health Advisor Report']);
                fputcsv($f, ['Type', ucfirst($report['type'])]);
                fputcsv($f, ['Period', $report['from'] . ' to ' . $report['to']]);
                fputcsv($f, ['Generated', $report['generated']]);
                fputcsv($f, []);

                // Summary
                fputcsv($f, ['Summary']);
                fputcsv($f, ['Questions Asked', $report['totals']['queries']]);
                fputcsv($f, ['Assessment Sessions', $report['totals']['sessions']]);
                fputcsv($f, ['Assessment Reports', $report['totals']['reports']]);
                fputcsv($f, ['Medication Doses Taken', $report['totals']['taken']]);
                fputcsv($f, ['Medication Doses Missed', $report['totals']['missed']]);
                fputcsv($f, ['Medication Adherence %', $report['totals']['adherence'] ?? '—']);
                fputcsv($f, []);

                // Assessment Sessions
                if (!empty($report['sessions'])) {
                    fputcsv($f, ['Assessment Sessions']);
                    fputcsv($f, ['Date', 'Mode', 'Categories', 'Risk Level', 'Risk Score', 'Sentiment', 'Status']);
                    foreach ($report['sessions'] as $s) {
                        fputcsv($f, [
                            $s['date'],
                            $s['mode'],
                            implode('; ', $s['categories']),
                            $s['risk_level'],
                            $s['risk_score'],
                            $s['sentiment'],
                            $s['status'],
                        ]);
                    }
                    fputcsv($f, []);
                }

                // AI Advisor Queries
                if (!empty($report['advisor'])) {
                    fputcsv($f, ['AI Health Advisor Queries']);
                    fputcsv($f, ['Date', 'Question', 'Category', 'Urgency', 'Sentiment']);
                    foreach ($report['advisor'] as $q) {
                        fputcsv($f, [
                            $q['date'],
                            $q['question'],
                            $q['category'],
                            $q['urgency'],
                            $q['sentiment'],
                        ]);
                    }
                    fputcsv($f, []);
                }

                // Category breakdown
                if (!empty($report['by_category'])) {
                    fputcsv($f, ['Questions by Category']);
                    foreach ($report['by_category'] as $cat => $count) {
                        fputcsv($f, [ucfirst($cat), $count]);
                    }
                    fputcsv($f, []);
                }

                // Urgency breakdown
                if (!empty($report['by_urgency'])) {
                    fputcsv($f, ['Questions by Urgency']);
                    foreach ($report['by_urgency'] as $urg => $count) {
                        fputcsv($f, [ucfirst($urg), $count]);
                    }
                }

                fclose($f);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'pdf') {
            return redirect()->route('reports.index', [
                'type'  => $type,
                'print' => 1,
                'from'  => $request->input('from'),
                'to'    => $request->input('to'),
            ]);
        }

        abort(404);
    }

    /**
     * Build the full report — combines assessments, advisor, medications, sentiment.
     */
    protected function buildReport(int $userId, string $type, Carbon $start, Carbon $end): array
    {
        // Symptom queries (AI Advisor)
        $queries = SymptomQuery::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->with('response')
            ->get();

        // Assessment sessions
        $sessions = AssessmentSession::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Assessment reports
        $reports = AssessmentReport::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Medication logs
        $medLogs = MedicationLog::where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $taken  = $medLogs->where('status', 'taken')->count();
        $missed = $medLogs->where('status', 'missed')->count();
        $adherence = ($taken + $missed) > 0
            ? round(($taken / ($taken + $missed)) * 100)
            : null;

        // Instant sentiment preview (no prediction)
        $sentiment = $this->analyzeSentiment($queries, $sessions, $reports);

        return [
            'type'      => $type,
            'from'      => $start->toDateString(),
            'to'        => $end->toDateString(),
            'generated' => now()->format('M d, Y H:i'),

            'totals' => [
                'queries'   => $queries->count(),
                'sessions'  => $sessions->count(),
                'reports'   => $reports->count(),
                'taken'     => $taken,
                'missed'    => $missed,
                'adherence' => $adherence,
            ],

            'sentiment'   => $sentiment,
            'by_category' => $queries->groupBy('category')->map->count()->toArray(),
            'by_urgency'  => $queries->groupBy('urgency')->map->count()->toArray(),

            // Assessment sessions summary
            'sessions' => $sessions->map(fn ($s) => [
                'id'          => $s->id,
                'date'        => $s->started_at ? $s->started_at->format('M d, Y H:i') : '',
                'mode'        => $s->mode,
                'status'      => $s->status,
                'risk_level'  => $s->current_risk_level,
                'risk_score'  => $s->running_risk_score,
                'sentiment'   => $s->running_sentiment,
                'categories'  => $s->selected_categories ?? [],
                'report_id'   => $s->report->id ?? null,
            ])->values()->all(),

            // AI Advisor queries
            'advisor' => $queries->take(30)->map(fn ($q) => [
                'id'        => $q->id,
                'date'      => $q->created_at->format('M d, H:i'),
                'question'  => $q->question,
                'category'  => $q->category ?? 'general',
                'urgency'   => $q->urgency,
                'sentiment' => $q->response->sentiment_score ?? 0,
                'label'     => $q->response->sentiment_label ?? 'neutral',
                'has_ai'    => $q->response && $q->response->source === 'openai',
            ])->values()->all(),
        ];
    }

    /**
     * Analyze sentiment from ALL user activities:
     *  - Assessment sessions
     *  - AI advisor queries
     *  - Assessment reports
     */
    protected function analyzeSentiment($queries, $sessions, $reports): array
    {
        $assessScores  = [];
        $advisorScores = [];
        $reportScores  = [];
        $byCategory    = [];

        // Assessment sessions
        foreach ($sessions as $s) {
            if ($s->running_sentiment !== null) {
                $assessScores[] = $s->running_sentiment;
            }
        }

        // AI advisor queries
        foreach ($queries as $q) {
            if ($q->response && $q->response->sentiment_score !== null) {
                $advisorScores[] = $q->response->sentiment_score;

                $cat = $q->category ?? 'general';
                $byCategory[$cat][] = $q->response->sentiment_score;
            }
        }

        // Assessment reports
        foreach ($reports as $r) {
            if ($r->final_sentiment !== null) {
                $reportScores[] = $r->final_sentiment;
            }
        }

        // Combined
        $all = array_merge($assessScores, $advisorScores, $reportScores);

        if (empty($all)) {
            return [
                'average'  => 0,
                'label'    => 'neutral',
                'positive' => 0,
                'neutral'  => 0,
                'negative' => 0,
                'samples'  => 0,
                'by_source' => [
                    'assessments' => ['average' => 0, 'samples' => 0],
                    'advisor'     => ['average' => 0, 'samples' => 0],
                    'reports'     => ['average' => 0, 'samples' => 0],
                ],
                'by_category' => [],
            ];
        }

        $avg = round(array_sum($all) / count($all), 2);

        // Per-category averages (advisor queries)
        $categoryAverages = [];
        foreach ($byCategory as $cat => $scores) {
            $categoryAverages[$cat] = round(array_sum($scores) / count($scores), 2);
        }

        return [
            'average'  => $avg,
            'label'    => $this->labelFor($avg),
            'positive' => count(array_filter($all, fn ($s) => $s > 0.2)),
            'neutral'  => count(array_filter($all, fn ($s) => $s >= -0.2 && $s <= 0.2)),
            'negative' => count(array_filter($all, fn ($s) => $s < -0.2)),
            'samples'  => count($all),

            'by_source' => [
                'assessments' => [
                    'average' => count($assessScores) > 0
                        ? round(array_sum($assessScores) / count($assessScores), 2)
                        : 0,
                    'samples' => count($assessScores),
                ],
                'advisor' => [
                    'average' => count($advisorScores) > 0
                        ? round(array_sum($advisorScores) / count($advisorScores), 2)
                        : 0,
                    'samples' => count($advisorScores),
                ],
                'reports' => [
                    'average' => count($reportScores) > 0
                        ? round(array_sum($reportScores) / count($reportScores), 2)
                        : 0,
                    'samples' => count($reportScores),
                ],
            ],

            'by_category' => $categoryAverages,
        ];
    }

    /**
     * Convert a type string to [start, end] Carbon instances.
     */
    protected function range(string $type, Request $request): array
    {
        if ($type === 'custom' && $request->filled('from') && $request->filled('to')) {
            return [
                Carbon::parse($request->from)->startOfDay(),
                Carbon::parse($request->to)->endOfDay(),
            ];
        }

        $now = now();
        return match ($type) {
            'daily'   => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default   => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
        };
    }

    /**
     * Map a numeric sentiment score to a label.
     */
    protected function labelFor(float $score): string
    {
        if ($score > 0.3)   return 'positive';
        if ($score > 0.05)  return 'slightly_positive';
        if ($score > -0.05) return 'neutral';
        if ($score > -0.3)  return 'slightly_negative';
        return 'negative';
    }
}