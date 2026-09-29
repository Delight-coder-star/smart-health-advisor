<?php

namespace App\Services;

use App\Models\AssessmentSession;
use App\Models\SessionQuestion;
use App\Models\SessionAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AssessmentFlowService
{
    public function __construct(
        protected SentimentAnalysisService $sentiment,
        protected DiagnosticsService $diagnostics,
        protected RiskAssessmentService $risk,
        protected AIQuestionService $aiQuestions,
    ) {
    }

    public function startSession(User $user, string $mode, array $categories, int $max = 20): AssessmentSession
    {
        $categories = array_values(array_filter(
            $categories,
            fn ($c) => array_key_exists($c, CategoryQuestionBank::CATEGORIES)
        ));

        if (empty($categories)) {
            $categories = ['mental_health', 'general_wellness'];
        }

        return AssessmentSession::create([
            'user_id' => $user->id,
            'mode' => $mode,
            'max_questions' => $max,
            'selected_categories' => $categories,
            'status' => 'active',
            'domains_covered' => [],
        ]);
    }

    /**
     * Get the next question. Respects the selected max_questions.
     * If the category banks are exhausted, recycles questions after a full pass.
     */
    public function nextQuestion(AssessmentSession $session): array
    {
        // Stop only when we've asked the number of questions the user chose
        if ($session->questions_asked >= $session->max_questions) {
            return ['done' => true];
        }

        $categories = $session->selected_categories ?? ['mental_health'];
        $askedThisSession = $session->questions()->pluck('question_text')->all();

        // Count total questions available across the selected categories
        $totalAvailable = CategoryQuestionBank::totalFor($categories);

        // If we've asked more than the pool size, allow recycling
        $allowRecycle = count($askedThisSession) >= $totalAvailable;

        // Prefer categories with the least progress so we cover all evenly
        $covered = $session->domains_covered ?? [];
        $remaining = array_values(array_diff($categories, $covered));
        if (empty($remaining)) {
            $remaining = $categories;
        }

        // Crisis escalation — probe mental health first
        if ($session->crisis_flag_raised && !in_array('mental_health', $remaining, true)) {
            array_unshift($remaining, 'mental_health');
        }

        $targetCategory = $remaining[0] ?? $categories[array_rand($categories)];
        $previousAnswer = $session->answers()->latest()->value('raw_answer');

        // 1. Try AI for a fresh question
        $generated = null;
        try {
            $generated = $this->aiQuestions->nextQuestionForCategory(
                $targetCategory,
                $askedThisSession,
                $previousAnswer
            );
        } catch (\Throwable $e) {
            Log::warning('AI question failed: ' . $e->getMessage());
        }

        // 2. Try bank — cycle through target category first
        if (!$generated) {
            $generated = CategoryQuestionBank::pickRandomWeighted([$targetCategory], $askedThisSession);
        }

        // 3. Try other selected categories
        if (!$generated) {
            $shuffled = $categories;
            shuffle($shuffled);
            foreach ($shuffled as $cat) {
                $generated = CategoryQuestionBank::pickRandomWeighted([$cat], $askedThisSession);
                if ($generated) break;
            }
        }

        // 4. Allow recycling — pick any category that already had questions
        if (!$generated && $allowRecycle) {
            $shuffled = $categories;
            shuffle($shuffled);
            foreach ($shuffled as $cat) {
                $generated = CategoryQuestionBank::pickFrom($cat, []);
                if ($generated) break;
            }
        }

        // 5. Last resort — any category in the whole system
        if (!$generated) {
            $allKeys = array_keys(CategoryQuestionBank::CATEGORIES);
            shuffle($allKeys);
            foreach ($allKeys as $cat) {
                $generated = CategoryQuestionBank::pickRandomWeighted([$cat], $askedThisSession);
                if ($generated) break;
            }
        }

        if (!$generated) {
            // Truly nothing — end early
            return ['done' => true];
        }

        $seq = $session->nextSequence();
        $question = SessionQuestion::create([
            'session_id' => $session->id,
            'sequence' => $seq,
            'domain' => $generated['category'] ?? $targetCategory,
            'subdomain' => null,
            'question_text' => $generated['text'],
            'question_type' => $generated['type'] ?? 'scale',
            'options' => $this->optionsForType($generated['type'] ?? 'scale'),
            'weight' => $generated['weight'] ?? 2,
            'is_crisis_sensitive' => $generated['sensitive'] ?? false,
            'source' => $generated['source'] ?? 'template',
            'generation_metadata' => $generated['generation_metadata'] ?? null,
        ]);

        $session->increment('questions_asked');

        $newCovered = array_values(array_unique(array_merge($covered, [$question->domain])));
        $session->update(['domains_covered' => $newCovered]);

        return [
            'done' => false,
            'question' => [
                'id' => $question->id,
                'sequence' => $seq,
                'domain' => $question->domain,
                'domain_label' => CategoryQuestionBank::CATEGORIES[$question->domain]['label'] ?? $question->domain,
                'text' => $question->question_text,
                'type' => $question->question_type,
                'options' => $question->options,
                'sensitive' => $question->is_crisis_sensitive,
                'source' => $question->source,
            ],
            'progress' => [
                'asked' => $session->questions_asked,
                'max' => $session->max_questions,
                'risk_score' => $session->running_risk_score,
                'risk_level' => $session->current_risk_level,
            ],
        ];
    }

    public function submitAnswer(AssessmentSession $session, SessionQuestion $question, array $payload): SessionAnswer
    {
        $raw = $payload['answer'] ?? '';
        $chosenIndex = $payload['choice_index'] ?? null;
        $latency = $payload['latency_ms'] ?? null;

        $numeric = $this->sentiment->scoreFor($question->question_type, $raw, $chosenIndex);
        $analysis = $this->sentiment->analyze($raw ?: '');
        $sentiment = $analysis['score'] ?? 0;

        $crisis = $analysis['crisis_detected'] ?? false;
        $substance = $analysis['substance_detected'] ?? false;
        $selfHarm = $this->isSelfHarm($raw);

        $answer = SessionAnswer::create([
            'session_id' => $session->id,
            'question_id' => $question->id,
            'user_id' => $session->user_id,
            'raw_answer' => $raw,
            'numeric_score' => $numeric,
            'sentiment_score' => $sentiment,
            'sentiment_label' => $analysis['label'] ?? null,
            'confidence' => $analysis['confidence'] ?? 0,
            'keywords_detected' => $analysis['keywords'] ?? [],
            'analysis_snapshot' => $analysis,
            'crisis_detected' => $crisis,
            'substance_flagged' => $substance,
            'self_harm_flagged' => $selfHarm,
            'response_latency_ms' => $latency,
            'answered_at' => now(),
        ]);

        $session->recordAnswer($answer);

        if ($crisis || $session->current_risk_level === 'critical') {
            $this->risk->raiseCrisisAlert($session->user, $answer);
        }

        return $answer;
    }

    public function finishSession(AssessmentSession $session): \App\Models\AssessmentReport
    {
        $session->update(['status' => 'completed', 'completed_at' => now()]);

        $mergedAdvice = $this->mergeAdvice($session);
        $session->update(['merged_advice' => $mergedAdvice]);

        return $this->diagnostics->generateReport($session);
    }

    protected function mergeAdvice(AssessmentSession $session): array
    {
        $answers = $session->answers()->with('question')->get();
        $categories = $session->selected_categories ?? [];

        $byCategory = [];
        foreach ($answers as $a) {
            $cat = $a->question->domain ?? 'general_wellness';
            $byCategory[$cat]['scores'][] = $a->numeric_score ?? 3;
            $byCategory[$cat]['answers'][] = $a->raw_answer;
            $byCategory[$cat]['sentiments'][] = $a->sentiment_score;
        }

        $merged = [];
        foreach ($categories as $cat) {
            $data = $byCategory[$cat] ?? null;
            if (!$data) continue;

            $avgScore = round(array_sum($data['scores']) / count($data['scores']), 2);
            $avgSentiment = round(array_sum($data['sentiments']) / count($data['sentiments']), 2);

            $level = 'low';
            if ($avgScore >= 4.5) $level = 'critical';
            elseif ($avgScore >= 3.5) $level = 'high';
            elseif ($avgScore >= 2.5) $level = 'moderate';

            $meta = CategoryQuestionBank::CATEGORIES[$cat] ?? null;
            $guidance = $this->guidanceForCategory($cat, $level, $data['answers']);

            $merged[] = [
                'category' => $cat,
                'label' => $meta['label'] ?? ucfirst($cat),
                'average_score' => $avgScore,
                'average_sentiment' => $avgSentiment,
                'level' => $level,
                'summary' => $guidance['summary'],
                'actions' => $guidance['actions'],
                'see_doctor' => $guidance['see_doctor'],
                'prevention' => $guidance['prevention'],
                'red_flags' => $guidance['red_flags'],
                'resource_links' => $guidance['resource_links'],
            ];
        }

        return $merged;
    }

    protected function guidanceForCategory(string $category, string $level, array $answers): array
    {
        $knowledge = $this->categoryKnowledge($category);

        $levelText = match ($level) {
            'critical' => 'a critical level of concern',
            'high' => 'a high level of concern',
            'moderate' => 'a moderate level of concern',
            default => 'a low level of concern',
        };

        $summary = "Your responses in this category indicate {$levelText}. " . $knowledge['summary'];

        $actions = $knowledge['actions'];
        $seeDoctor = $knowledge['see_doctor'];
        $prevention = $knowledge['prevention'];
        $redFlags = $knowledge['red_flags'];

        if ($level === 'critical') {
            array_unshift($seeDoctor, '⚠ SEEK PROFESSIONAL HELP IMMEDIATELY');
        } elseif ($level === 'high') {
            array_unshift($seeDoctor, 'Schedule a professional consultation within 48 hours');
        }

        return [
            'summary' => $summary,
            'actions' => $actions,
            'see_doctor' => $seeDoctor,
            'prevention' => $prevention,
            'red_flags' => $redFlags,
            'resource_links' => $knowledge['resources'],
        ];
    }

    protected function categoryKnowledge(string $category): array
    {
        return match ($category) {
            'mental_health' => [
                'summary' => 'Mental health is as important as physical health. Early support changes outcomes.',
                'actions' => ['Practice daily breathing exercises', 'Maintain a sleep schedule', 'Journal your feelings'],
                'see_doctor' => ['If you feel persistently sad or hopeless', 'If you have thoughts of self-harm'],
                'prevention' => ['Stay socially connected', 'Limit alcohol and caffeine', 'Exercise 30 min daily'],
                'red_flags' => ['Thoughts of suicide', 'Unable to function daily', 'Panic attacks'],
                'resources' => ['Campus Counselling: +263 717 923 021', 'Friendship Bench Zimbabwe'],
            ],
            'substance_abuse' => [
                'summary' => 'Substance use disorder is a medical condition — help is effective and available.',
                'actions' => ['Identify triggers', 'Remove substances from your environment', 'Tell a trusted person'],
                'see_doctor' => ['If you experience withdrawal symptoms', 'If using affects daily life', 'If you want to quit'],
                'prevention' => ['Avoid high-risk social settings', 'Build sober support networks', 'Find healthy outlets'],
                'red_flags' => ['Overdose', 'Seizures', 'Suicidal thoughts'],
                'resources' => ['Zimbabwe Civil Liberties: Drug Helpline', 'AA/NA meetings'],
            ],
            'pregnancy' => [
                'summary' => 'Regular prenatal care protects both you and your baby.',
                'actions' => ['Take folic acid daily', 'Attend all prenatal visits', 'Stay hydrated'],
                'see_doctor' => ['Any bleeding or severe pain', 'Reduced fetal movement', 'Severe headaches'],
                'prevention' => ['Avoid alcohol and smoking', 'Manage stress', 'Eat a balanced diet'],
                'red_flags' => ['Heavy bleeding', 'Severe abdominal pain', 'Fainting'],
                'resources' => ['Nearest clinic', 'Zimbabwe Family Planning Council'],
            ],
            'chronic_illness' => [
                'summary' => 'Consistent management of chronic conditions prevents complications.',
                'actions' => ['Take medications as prescribed', 'Monitor your vitals', 'Reduce salt and sugar'],
                'see_doctor' => ['BP above 180/120', 'Blood sugar above 300 mg/dL', 'Chest pain'],
                'prevention' => ['Regular exercise', 'Balanced diet', 'Routine check-ups'],
                'red_flags' => ['Chest pain', 'Loss of consciousness', 'Severe shortness of breath'],
                'resources' => ['Nearest clinic for BP/sugar checks', 'Diabetes Zimbabwe'],
            ],
            'infectious_disease' => [
                'summary' => 'Early testing and treatment of infectious diseases saves lives.',
                'actions' => ['Get tested', 'Complete full course of medication', 'Inform close contacts'],
                'see_doctor' => ['Persistent fever', 'Cough over 2 weeks', 'Unexplained weight loss'],
                'prevention' => ['Wash hands frequently', 'Use treated mosquito nets', 'Vaccinate'],
                'red_flags' => ['Difficulty breathing', 'Severe bleeding', 'Confusion'],
                'resources' => ['Nearest clinic', 'New Start Centre (HIV testing)'],
            ],
            'cancer' => [
                'summary' => 'Early detection dramatically improves cancer outcomes.',
                'actions' => ['Get regular screenings', 'Report any unusual symptoms', 'Maintain healthy lifestyle'],
                'see_doctor' => ['Any lump or unusual bleeding', 'Symptoms over 3 weeks', 'Family history concerns'],
                'prevention' => ['No smoking', 'Limit alcohol', 'Sun protection'],
                'red_flags' => ['Unexplained bleeding', 'Rapid weight loss', 'New lumps'],
                'resources' => ['Cancer Association of Zimbabwe', 'Nearest screening centre'],
            ],
            default => [
                'summary' => 'General wellness is built through consistent healthy habits.',
                'actions' => ['Exercise regularly', 'Eat balanced meals', 'Get enough sleep'],
                'see_doctor' => ['Persistent symptoms', 'Symptoms over 3 weeks'],
                'prevention' => ['Stay hydrated', 'Manage stress', 'Regular check-ups'],
                'red_flags' => ['Severe pain', 'High fever', 'Difficulty breathing'],
                'resources' => ['Nearest clinic', 'Campus health services'],
            ],
        };
    }

    protected function optionsForType(string $type): array
    {
        return match ($type) {
            'scale' => ['Not at all', 'A little', 'Moderately', 'Quite a bit', 'Extremely'],
            'frequency' => ['Never', 'Rarely', 'Sometimes', 'Often', 'Almost always'],
            'boolean' => ['No', 'Yes'],
            default => [],
        };
    }

    protected function isSelfHarm(string $text): bool
    {
        $needles = ['cut myself', 'hurt myself', 'self-harm', 'self harm', 'suicide', 'kill myself', 'end my life'];
        $t = strtolower($text);
        foreach ($needles as $n) {
            if (str_contains($t, $n)) return true;
        }
        return false;
    }
}