<?php

namespace App\Http\Controllers;

use App\Models\SymptomQuery;
use App\Models\AiAdviceResponse;
use App\Services\SymptomAdvisorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SymptomAdvisorController extends Controller
{
    public function __construct(
        protected SymptomAdvisorService $advisor
    ) {
    }

    public function index()
    {
        $recent = SymptomQuery::where('user_id', Auth::id())
            ->with('response')
            ->latest()
            ->limit(10)
            ->get();

        return view('symptom.index', compact('recent'));
    }

    public function ask(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $response = $this->advisor->advise(Auth::id(), $data['question']);

        return redirect()->route('symptom.response', $response->id);
    }

    public function response(AiAdviceResponse $response)
    {
        abort_unless($response->user_id === Auth::id(), 403);

        $response->load('symptomQuery');

        return view('symptom.response', compact('response'));
    }
}