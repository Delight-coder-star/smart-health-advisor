@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')

{{-- Crisis strip if user is currently critical --}}
@if(($user->risk_level ?? 'low') === 'critical')
<div class="crisis-strip mb-4">
    <i class="fas fa-triangle-exclamation fa-2x"></i>
    <div class="flex-grow-1">
        <strong>You are not alone.</strong>
        <div class="small">Our counselling team has been notified. Call <strong>+263 4 123 4567</strong> anytime.</div>
    </div>
</div>
@endif

{{-- Hero with 4 mode buttons --}}
<div class="hero-card mb-4">
    <div class="hero-inner row align-items-center">
        <div class="col-md-7">
            <h2 class="mb-1">Welcome back, {{ $user->name }} 👋</h2>
            <p class="mb-4 opacity-75">Your mental health journey matters. Choose how you'd like to check in today.</p>

            <form action="{{ route('assessment.session.start') }}" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col-sm-6 col-lg-3">
                        <button type="submit" name="mode" value="adaptive" class="mode-card">
                            <div class="mode-title"><i class="fas fa-brain me-2"></i>Adaptive</div>
                            <div class="mode-desc">AI-tailored, ~20 questions</div>
                        </button>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <button type="submit" name="mode" value="quick" class="mode-card">
                            <div class="mode-title"><i class="fas fa-bolt me-2"></i>Quick</div>
                            <div class="mode-desc">Screening, 8 questions</div>
                        </button>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <button type="submit" name="mode" value="comprehensive" class="mode-card">
                            <div class="mode-title"><i class="fas fa-layer-group me-2"></i>Comprehensive</div>
                            <div class="mode-desc">Full battery, 40 questions</div>
                        </button>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <button type="submit" name="mode" value="targeted" class="mode-card">
                            <div class="mode-title"><i class="fas fa-crosshairs me-2"></i>Targeted</div>
                            <div class="mode-desc">Focus on flagged areas</div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-5 text-center mt-4 mt-md-0">
            <div class="d-inline-block p-4" style="background: rgba(255,255,255,0.1); border-radius: 16px;">
                <i class="fas fa-heart-pulse" style="font-size: 5rem; opacity: 0.9;"></i>
                <div class="mt-2 small opacity-75">Harare Polytechnic College</div>
            </div>
        </div>
    </div>
</div>

{{-- Resume banner --}}
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

{{-- Stat tiles --}}
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

{{-- Risk / Sentiment / Top concerns --}}
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
                <div class="progress mt-2" style="height:8px;">
                    <div class="progress-bar" style="width: {{ $latestReport->final_risk_score }}%"></div>
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
                <small class="text-muted">Score: {{ number_format($latestReport->final_sentiment, 2) }}</small>
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
@else
<div class="card mb-4">
    <div class="card-body text-center py-5">
        <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
        <h4>Start Your First Assessment</h4>
        <p class="text-muted mb-4">
            Our AI-powered system will ask you tailored questions, analyse each response in real time,
            and build a comprehensive report for you.
        </p>
        <form action="{{ route('assessment.session.start') }}" method="POST">
            @csrf
            <button type="submit" name="mode" value="adaptive" class="btn btn-primary btn-lg">
                <i class="fas fa-play me-2"></i>Begin Adaptive Assessment
            </button>
        </form>
    </div>
</div>
@endif

{{-- Charts row --}}
@if($assessmentHistory->count() > 1)
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-line me-2 text-primary"></i>Progress Over Time</span>
                <small class="text-muted">Last {{ $assessmentHistory->count() }} sessions</small>
            </div>
            <div class="card-body">
                <canvas id="progressChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-radar me-2 text-info"></i>Domain Snapshot
            </div>
            <div class="card-body">
                <canvas id="domainRadar" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Recommendations + Health tips --}}
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
                            @if(!empty($rec->action_items))
                                <ul class="list-unstyled small mb-0">
                                    @foreach(array_slice($rec->action_items, 0, 2) as $item)
                                        <li><i class="fas fa-check-circle text-success me-1"></i>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                        <p class="mb-0">No pending recommendations. Great job!</p>
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

{{-- Recent sessions --}}
@if($recentSessions->count() > 0)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-history me-2"></i>Recent Assessment Sessions</span>
        <span class="badge bg-secondary">{{ $recentSessions->count() }}</span>
    </div>
    <div class="card-body p-0">
        @foreach($recentSessions as $session)
            <div class="session-row d-flex align-items-center">
                <div class="me-3 text-center" style="width:60px;">
                    <div class="small text-muted">{{ $session->started_at ? $session->started_at->format('M d') : '—' }}</div>
                    <div class="fw-bold">{{ $session->started_at ? $session->started_at->format('H:i') : '' }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-light text-dark border">{{ ucfirst($session->mode) }}</span>
                        <span class="risk-badge risk-{{ $session->current_risk_level }}">
                            {{ ucfirst($session->current_risk_level) }}
                        </span>
                        @if($session->status === 'active')
                            <span class="badge bg-warning text-dark">In progress</span>
                        @elseif($session->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                        @endif
                        @if($session->crisis_flag_raised)
                            <span class="badge bg-danger"><i class="fas fa-flag me-1"></i>Crisis flag</span>
                        @endif
                    </div>
                    <div class="small text-muted">
                        {{ $session->questions_answered }} answered
                        · Risk {{ $session->running_risk_score }}/100
                        · Sentiment {{ number_format($session->running_sentiment, 2) }}
                    </div>
                    @if(!empty($session->domains_covered))
                        <div class="mt-1">
                            @foreach(array_slice($session->domains_covered, 0, 6) as $d)
                                <span class="domain-chip">{{ ucfirst($d) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="text-end">
                    @if($session->status === 'active')
                        <a href="{{ route('assessment.session.run', $session) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-play me-1"></i>Resume
                        </a>
                    @elseif($session->report)
                        <a href="{{ route('assessment.report.show', $session->report) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-medical me-1"></i>Report
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Quick actions --}}
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
        <a href="{{ route('profile.edit') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-cog fa-2x text-info mb-2"></i>
                    <h6 class="mb-1">Update Profile</h6>
                    <p class="text-muted small mb-0">Keep details current</p>
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
                <strong>+263 4 123 4567</strong>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    @if($assessmentHistory->count() > 1)
    var progressEl = document.getElementById('progressChart');
    if (progressEl) {
        new Chart(progressEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($assessmentHistory->pluck('generated_at')->map(function ($d) { return $d ? \Carbon\Carbon::parse($d)->format('M d') : ''; })) !!},
                datasets: [
                    {
                        label: 'Risk Score',
                        data: {!! json_encode($assessmentHistory->pluck('final_risk_score')) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79,70,229,0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } } },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { callback: function(v){ return v + '%'; } }, grid: { color: '#f3f4f6' } },
                    x: { ticks: { color: '#6b7280' }, grid: { display: false } }
                }
            }
        });
    }

    @if($latestReport && !empty($latestReport->domain_scores))
    var radarEl = document.getElementById('domainRadar');
    if (radarEl) {
        var ds = @json($latestReport->domain_scores);
        new Chart(radarEl.getContext('2d'), {
            type: 'radar',
            data: {
                labels: Object.keys(ds).map(function(k){ return k.replace(/_/g, ' ').replace(/\b\w/g, function(c){ return c.toUpperCase(); }); }),
                datasets: [{
                    label: 'Score',
                    data: Object.values(ds),
                    backgroundColor: 'rgba(79,70,229,0.2)',
                    borderColor: '#4f46e5',
                    borderWidth: 2,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { stepSize: 1, display: false },
                        grid: { color: '#e5e7eb' },
                        angleLines: { color: '#e5e7eb' },
                        pointLabels: { font: { size: 11 }, color: '#4b5563' }
                    }
                }
            }
        });
    }
    @endif
    @endif
});
</script>
@endpush