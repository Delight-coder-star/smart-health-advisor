@extends('layouts.app')

@section('title', 'Assessment Report')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <h2>Assessment Report</h2>
                <p class="text-muted mb-3">
                    {{ $report->generated_at ? $report->generated_at->format('M d, Y H:i') : '' }}
                </p>
                <div>
                    <span class="risk-badge risk-{{ $report->final_risk_level }} fs-6">
                        {{ ucfirst($report->final_risk_level) }} risk
                    </span>
                    <span class="ms-3">Score: <strong>{{ $report->final_risk_score }}/100</strong></span>
                    <span class="ms-3">Sentiment: <strong>{{ number_format($report->final_sentiment, 2) }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Merged advice per category --}}
        @if($report->session->merged_advice)
        <h3 class="mt-4 mb-3">
            <i class="fas fa-user-md me-2"></i>Your Personalized Advice
        </h3>

        @foreach($report->session->merged_advice as $block)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-folder-open me-2"></i>{{ $block['label'] }}
                </span>
                <span class="risk-badge risk-{{ $block['level'] }}">
                    {{ ucfirst($block['level']) }}
                </span>
            </div>
            <div class="card-body">

                <p class="mb-3">
                    <strong>Score:</strong> {{ $block['average_score'] }}/5 &nbsp;
                    <strong>Sentiment:</strong> {{ number_format($block['average_sentiment'], 2) }}
                </p>

                <p class="mb-4">{{ $block['summary'] }}</p>

                @if(!empty($block['actions']))
                <h6 class="text-primary"><i class="fas fa-check-circle me-2"></i>What You Can Do</h6>
                <ul>
                    @foreach($block['actions'] as $a)<li>{{ $a }}</li>@endforeach
                </ul>
                @endif

                @if(!empty($block['see_doctor']))
                <h6 class="text-danger mt-3"><i class="fas fa-user-md me-2"></i>When to See a Doctor</h6>
                <ul>
                    @foreach($block['see_doctor'] as $w)<li>{{ $w }}</li>@endforeach
                </ul>
                @endif

                @if(!empty($block['prevention']))
                <h6 class="text-success mt-3"><i class="fas fa-shield-alt me-2"></i>Prevention</h6>
                <ul>
                    @foreach($block['prevention'] as $p)<li>{{ $p }}</li>@endforeach
                </ul>
                @endif

                @if(!empty($block['red_flags']))
                <h6 class="text-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i>Red Flags</h6>
                <ul>
                    @foreach($block['red_flags'] as $f)<li>{{ $f }}</li>@endforeach
                </ul>
                @endif

                @if(!empty($block['resource_links']))
                <h6 class="mt-3"><i class="fas fa-link me-2"></i>Resources</h6>
                <ul>
                    @foreach($block['resource_links'] as $r)<li>{{ $r }}</li>@endforeach
                </ul>
                @endif

            </div>
        </div>
        @endforeach
        @endif

        {{-- Raw answers log --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>Full Answer Log ({{ $report->session->answers->count() }})
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Category</th><th>Question</th><th>Answer</th><th>Score</th></tr>
                    </thead>
                    <tbody>
                        @foreach($report->session->answers as $i => $a)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ ucwords(str_replace('_', ' ', $a->question->domain)) }}
                                </span>
                            </td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($a->question->question_text, 60) }}</td>
                            <td class="small">{{ \Illuminate\Support\Str::limit($a->raw_answer, 60) }}</td>
                            <td>{{ $a->numeric_score }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Back to Dashboard
            </a>
            <a href="{{ route('assessment.session.choose') }}" class="btn btn-outline-primary">
                <i class="fas fa-redo me-2"></i>New Assessment
            </a>
        </div>

    </div>
</div>

@endsection