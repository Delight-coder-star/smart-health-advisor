<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Alert;
use App\Models\SessionAnswer;

class RiskAssessmentService
{
    protected array $questionWeights = [
        'substance_use' => 3,
        'substance_frequency' => 2,
        'mood_changes' => 2,
        'sleep_issues' => 2,
        'appetite_changes' => 1.5,
        'energy_levels' => 1.5,
        'concentration' => 1.5,
        'social_withdrawal' => 2,
        'anxiety_levels' => 2.5,
        'depression_signs' => 3,
        'hallucinations' => 4,
        'self_harm' => 5,
        'suicidal_thoughts' => 5,
        'support_system' => 1,
        'coping_mechanisms' => 1.5,
    ];

    public function __construct(
        protected SentimentAnalysisService $sentiment
    ) {
    }

    public function calculateRiskScore(array $responses): array
    {
        $totalScore = 0;
        $maxPossibleScore = 0;
        $indicators = [];
        $crisisDetected = false;

        foreach ($responses as $key => $response) {
            $weight = $this->questionWeights[$key] ?? 1;
            $maxPossibleScore += $weight * 5;

            $text = is_array($response) ? ($response['text'] ?? '') : (string) $response;
            $score = is_array($response) ? ($response['score'] ?? null) : null;

            $sentiment = $text ? $this->sentiment->analyze($text) : ['score' => 0, 'crisis_detected' => false];

            if ($score === null) {
                $score = $this->mapSentimentToScore($sentiment['score'] ?? 0);
            }

            $totalScore += $score * $weight;

            if ($score >= 4) {
                $indicators[] = [
                    'key' => $key,
                    'text' => $text,
                    'severity' => $score >= 5 ? 'critical' : 'high',
                    'sentiment' => $sentiment,
                ];
            }

            if ($sentiment['crisis_detected'] ?? false) {
                $crisisDetected = true;
            }
        }

        $normalizedScore = $maxPossibleScore > 0
            ? (int) round(($totalScore / $maxPossibleScore) * 100)
            : 0;

        $riskLevel = $this->getRiskLevel($normalizedScore, $crisisDetected);

        return [
            'total_score' => $normalizedScore,
            'risk_level' => $riskLevel,
            'indicators' => $indicators,
            'crisis_detected' => $crisisDetected,
            'requires_attention' => in_array($riskLevel, ['high', 'critical'], true),
        ];
    }

    protected function mapSentimentToScore(float $sentimentScore): int
    {
        if ($sentimentScore >= 0.5) return 1;
        if ($sentimentScore >= 0.1) return 2;
        if ($sentimentScore > -0.1) return 3;
        if ($sentimentScore > -0.5) return 4;
        return 5;
    }

    protected function getRiskLevel(int $score, bool $crisisDetected = false): string
    {
        if ($crisisDetected) return 'critical';
        if ($score >= 75) return 'critical';
        if ($score >= 50) return 'high';
        if ($score >= 25) return 'moderate';
        return 'low';
    }

    public function processAssessment(Assessment $assessment): void
    {
        $user = $assessment->user;

        $user->update([
            'risk_score' => $assessment->total_score,
            'risk_level' => $assessment->risk_level,
            'last_assessment_at' => now(),
        ]);

        if ($assessment->requires_attention) {
            $this->createAlert($assessment);
        }
    }

    protected function createAlert(Assessment $assessment): void
    {
        $severity = $assessment->risk_level === 'critical' ? 'critical' : 'warning';

        Alert::create([
            'user_id' => $assessment->user_id,
            'assessment_id' => $assessment->id,
            'type' => $assessment->risk_level === 'critical' ? 'emergency' : 'risk_alert',
            'severity' => $severity,
            'title' => $assessment->risk_level === 'critical'
                ? 'Critical Risk Alert - Immediate Attention Required'
                : 'High Risk Alert - Follow-up Needed',
            'message' => sprintf(
                'Student %s (ID: %s) has been assessed with a %s risk level (Score: %d). Immediate attention may be required.',
                $assessment->user->name,
                $assessment->user->student_id ?? 'N/A',
                $assessment->risk_level,
                $assessment->total_score
            ),
            'status' => 'pending',
        ]);

        $assessment->update(['alert_sent' => true]);
    }

    /**
     * Raise a crisis alert from an in-session answer.
     */
    public function raiseCrisisAlert(\App\Models\User $user, SessionAnswer $answer): void
    {
        Alert::create([
            'user_id' => $user->id,
            'assessment_id' => null,
            'type' => 'emergency',
            'severity' => 'critical',
            'title' => 'Crisis Flag Raised During Assessment',
            'message' => 'User ' . $user->name . ' provided a response during assessment that triggered a crisis flag: "'
                . \Illuminate\Support\Str::limit((string) $answer->raw_answer, 100) . '"',
            'status' => 'pending',
        ]);
    }
}