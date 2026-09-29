<?php

namespace App\Services;

use App\Models\AssessmentSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIReportService
{
    public function summarise(AssessmentSession $session, array $domainScores, array $topConcerns, array $crisis): array
    {
        if ($this->hasAI()) {
            $ai = $this->callAI($session, $domainScores, $topConcerns, $crisis);
            if ($ai) {
                return $ai;
            }
        }

        return $this->localSummary($session, $domainScores, $topConcerns, $crisis);
    }

    protected function callAI(AssessmentSession $session, array $domainScores, array $top, array $crisis): ?array
    {
        try {
            $prompt = "You are a clinical assistant. Summarise this student's health screening in 4 short sections: "
                . "'overview', 'areas_of_concern', 'strengths', 'suggested_next_steps'. Return JSON.\n\n"
                . "Risk score: {$session->running_risk_score}/100 ({$session->current_risk_level})\n"
                . "Sentiment: {$session->running_sentiment}\n"
                . "Domain scores: " . json_encode($domainScores) . "\n"
                . "Top concerns: " . json_encode($top) . "\n"
                . "Crisis indicators: " . json_encode($crisis);

            $response = Http::withToken(config('sentiment.openai_api_key'))
                ->timeout(20)
                ->post(config('sentiment.openai_api_url'), [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Output valid JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->successful()) {
                $data = json_decode($response->json('choices.0.message.content'), true);
                if (is_array($data)) {
                    $data['source'] = 'ai';
                    return $data;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AI report failed: ' . $e->getMessage());
        }

        return null;
    }

    protected function localSummary(AssessmentSession $session, array $domainScores, array $top, array $crisis): array
    {
        $level = $session->current_risk_level;
        $score = $session->running_risk_score;

        $overview = match ($level) {
            'critical' => "The assessment indicates a critical level of concern (score {$score}/100). Immediate support is strongly recommended.",
            'high' => "The assessment indicates a high level of concern (score {$score}/100). A follow-up with a counsellor within 48 hours is advised.",
            'moderate' => "The assessment shows moderate concern (score {$score}/100). Self-care and monitoring are recommended.",
            default => "The assessment shows a low level of concern (score {$score}/100). Continue healthy habits and check in regularly.",
        };

        $concernLabels = array_map(fn ($c) => $c['label'] . " ({$c['level']})", $top);
        $concerns = $concernLabels ?: ['No significant areas flagged.'];

        $strengths = [];
        if ($session->running_sentiment > 0.2) $strengths[] = 'Positive emotional tone in responses';
        if (empty($crisis)) $strengths[] = 'No crisis indicators detected';

        $next = match ($level) {
            'critical' => ['Contact the campus counselling centre immediately', 'Reach out to a trusted person today', 'Call crisis helpline: +263 4 123 4567'],
            'high' => ['Book a counselling appointment within 48 hours', 'Practice daily grounding exercises', 'Avoid isolation'],
            'moderate' => ['Schedule a check-in with a peer counsellor', 'Maintain sleep and exercise routines'],
            default => ['Continue regular self-care', 'Re-assess in 4 weeks'],
        };

        return [
            'overview' => $overview,
            'areas_of_concern' => $concerns,
            'strengths' => $strengths ?: ['Assessment completed — help-seeking is a strength'],
            'suggested_next_steps' => $next,
            'source' => 'local',
        ];
    }

    protected function hasAI(): bool
    {
        return !empty(config('sentiment.openai_api_key'));
    }
}