@extends('layouts.app')

@section('title', 'Health Trends')

@section('content')

<h2 class="mb-3">
    <i class="fas fa-chart-line me-2"></i>Your Health Trends
</h2>

<p class="text-muted">Analysis of your activity over the last 30 days.</p>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-primary">{{ $sessions->count() }}</div>
            <div class="stat-label">Assessments</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-info">{{ $queries->sum() }}</div>
            <div class="stat-label">Questions Asked</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-success">{{ $adherence }}%</div>
            <div class="stat-label">Medication Adherence</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-danger">{{ $missed }}</div>
            <div class="stat-label">Missed Doses</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Risk Score Trend</div>
            <div class="card-body">
                <canvas id="riskTrend" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Questions by Category</div>
            <div class="card-body">
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

@if($latestReport && $latestReport->domain_scores)
<div class="card mb-4">
    <div class="card-header">Domain Radar (Latest Report)</div>
    <div class="card-body">
        <canvas id="domainRadar" height="100"></canvas>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var riskEl = document.getElementById('riskTrend');
    if (riskEl) {
        new Chart(riskEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($sessions->pluck('created_at')->map(fn($d) => $d->format('M d'))) !!},
                datasets: [{
                    label: 'Risk Score',
                    data: {!! json_encode($sessions->pluck('running_risk_score')) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });
    }

    var catEl = document.getElementById('categoryChart');
    if (catEl) {
        new Chart(catEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categories->keys()) !!},
                datasets: [{
                    data: {!! json_encode($categories->values()) !!},
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#7c3aed', '#3b82f6', '#f97316']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    @if($latestReport && $latestReport->domain_scores)
    var radarEl = document.getElementById('domainRadar');
    if (radarEl) {
        var ds = @json($latestReport->domain_scores);
        new Chart(radarEl.getContext('2d'), {
            type: 'radar',
            data: {
                labels: Object.keys(ds).map(function (k) { return k.replace(/_/g, ' '); }),
                datasets: [{
                    label: 'Score',
                    data: Object.values(ds),
                    backgroundColor: 'rgba(79,70,229,0.2)',
                    borderColor: '#4f46e5',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { r: { beginAtZero: true, max: 5 } }
            }
        });
    }
    @endif
});
</script>
@endpush