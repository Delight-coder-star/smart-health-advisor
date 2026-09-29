<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\BehavioralResponse;
use App\Services\SentimentAnalysisService;
use App\Services\RiskAssessmentService;
use App\Services\RecommendationService;
use App\Services\VideoAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    protected SentimentAnalysisService $sentimentService;
    protected RiskAssessmentService $riskService;
    protected RecommendationService $recommendationService;
    protected VideoAnalysisService $videoService;

    public function __construct(
        SentimentAnalysisService $sentimentService,
        RiskAssessmentService $riskService,
        RecommendationService $recommendationService,
        VideoAnalysisService $videoService
    ) {
        $this->sentimentService = $sentimentService;
        $this->riskService = $riskService;
        $this->recommendationService = $recommendationService;
        $this->videoService = $videoService;
    }

    protected array $questions = [
        [
            'key' => 'substance_use',
            'text' => 'Do you ever use substances like alcohol or drugs?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Never'],
                ['value' => 2, 'label' => 'Rarely (once a month or less)'],
                ['value' => 3, 'label' => 'Sometimes (2-4 times a month)'],
                ['value' => 4, 'label' => 'Often (2-3 times a week)'],
                ['value' => 5, 'label' => 'Daily'],
            ],
            'category' => 'substance',
        ],
        [
            'key' => 'substance_frequency',
            'text' => 'What situations usually lead you to use substances?',
            'type' => 'textarea',
            'category' => 'substance',
        ],
        [
            'key' => 'mood_changes',
            'text' => 'Have you noticed any changes in your mood recently?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'No changes'],
                ['value' => 2, 'label' => 'Slight changes'],
                ['value' => 3, 'label' => 'Moderate changes'],
                ['value' => 4, 'label' => 'Significant changes'],
                ['value' => 5, 'label' => 'Severe mood swings'],
            ],
            'category' => 'mental_health',
        ],
        [
            'key' => 'sleep_issues',
            'text' => 'How would you describe your sleep patterns?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Sleeping well (7-9 hours)'],
                ['value' => 2, 'label' => 'Occasional sleep issues'],
                ['value' => 3, 'label' => 'Frequent sleep problems'],
                ['value' => 4, 'label' => 'Difficulty sleeping most nights'],
                ['value' => 5, 'label' => 'Severe insomnia or sleeping too much'],
            ],
            'category' => 'physical',
        ],
        [
            'key' => 'appetite_changes',
            'text' => 'Have you experienced changes in appetite or eating habits?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'No changes'],
                ['value' => 2, 'label' => 'Slight changes'],
                ['value' => 3, 'label' => 'Moderate changes'],
                ['value' => 4, 'label' => 'Significant changes'],
                ['value' => 5, 'label' => 'Severe changes'],
            ],
            'category' => 'physical',
        ],
        [
            'key' => 'energy_levels',
            'text' => 'How would you rate your energy levels?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Normal energy'],
                ['value' => 2, 'label' => 'Slightly tired'],
                ['value' => 3, 'label' => 'Often fatigued'],
                ['value' => 4, 'label' => 'Very tired most days'],
                ['value' => 5, 'label' => 'Exhausted all the time'],
            ],
            'category' => 'physical',
        ],
        [
            'key' => 'concentration',
            'text' => 'How is your ability to concentrate or make decisions?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'No problems'],
                ['value' => 2, 'label' => 'Minor difficulties'],
                ['value' => 3, 'label' => 'Moderate difficulties'],
                ['value' => 4, 'label' => 'Significant difficulties'],
                ['value' => 5, 'label' => 'Severe difficulties'],
            ],
            'category' => 'mental_health',
        ],
        [
            'key' => 'social_withdrawal',
            'text' => 'Have you been withdrawing from social activities?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Not at all'],
                ['value' => 2, 'label' => 'Slightly'],
                ['value' => 3, 'label' => 'Moderately'],
                ['value' => 4, 'label' => 'Significantly'],
                ['value' => 5, 'label' => 'Completely withdrawn'],
            ],
            'category' => 'social',
        ],
        [
            'key' => 'anxiety_levels',
            'text' => 'How often do you feel anxious or excessively worried?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Never'],
                ['value' => 2, 'label' => 'Rarely'],
                ['value' => 3, 'label' => 'Sometimes'],
                ['value' => 4, 'label' => 'Often'],
                ['value' => 5, 'label' => 'Almost always'],
            ],
            'category' => 'mental_health',
        ],
        [
            'key' => 'depression_signs',
            'text' => 'How often do you feel persistently sad or hopeless?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Never'],
                ['value' => 2, 'label' => 'Rarely'],
                ['value' => 3, 'label' => 'Sometimes'],
                ['value' => 4, 'label' => 'Often'],
                ['value' => 5, 'label' => 'Almost always'],
            ],
            'category' => 'mental_health',
        ],
        [
            'key' => 'hallucinations',
            'text' => 'Have you experienced hallucinations or delusions?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Never'],
                ['value' => 2, 'label' => 'Rarely'],
                ['value' => 3, 'label' => 'Sometimes'],
                ['value' => 4, 'label' => 'Often'],
                ['value' => 5, 'label' => 'Frequently'],
            ],
            'category' => 'mental_health',
        ],
        [
            'key' => 'self_harm',
            'text' => 'Have you had thoughts of self-harm?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Never'],
                ['value' => 2, 'label' => 'Rarely'],
                ['value' => 3, 'label' => 'Sometimes'],
                ['value' => 4, 'label' => 'Often'],
                ['value' => 5, 'label' => 'Frequently'],
            ],
            'category' => 'crisis',
        ],
        [
            'key' => 'suicidal_thoughts',
            'text' => 'Have you had thoughts of ending your life?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Never'],
                ['value' => 2, 'label' => 'Rarely'],
                ['value' => 3, 'label' => 'Sometimes'],
                ['value' => 4, 'label' => 'Often'],
                ['value' => 5, 'label' => 'Frequently'],
            ],
            'category' => 'crisis',
        ],
        [
            'key' => 'support_system',
            'text' => 'How would you rate your support system (friends, family)?',
            'type' => 'select',
            'options' => [
                ['value' => 1, 'label' => 'Very strong'],
                ['value' => 2, 'label' => 'Strong'],
                ['value' => 3, 'label' => 'Moderate'],
                ['value' => 4, 'label' => 'Weak'],
                ['value' => 5, 'label' => 'No support'],
            ],
            'category' => 'social',
        ],
        [
            'key' => 'coping_mechanisms',
            'text' => 'How do you usually cope with stress?',
            'type' => 'textarea',
            'category' => 'mental_health',
        ],
    ];

    public function index()
    {
        $user = Auth::user();
        $latestAssessment = $user->latestAssessment;

        return view('assessment.index', compact('user', 'latestAssessment'));
    }

    public function questions()
    {
        $questions = $this->questions;

        return view('assessment.questions', compact('questions'));
    }

    public function video()
    {
        return view('assessment.video');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'responses' => ['required', 'array'],
            'responses.*.key' => ['required', 'string'],
            'responses.*.text' => ['nullable', 'string'],
            'responses.*.score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
        ]);

        $assessment = Assessment::create([
            'user_id' => $user->id,
            'type' => 'initial',
            'status' => 'in_progress',
        ]);

        $responseTexts = [];
        $responseScores = [];

        foreach ($validated['responses'] as $response) {
            $key = $response['key'];
            $question = collect($this->questions)->firstWhere('key', $key);

            if (!$question) {
                continue;
            }

            $text = $response['text'] ?? '';
            $score = $response['score'] ?? null;

            $sentiment = null;
            if (!empty($text)) {
                $sentiment = $this->sentimentService->analyze($text);
                $responseTexts[] = $text;
            }

            if ($score === null && $sentiment !== null) {
                $score = $this->mapSentimentToScore($sentiment['score']);
            }

            if ($score !== null) {
                $responseScores[$key] = $score;
            }

            BehavioralResponse::create([
                'assessment_id' => $assessment->id,
                'user_id' => $user->id,
                'question_key' => $key,
                'question_text' => $question['text'],
                'response_text' => $text,
                'response_score' => $score ?? 0,
                'sentiment_score' => $sentiment['score'] ?? 0,
                'keywords_detected' => $sentiment['keywords'] ?? [],
            ]);
        }

        $overallSentiment = !empty($responseTexts)
            ? $this->sentimentService->analyzeBatch($responseTexts)
            : ['score' => 0, 'label' => 'neutral', 'crisis_detected' => false];

        $riskData = $this->riskService->calculateRiskScore(
            array_map(
                fn ($key, $score) => ['key' => $key, 'score' => $score],
                array_keys($responseScores),
                $responseScores
            )
        );

        $assessment->update([
            'total_score' => $riskData['total_score'],
            'sentiment_score' => $overallSentiment['score'],
            'sentiment_label' => $overallSentiment['label'],
            'risk_level' => $riskData['risk_level'],
            'indicators' => $riskData['indicators'],
            'ai_analysis' => [
                'sentiment' => $overallSentiment,
                'risk' => $riskData,
            ],
            'requires_attention' => $riskData['requires_attention'],
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'videos');
            $this->videoService->processVideo($videoPath, $assessment->id, $user->id);
        }

        $this->recommendationService->generateRecommendations($assessment);
        $this->riskService->processAssessment($assessment);

        return redirect()->route('assessment.results', $assessment)
            ->with('success', 'Assessment completed successfully.');
    }

    public function results(Assessment $assessment)
    {
        if ($assessment->user_id !== Auth::id()
            && !Auth::user()->isAdvisor()
            && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $assessment->load(['behavioralResponses', 'videoAnalysis', 'recommendations']);

        $recommendations = $assessment->recommendations;
        $healthTips = $this->recommendationService->getHealthTips(
            $this->getCategoryFromRisk($assessment->risk_level)
        );

        return view('assessment.results', compact('assessment', 'recommendations', 'healthTips'));
    }

    protected function mapSentimentToScore(float $sentimentScore): int
    {
        if ($sentimentScore >= 0.5) {
            return 1;
        }
        if ($sentimentScore >= 0.1) {
            return 2;
        }
        if ($sentimentScore > -0.1) {
            return 3;
        }
        if ($sentimentScore > -0.5) {
            return 4;
        }

        return 5;
    }

    protected function getCategoryFromRisk(string $riskLevel): string
    {
        return match ($riskLevel) {
            'critical', 'high' => 'crisis',
            'moderate' => 'mental_health',
            default => 'general',
        };
    }
}