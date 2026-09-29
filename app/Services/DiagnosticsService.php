<?php

namespace App\Services;

use App\Models\AssessmentSession;
use App\Models\AssessmentReport;
use Illuminate\Support\Str;

class DiagnosticsService
{
    public function __construct(
        protected AdaptiveEngineService $engine,
        protected RecommendationService $recommendations,
        protected AIReportService $aiReports,
    ) {
    }

    public function generateReport(AssessmentSession $session): AssessmentReport
    {
        $answers = $session->answers()->with('question')->get();
        $domainScores = $this->engine->domainScores($answers);

        $topConcerns = collect($domainScores)
            ->filter(fn ($v) => $v >= 2.5)
            ->sortDesc()
            ->take(6)
            ->map(fn ($score, $domain) => [
                'domain' => $domain,
                'label' => CategoryQuestionBank::CATEGORIES[$domain]['label'] ?? ucwords(str_replace('_', ' ', $domain)),
                'score' => $score,
                'level' => $this->levelFromScore($score),
            ])
            ->values()
            ->all();

        $protective = $this->detectProtectiveFactors($answers);
        $crisis = $this->detectCrisisIndicators($answers);

        $summary = $this->aiReports->summarise($session, $domainScores, $topConcerns, $crisis);
        $recs = $this->recommendations->generateFor($session, $topConcerns, $crisis);

        return AssessmentReport::create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'final_risk_score' => $session->running_risk_score,
            'final_risk_level' => $session->current_risk_level,
            'final_sentiment' => $session->running_sentiment,
            'domain_scores' => $domainScores,
            'top_concerns' => $topConcerns,
            'protective_factors' => $protective,
            'crisis_indicators' => $crisis,
            'diagnostic_summary' => $summary,
            'recommendations' => $recs,
            'generated_at' => now(),
        ]);
    }

    protected function levelFromScore(float $score): string
    {
        if ($score >= 4.5) return 'critical';
        if ($score >= 3.5) return 'high';
        if ($score >= 2.5) return 'moderate';
        return 'low';
    }

    protected function detectProtectiveFactors($answers): array
    {
        $factors = [];
        foreach ($answers as $a) {
            $text = strtolower((string) $a->raw_answer);
            if (str_contains($text, 'friend') || str_contains($text, 'family')) $factors[] = 'Social support present';
            if (str_contains($text, 'exercise') || str_contains($text, 'gym')) $factors[] = 'Physical activity';
            if ($a->sentiment_score > 0.3) $factors[] = 'Positive emotional expression';
        }
        return array_values(array_unique($factors));
    }

    protected function detectCrisisIndicators($answers): array
    {
        $indicators = [];
        foreach ($answers as $a) {
            if ($a->crisis_detected) $indicators[] = 'Crisis keywords in "' . Str::limit($a->raw_answer, 40) . '"';
            if ($a->self_harm_flagged) $indicators[] = 'Self-harm keywords detected';
            if ($a->substance_flagged) $indicators[] = 'Substance use keywords detected';
        }
        return $indicators;
    }
}