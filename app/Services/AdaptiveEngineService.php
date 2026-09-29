<?php

namespace App\Services;

use App\Models\AssessmentSession;

class AdaptiveEngineService
{
    public function prioritise(AssessmentSession $session): array
    {
        $answers = $session->answers()->with('question')->get();
        $domainScores = $this->domainScores($answers);

        if ($session->crisis_flag_raised) {
            return array_merge(
                ['mental_health', 'substance_abuse'],
                array_keys($domainScores)
            );
        }

        arsort($domainScores);
        return array_keys($domainScores);
    }

    public function domainScores($answers): array
    {
        $scores = [];
        foreach ($answers as $a) {
            $domain = $a->question->domain ?? 'general_wellness';
            $raw = $a->numeric_score ?? 3;

            if ($a->sentiment_score < -0.3) $raw += 1;
            if ($a->sentiment_score < -0.6) $raw += 1;

            $scores[$domain] = ($scores[$domain] ?? 0) + $raw;
        }

        foreach ($scores as $d => $sum) {
            $count = $answers->filter(fn ($a) => ($a->question->domain ?? '') === $d)->count() ?: 1;
            $scores[$d] = round($sum / $count, 2);
        }

        foreach (array_keys(CategoryQuestionBank::CATEGORIES) as $d) {
            $scores[$d] = $scores[$d] ?? 0;
        }

        return $scores;
    }

    public function crisisEscalation(array $scores, bool $crisisFlag): bool
    {
        if ($crisisFlag) return true;
        return ($scores['mental_health'] ?? 0) >= 4.0;
    }
}