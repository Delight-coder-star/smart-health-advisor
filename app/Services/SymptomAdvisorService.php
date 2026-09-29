<?php

namespace App\Services;

use App\Models\SymptomQuery;
use App\Models\AiAdviceResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SymptomAdvisorService
{
    public function __construct(
        protected MedicalKnowledgeService $knowledge
    ) {
    }

    public function advise(int $userId, string $question): AiAdviceResponse
    {
        $research = $this->knowledge->research($question);
        $urgency = $this->detectUrgency($question);

        $query = SymptomQuery::create([
            'user_id' => $userId,
            'question' => $question,
            'category' => $research['conditions'][0]['category'] ?? 'general',
            'keywords_detected' => $research['keywords'],
            'urgency' => $urgency,
            'status' => 'answered',
        ]);

        $advice = $this->hasAI() ? $this->callAI($question, $research, $urgency) : null;

        if (!$advice) {
            $advice = $this->localAdvice($question, $research, $urgency);
        }

        return AiAdviceResponse::create([
            'query_id' => $query->id,
            'user_id' => $userId,
            'response_text' => $advice['summary'],
            'possible_conditions' => $advice['conditions'],
            'recommended_actions' => $advice['actions'],
            'when_to_see_doctor' => $advice['see_doctor'],
            'prevention_tips' => $advice['prevention'],
            'red_flags' => $advice['red_flags'],
            'confidence' => $advice['confidence'],
            'source' => $advice['source'],
            'related_videos' => $research['videos'],
            'knowledge_refs' => array_column($research['conditions'], 'name'),
            'disclaimer_shown' => true,
        ]);
    }

    protected function detectUrgency(string $text): string
    {
        $t = strtolower($text);

        $critical = [
            'chest pain', "can't breathe", 'cannot breathe', 'severe bleeding',
            'unconscious', 'suicide', 'overdose', 'stroke', 'heart attack',
        ];
        $high = [
            'high fever', 'vomiting blood', 'blood in stool', 'severe pain',
            'pregnant and bleeding',
        ];

        foreach ($critical as $w) {
            if (str_contains($t, $w)) return 'critical';
        }
        foreach ($high as $w) {
            if (str_contains($t, $w)) return 'high';
        }
        if (str_contains($t, 'pain') || str_contains($t, 'fever')) return 'medium';

        return 'low';
    }

    protected function callAI(string $question, array $research, string $urgency): ?array
    {
        try {
            $kb = collect($research['conditions'])
                ->map(fn ($c) => "- {$c['name']} ({$c['category']}): {$c['summary']}")
                ->implode("\n");

            $prompt = "You are a careful, friendly medical advisor for a Zimbabwe college health system.\n\n"
                . "Student question: \"{$question}\"\n\n"
                . "Known knowledge base matches:\n{$kb}\n\n"
                . "Return JSON with keys: summary, conditions (array), actions (array), "
                . "see_doctor (array), prevention (array), red_flags (array), confidence (0-1).\n"
                . "Never diagnose with certainty. Output only JSON.";

            $res = Http::withToken(config('sentiment.openai_api_key'))
                ->timeout(25)
                ->post(config('sentiment.openai_api_url'), [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Output valid JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($res->successful()) {
                $data = json_decode($res->json('choices.0.message.content'), true);
                if (is_array($data)) {
                    $data['source'] = 'openai';
                    $data['confidence'] = $data['confidence'] ?? 0.75;
                    return $data;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AI advice failed: ' . $e->getMessage());
        }

        return null;
    }

    protected function localAdvice(string $question, array $research, string $urgency): array
    {
        $conditions = $research['conditions'];
        $first = $conditions[0] ?? null;

        $summary = $first
            ? "Based on what you described, this may relate to {$first['name']}. This is not a diagnosis — please consult a health professional."
            : "Thank you for sharing. Your symptoms need professional review. Please continue reading the guidance below.";

        $actions = [
            'Rest and stay hydrated',
            'Monitor your symptoms over the next 24 hours',
            'Avoid self-medicating without professional advice',
        ];

        $seeDoctor = [
            'If symptoms last more than 3 days',
            'If symptoms get significantly worse',
            'If you develop a high fever (above 38.5°C)',
        ];

        $prevention = $first['prevention'] ?? [
            'Maintain good hygiene',
            'Eat a balanced diet',
            'Get enough sleep',
        ];

        $redFlags = [
            'Difficulty breathing',
            'Chest pain',
            'Severe bleeding',
            'Loss of consciousness',
            'Suicidal thoughts',
        ];

        if ($urgency === 'critical') {
            array_unshift($seeDoctor, 'SEEK EMERGENCY CARE IMMEDIATELY');
            $summary = "Your description suggests a potentially serious situation. "
                . "Please seek emergency care now.\n\n" . $summary;
        }

        return [
            'summary' => $summary,
            'conditions' => array_map(fn ($c) => $c['name'], $conditions),
            'actions' => $actions,
            'see_doctor' => $seeDoctor,
            'prevention' => $prevention,
            'red_flags' => $redFlags,
            'confidence' => $first ? 0.6 : 0.4,
            'source' => 'local',
        ];
    }

    protected function hasAI(): bool
    {
        return !empty(config('sentiment.openai_api_key'));
    }
}