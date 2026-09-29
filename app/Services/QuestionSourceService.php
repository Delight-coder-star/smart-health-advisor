<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuestionSourceService
{
    /**
     * Try to get a fresh question for the category from multiple sources.
     * Returns null if all sources fail — caller falls back to the static bank.
     */
    public function fetchFresh(string $category, array $excludeTexts, ?string $previousAnswer = null): ?array
    {
        // 1. Try primary AI
        $ai = $this->fromAI($category, $excludeTexts, $previousAnswer);
        if ($ai) {
            return $ai;
        }

        // 2. Try secondary AI provider if configured
        $ai2 = $this->fromSecondaryAI($category, $excludeTexts);
        if ($ai2) {
            return $ai2;
        }

        return null;
    }

    protected function fromAI(string $category, array $exclude, ?string $previous): ?array
    {
        if (!config('sentiment.openai_api_key')) {
            return null;
        }

        $meta = CategoryQuestionBank::CATEGORIES[$category] ?? null;
        if (!$meta) {
            return null;
        }

        try {
            $excludeStr = empty($exclude)
                ? '(none)'
                : "- " . implode("\n- ", array_slice($exclude, -15));

            $prompt = "You are a careful clinical screening assistant for Zimbabwe Polytechnic students.\n"
                . "Category: {$meta['label']}\n"
                . "Description: {$meta['description']}\n\n"
                . "Generate ONE original screening question (10-25 words).\n"
                . "Return JSON: {question, type: scale|frequency|boolean|text, weight: 1-5, sensitive: bool}.\n\n"
                . "Rules:\n"
                . "- Never repeat any of these already-asked questions:\n{$excludeStr}\n"
                . "- Use warm, non-judgemental wording.\n"
                . "- Cover something not already asked.\n"
                . ($previous ? "- The student's last answer was: \"{$previous}\"\n" : '');

            $res = Http::withToken(config('sentiment.openai_api_key'))
                ->timeout(15)
                ->post(config('sentiment.openai_api_url'), [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Output only valid JSON.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 1.0,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (!$res->successful()) {
                return null;
            }

            $data = json_decode($res->json('choices.0.message.content'), true);
            if (!is_array($data) || empty($data['question'])) {
                return null;
            }

            return [
                'category' => $category,
                'text' => $data['question'],
                'type' => $data['type'] ?? 'scale',
                'weight' => (int) ($data['weight'] ?? 2),
                'sensitive' => (bool) ($data['sensitive'] ?? false),
                'source' => 'openai',
                'generation_metadata' => ['model' => 'gpt-4o-mini'],
            ];
        } catch (\Throwable $e) {
            Log::warning('Primary AI question failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function fromSecondaryAI(string $category, array $exclude): ?array
    {
        $url = env('SECONDARY_AI_URL');
        $key = env('SECONDARY_AI_KEY');

        if (!$url || !$key) {
            return null;
        }

        try {
            $meta = CategoryQuestionBank::CATEGORIES[$category] ?? null;
            if (!$meta) {
                return null;
            }

            $res = Http::withToken($key)->timeout(15)->post($url, [
                'prompt' => "Generate one screening question for: {$meta['label']}. "
                    . "Return JSON {question, type, weight, sensitive}.",
            ]);

            if (!$res->successful()) {
                return null;
            }

            $data = $res->json();
            if (!is_array($data) || empty($data['question'])) {
                return null;
            }

            return [
                'category' => $category,
                'text' => $data['question'],
                'type' => $data['type'] ?? 'scale',
                'weight' => (int) ($data['weight'] ?? 2),
                'sensitive' => (bool) ($data['sensitive'] ?? false),
                'source' => 'secondary_ai',
            ];
        } catch (\Throwable $e) {
            Log::warning('Secondary AI failed: ' . $e->getMessage());
            return null;
        }
    }
}