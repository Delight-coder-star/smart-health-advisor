@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')

@if(($user->risk_level ?? 'low') === 'critical')
<div class="crisis-strip mb-4">
    <i class="fas fa-triangle-exclamation fa-2x"></i>
    <div class="flex-grow-1">
        <strong>You are not alone.</strong>
        <div class="small">
            Our counselling team has been notified. Call <strong>+263 4 123 4567</strong> anytime.
        </div>
    </div>
</div>
@endif

<div class="hero-card mb-4">
    <div class="hero-inner row align-items-center">
        <div class="col-md-7">
            <h2 class="mb-1">Welcome back, {{ $user->name }} 👋</h2>
            <p class="mb-4 opacity-75">
                Your mental health journey matters. Choose how you'd like to check in today.
            </p>

            <a href="{{ route('assessment.session.choose') }}" class="btn btn-light btn-lg">
    <i class="fas fa-play me-2"></i>Start New Assessment
</a>
        </div>

        <div class="col-md-5 text-center mt-4 mt-md-0">
            <div class="d-inline-block p-4" style="background: rgba(255,255,255,0.1); border-radius: 16px;">
                <img src="{{ asset('images/health.png') }}" alt="Health" style="max-width: 140px; max-height: 140px;">
                <div class="mt-2 small opacity-75">Harare Polytechnic College</div>
            </div>
        </div>
    </div>
</div>

@if($activeSession)
<div class="resume-banner mb-4 d-flex align-items-center">
    <i class="fas fa-play-circle fa-2x me-3"></i>
    <div class="flex-grow-1">
        <strong>You have an assessment in progress</strong>
        <div class="small opacity-90">
            Started {{ $activeSession->started_at ? $activeSession->started_at->diffForHumans() : 'recently' }}
            · {{ $activeSession->questions_answered }} of {{ $activeSession->max_questions }} answered
        </div>
    </div>
    <a href="{{ route('assessment.session.run', $activeSession) }}" class="btn btn-light">
        <i class="fas fa-play me-1"></i>Resume
    </a>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-primary">{{ $stats['total_sessions'] }}</div>
            <div class="stat-label">Assessments</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-success">{{ $stats['streak_days'] }}</div>
            <div class="stat-label">Day Streak</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-info">{{ $stats['domains_covered'] }}</div>
            <div class="stat-label">Domains Covered</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-warning">{{ $stats['pending_recommendations'] }}</div>
            <div class="stat-label">Open Tasks</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('symptom.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-stethoscope fa-2x text-primary mb-2"></i>
                    <h6 class="mb-1">Ask Health Advisor</h6>
                    <p class="text-muted small mb-0">Get AI medical advice</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('trends.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                    <h6 class="mb-1">Health Trends</h6>
                    <p class="text-muted small mb-0">Track your analytics</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('medications.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-pills fa-2x text-warning mb-2"></i>
                    <h6 class="mb-1">Medication Reminders</h6>
                    <p class="text-muted small mb-0">Manage your meds</p>
                </div>
            </div>
        </a>
    </div>
</div>

@if($latestReport)
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small mb-1">CURRENT RISK</div>
                <div style="font-size:3.25rem; line-height:1;" class="mb-2">
                    @if($latestReport->final_risk_level === 'critical') 😢
                    @elseif($latestReport->final_risk_level === 'high') 😟
                    @elseif($latestReport->final_risk_level === 'moderate') 😐
                    @else 🙂
                    @endif
                </div>
                <span class="risk-badge risk-{{ $latestReport->final_risk_level }}">
                    {{ ucfirst($latestReport->final_risk_level) }}
                </span>
                <div class="mt-3" style="font-size:2rem; font-weight:700;">
                    {{ $latestReport->final_risk_score }}<small class="text-muted" style="font-size:1rem;">/100</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small mb-1">EMOTIONAL TONE</div>
                <div style="font-size:3.25rem; line-height:1;" class="mb-2">
                    @if($latestReport->final_sentiment > 0.3) 😊
                    @elseif($latestReport->final_sentiment > 0.05) 🙂
                    @elseif($latestReport->final_sentiment > -0.05) 😐
                    @elseif($latestReport->final_sentiment > -0.3) 😟
                    @else 😢
                    @endif
                </div>
                <h5 class="mb-1">
                    @if($latestReport->final_sentiment > 0.3) Positive
                    @elseif($latestReport->final_sentiment > 0.05) Slightly positive
                    @elseif($latestReport->final_sentiment > -0.05) Neutral
                    @elseif($latestReport->final_sentiment > -0.3) Slightly negative
                    @else Negative
                    @endif
                </h5>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small mb-2">TOP CONCERNS</div>
                @forelse(array_slice($latestReport->top_concerns ?? [], 0, 3) as $concern)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-semibold small">{{ $concern['label'] ?? 'Concern' }}</div>
                            <span class="risk-badge risk-{{ $concern['level'] ?? 'low' }}" style="font-size:0.65rem;">
                                {{ ucfirst($concern['level'] ?? 'low') }}
                            </span>
                        </div>
                        <div>
                            <strong>{{ $concern['score'] ?? 0 }}</strong><small class="text-muted">/5</small>
                        </div>
                    </div>
                @empty
                    <div class="text-muted py-3 text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                        No concerns detected.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-lightbulb me-2 text-warning"></i>Your Recommendations</span>
                <span class="badge bg-primary">{{ $recommendations->count() }}</span>
            </div>
            <div class="card-body">
                @forelse($recommendations as $rec)
                    <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="me-3">
                            <span class="badge bg-secondary">{{ ucfirst($rec->priority) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $rec->title }}</h6>
                            <p class="text-muted small mb-2">{{ Str::limit($rec->description, 120) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">No pending recommendations.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-heart me-2 text-danger"></i>Health Tips
            </div>
            <div class="card-body">
                @forelse($healthTips as $tip)
                    <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <h6 class="mb-1">
                            @if($tip->icon)<i class="{{ $tip->icon }} me-1"></i>@endif
                            {{ $tip->title }}
                        </h6>
                        <p class="text-muted small mb-0">{{ Str::limit($tip->content, 130) }}</p>
                    </div>
                @empty
                    <p class="text-muted text-center py-4">No health tips available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('assessment.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                    <h6 class="mb-1">Classic Assessment</h6>
                    <p class="text-muted small mb-0">Standard question set</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-medical-alt fa-2x text-info mb-2"></i>
                    <h6 class="mb-1">My Reports</h6>
                    <p class="text-muted small mb-0">Daily, weekly, monthly</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <div class="card h-100" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
            <div class="card-body text-center">
                <i class="fas fa-phone-alt fa-2x mb-2"></i>
                <h6 class="mb-1">Need help now?</h6>
                <p class="small mb-1 opacity-75">Crisis Helpline</p>
              <strong>+263 717 923 021</strong>
            </div>
        </div>
    </div>
</div>

@endsection