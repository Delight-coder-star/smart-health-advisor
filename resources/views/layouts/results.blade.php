@extends('layouts.app')

@section('title', 'Assessment Results')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        {{-- Results Header --}}
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <div class="sentiment-indicator mb-3" style="font-size: 4rem;">
                    {{ $assessment->sentiment_emoji }}
                </div>
                <h2 class="mb-2">Assessment Complete</h2>
                <p class="text-muted mb-4">Here are your results and personalized recommendations.</p>
                
                <div class="row justify-content-center">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h6 class="text-muted mb-2">Risk Level</h6>
                            <span class="risk-badge risk-{{ $assessment->risk_level }} fs-6">
                                {{ ucfirst($assessment->risk_level) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h6 class="text-muted mb-2">Risk Score</h6>
                            <h3 class="mb-0">{{ $assessment->total_score }}<small class="text-muted">/100</small></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h6 class="text-muted mb-2">Emotional State</h6>
                            <h5 class="mb-0">{{ ucfirst(str_replace('_', ' ', $assessment->sentiment_label ?? 'Neutral')) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Crisis Alert --}}
        @if($assessment->risk_level === 'critical')
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Immediate Attention Required</h5>
            <p>Based on your responses, we strongly recommend reaching out for professional help immediately.</p>
            <hr>
            <p class="mb-0">
                <strong>Crisis Helpline:</strong> +263 4 123 4567<br>
                <strong>Campus Counseling:</strong> Visit the Student Affairs Office
            </p>
        </div>
        @endif

        {{-- AI Analysis --}}
        @if($assessment->ai_analysis)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-robot me-2 text-primary"></i>AI Analysis Summary
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Detected Indicators</h6>
                        @if($assessment->indicators && count($assessment->indicators) > 0)
                        <ul class="list-unstyled">
                            @foreach($assessment->indicators as $indicator)
                            <li class="mb-2">
                                <span class="badge bg-{{ $indicator['severity'] === 'critical' ? 'danger' : 'warning' }} me-2">
                                    {{ ucfirst($indicator['severity']) }}
                                </span>
                                {{ ucfirst(str_replace('_', ' ', $indicator['key'])) }}
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-muted">No significant indicators detected.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6>Keywords Detected</h6>
                        @php
                            $keywords = $assessment->ai_analysis['sentiment']['keywords'] ?? [];
                        @endphp
                        @if(count($keywords) > 0)
                        <div>
                            @foreach(array_slice($keywords, 0, 10) as $keyword)
                            <span class="badge bg-secondary me-1 mb-1">{{ $keyword }}</span>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted">No concerning keywords detected.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Video Analysis --}}
        @if($assessment->videoAnalysis && $assessment->videoAnalysis->status === 'completed')
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-eye me-2 text-info"></i>Eye Scan Analysis
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h6 class="text-muted">Pupil Dilation</h6>
                        <h4>{{ round($assessment->videoAnalysis->pupil_dilation_score * 100) }}%</h4>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Eye Movement</h6>
                        <h4>{{ round($assessment->videoAnalysis->eye_movement_score * 100) }}%</h4>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Blink Rate</h6>
                        <h4>{{ $assessment->videoAnalysis->blink_rate }}/min</h4>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Recommendations --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2 text-warning"></i>Your Personalized Recommendations
            </div>
            <div class="card-body">
                @forelse($recommendations as $recommendation)
                <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">{{ $recommendation->title }}</h5>
                        <span class="badge bg-{{ $recommendation->priority_color }}">
                            {{ ucfirst($recommendation->priority) }}
                        </span>
                    </div>
                    <p class="text-muted">{{ $recommendation->description }}</p>
                    
                    @if($recommendation->action_items && count($recommendation->action_items) > 0)
                    <h6 class="mt-3">Action Items:</h6>
                    <ul class="list-group list-group-flush">
                        @foreach($recommendation->action_items as $item)
                        <li class="list-group-item px-0">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $item }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center py-4">No recommendations available.</p>
                @endforelse
            </div>
        </div>

        {{-- Health Tips --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-heart me-2 text-danger"></i>Health Tips for You
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($healthTips as $tip)
                    <div class="col-md-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6>
                                @if($tip['icon'] ?? null)
                                <i class="{{ $tip['icon'] }} me-1"></i>
                                @endif
                                {{ $tip['title'] }}
                            </h6>
                            <p class="text-muted small mb-0">{{ $tip['content'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="text-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Back to Dashboard
            </a>
            <a href="{{ route('assessment.index') }}" class="btn btn-outline-primary ms-2">
                <i class="fas fa-redo me-2"></i>Take Another Assessment
            </a>
        </div>
    </div>
</div>
@endsection