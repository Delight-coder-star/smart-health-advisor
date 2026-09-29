<?php

namespace App\Http\Controllers;

use App\Models\AssessmentReport;
use App\Models\AssessmentSession;
use App\Models\SessionQuestion;
use App\Services\AssessmentFlowService;
use App\Services\CategoryQuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionAssessmentController extends Controller
{
    public function __construct(
        protected AssessmentFlowService $flow
    ) {
    }

    public function choose()
    {
        $categories = CategoryQuestionBank::listForPicker();
        return view('assessment.choose', compact('categories'));
    }

    public function start(Request $request)
    {
        $data = $request->validate([
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string'],
            'mode' => ['nullable', 'string', 'in:adaptive,quick,comprehensive,targeted'],
        ]);

        $mode = $data['mode'] ?? 'adaptive';
        $selected = $data['categories'];

        // Total questions available across selected categories
        $available = CategoryQuestionBank::totalFor($selected);

        // Session length depends on the mode AND the number of categories chosen
        $max = match ($mode) {
            'quick'         => min($available, max(6, count($selected) * 3)),
            'targeted'      => min($available, max(10, count($selected) * 5)),
            'comprehensive' => min($available, count($selected) * 15),
            default         => min($available, max(15, count($selected) * 8)), // adaptive
        };

        // Safety net — always at least 5 questions
        $max = max(5, $max);

        $session = $this->flow->startSession(Auth::user(), $mode, $selected, $max);

        return redirect()->route('assessment.session.run', $session);
    }

    public function run(AssessmentSession $session)
    {
        abort_unless($session->user_id === Auth::id(), 403);
        return view('assessment.session', compact('session'));
    }

    public function next(AssessmentSession $session)
    {
        abort_unless($session->user_id === Auth::id(), 403);

        $result = $this->flow->nextQuestion($session);

        if (!empty($result['done'])) {
            return response()->json(['done' => true]);
        }

        return response()->json($result);
    }

    public function answer(Request $request, AssessmentSession $session)
    {
        abort_unless($session->user_id === Auth::id(), 403);

        $data = $request->validate([
            'question_id' => 'required|exists:session_questions,id',
            'answer' => 'nullable|string',
            'choice_index' => 'nullable|integer',
            'latency_ms' => 'nullable|integer',
        ]);

        $question = SessionQuestion::findOrFail($data['question_id']);
        abort_unless($question->session_id === $session->id, 403);

        $answer = $this->flow->submitAnswer($session, $question, $data);
        $fresh = $session->fresh();

        return response()->json([
            'ok' => true,
            'analysis' => [
                'sentiment' => $answer->sentiment_score,
                'sentiment_label' => $answer->sentiment_label,
                'numeric_score' => $answer->numeric_score,
                'keywords' => $answer->keywords_detected,
                'crisis' => $answer->crisis_detected,
                'self_harm' => $answer->self_harm_flagged,
                'substance' => $answer->substance_flagged,
            ],
            'session' => [
                'risk_score' => $fresh->running_risk_score,
                'risk_level' => $fresh->current_risk_level,
                'answered' => $fresh->questions_answered,
                'asked' => $fresh->questions_asked,
                'max' => $fresh->max_questions,
            ],
        ]);
    }

    public function finish(AssessmentSession $session)
    {
        abort_unless($session->user_id === Auth::id(), 403);

        $report = $this->flow->finishSession($session);

        return response()->json([
            'ok' => true,
            'report_id' => $report->id,
            'redirect' => route('assessment.report.show', $report),
        ]);
    }

    public function report(AssessmentReport $report)
    {
        $user = Auth::user();
        abort_unless(
            $report->user_id === $user->id || $user->isAdvisor() || $user->isAdmin(),
            403
        );

        $report->load('session.questions', 'session.answers.question');

        return view('assessment.report', compact('report'));
    }
}