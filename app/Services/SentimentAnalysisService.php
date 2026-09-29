<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SentimentAnalysisService
{
    protected array $positiveWords = [
        'happy', 'good', 'great', 'excellent', 'wonderful', 'amazing', 'fantastic',
        'joy', 'love', 'like', 'enjoy', 'peaceful', 'calm', 'relaxed', 'content',
        'excited', 'hopeful', 'optimistic', 'grateful', 'thankful', 'blessed',
        'better', 'improving', 'progress', 'success', 'accomplished', 'proud',
        'motivated', 'energetic', 'active', 'social', 'connected', 'supported',
        'understood', 'valued', 'appreciated', 'confident', 'capable', 'strong',
    ];

    protected array $negativeWords = [
        'sad', 'depressed', 'unhappy', 'miserable', 'hopeless', 'worthless',
        'anxious', 'worried', 'nervous', 'scared', 'afraid', 'terrified',
        'angry', 'frustrated', 'irritated', 'annoyed', 'furious', 'rage',
        'lonely', 'isolated', 'alone', 'abandoned', 'rejected', 'unloved',
        'tired', 'exhausted', 'fatigued', 'drained', 'sleepy', 'insomnia',
        'stressed', 'overwhelmed', 'pressured', 'burdened', 'trapped',
        'confused', 'lost', 'uncertain', 'doubtful', 'insecure',
        'guilty', 'ashamed', 'embarrassed', 'humiliated', 'regretful',
        'pain', 'hurt', 'suffering', 'agony', 'torment', 'distress',
        'death', 'die', 'suicide', 'self-harm', 'cutting', 'ending',
        'addiction', 'substance', 'drugs', 'alcohol', 'drunk', 'high',
        'withdrawal', 'craving', 'relapse', 'using', 'abusing',
    ];

    protected array $intensifiers = [
        'very', 'extremely', 'really', 'so', 'too', 'highly', 'incredibly',
        'absolutely', 'completely', 'totally', 'utterly', 'deeply',
    ];

    protected array $negations = [
        'not', "n't", 'no', 'never', 'neither', 'nor', 'none', 'nobody',
        'nothing', 'nowhere', 'hardly', 'barely', 'scarcely',
    ];

    protected array $crisisKeywords = [
        'suicide', 'kill myself', 'end my life', 'want to die', 'better off dead',
        'self-harm', 'cutting', 'hurt myself', 'no reason to live', "can't go on",
        'overdose', 'jump off', 'hang myself', 'shoot myself',
    ];

    protected array $substanceKeywords = [
        'alcohol', 'drugs', 'marijuana', 'weed', 'cocaine', 'heroin', 'meth',
        'ecstasy', 'mdma', 'lsd', 'pills', 'prescription', 'overdose',
        'drunk', 'high', 'stoned', 'wasted', 'intoxicated', 'addicted',
        'craving', 'withdrawal', 'relapse', 'using', 'abusing', 'substance',
    ];

    public function analyze(string $text): array
    {
        if ($this->hasExternalApi()) {
            $result = $this->analyzeWithExternalApi($text);
            if ($result !== null) {
                return $result;
            }
        }

        return $this->analyzeLocally($text);
    }

    public function scoreFor(string $type, string $answerText, ?int $chosenIndex = null): int
    {
        $answerText = trim(strtolower($answerText));

        if ($type === 'boolean') {
            if (in_array($answerText, ['yes', 'y', 'true', '1'], true)) {
                return 4;
            }
            return 1;
        }

        if ($chosenIndex !== null) {
            return max(1, min(5, $chosenIndex + 1));
        }

        $scaleMap = [
            'not at all' => 1, 'a little' => 2, 'moderately' => 3,
            'quite a bit' => 4, 'extremely' => 5,
            'never' => 1, 'rarely' => 2, 'sometimes' => 3,
            'often' => 4, 'almost always' => 5,
        ];

        if (isset($scaleMap[$answerText])) {
            return $scaleMap[$answerText];
        }

        $sentiment = $this->analyze($answerText);
        $score = $sentiment['score'] ?? 0;

        if ($score >= 0.5) return 1;
        if ($score >= 0.1) return 2;
        if ($score > -0.1) return 3;
        if ($score > -0.5) return 4;
        return 5;
    }

    protected function analyzeLocally(string $text): array
    {
        $text = strtolower($text);
        $words = preg_split('/\s+/', $text);

        $positiveScore = 0;
        $negativeScore = 0;
        $negationActive = false;
        $intensifierMultiplier = 1.0;
        $detectedKeywords = [];
        $crisisDetected = false;
        $substanceDetected = false;

        foreach ($words as $word) {
            $word = preg_replace('/[^\w\']/', '', $word);
            if (empty($word)) continue;

            if (in_array($word, $this->negations, true)) {
                $negationActive = true;
                continue;
            }

            if (in_array($word, $this->intensifiers, true)) {
                $intensifierMultiplier = 1.5;
                continue;
            }

            foreach ($this->crisisKeywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $crisisDetected = true;
                    $detectedKeywords[] = $keyword;
                }
            }

            if (in_array($word, $this->substanceKeywords, true)) {
                $substanceDetected = true;
                $detectedKeywords[] = $word;
            }

            if (in_array($word, $this->positiveWords, true)) {
                $score = $negationActive ? -1 : 1;
                $positiveScore += $score * $intensifierMultiplier;
                $detectedKeywords[] = $word;
            }

            if (in_array($word, $this->negativeWords, true)) {
                $score = $negationActive ? 1 : -1;
                $negativeScore += abs($score) * $intensifierMultiplier;
                $detectedKeywords[] = $word;
            }

            $negationActive = false;
            $intensifierMultiplier = 1.0;
        }

        $totalWords = max(count($words), 1);
        $rawScore = ($positiveScore - $negativeScore) / $totalWords;
        $normalizedScore = max(-1, min(1, $rawScore * 2));

        $label = $this->getSentimentLabel($normalizedScore);

        $crisisLevel = 'none';
        if ($crisisDetected) $crisisLevel = 'critical';
        elseif ($substanceDetected) $crisisLevel = 'high';
        elseif ($normalizedScore < -0.5) $crisisLevel = 'moderate';

        return [
            'score' => round($normalizedScore, 4),
            'label' => $label,
            'confidence' => min(1, abs($normalizedScore) + 0.3),
            'keywords' => array_unique($detectedKeywords),
            'crisis_level' => $crisisLevel,
            'crisis_detected' => $crisisDetected,
            'substance_detected' => $substanceDetected,
            'positive_score' => $positiveScore,
            'negative_score' => $negativeScore,
            'method' => 'local_lexicon',
        ];
    }

    protected function analyzeWithExternalApi(string $text): ?array
    {
        try {
            $apiKey = config('sentiment.openai_api_key');
            $apiUrl = config('sentiment.openai_api_url', 'https://api.openai.com/v1/chat/completions');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a mental health sentiment analyzer. Analyze the following text and return a JSON object with: score (float -1 to 1), label (positive/neutral/negative), crisis_detected (boolean), substance_detected (boolean), keywords (array), confidence (0-1). Only return valid JSON.'],
                    ['role' => 'user', 'content' => $text],
                ],
                'temperature' => 0.3,
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $result = json_decode($content, true);

                if ($result && isset($result['score'])) {
                    $result['method'] = 'openai';
                    $result['crisis_level'] = $result['crisis_detected'] ? 'critical' : 'none';
                    return $result;
                }
            }
        } catch (\Exception $e) {
            Log::warning('External sentiment API failed: ' . $e->getMessage());
        }

        return null;
    }

    protected function getSentimentLabel(float $score): string
    {
        if ($score >= 0.3) return 'positive';
        if ($score >= 0.05) return 'slightly_positive';
        if ($score > -0.05) return 'neutral';
        if ($score > -0.3) return 'slightly_negative';
        return 'negative';
    }

    protected function hasExternalApi(): bool
    {
        return !empty(config('sentiment.openai_api_key'));
    }

    public function analyzeBatch(array $responses): array
    {
        $scores = [];
        $allKeywords = [];
        $crisisDetected = false;
        $substanceDetected = false;

        foreach ($responses as $response) {
            $result = $this->analyze($response);
            $scores[] = $result['score'];
            $allKeywords = array_merge($allKeywords, $result['keywords']);

            if ($result['crisis_detected']) $crisisDetected = true;
            if ($result['substance_detected']) $substanceDetected = true;
        }

        $avgScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;

        return [
            'score' => round($avgScore, 4),
            'label' => $this->getSentimentLabel($avgScore),
            'keywords' => array_unique($allKeywords),
            'crisis_detected' => $crisisDetected,
            'substance_detected' => $substanceDetected,
            'individual_scores' => $scores,
        ];
    }
}