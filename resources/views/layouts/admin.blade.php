@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-1">Admin Dashboard</h2>
        <p class="text-muted">System overview and metrics</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-primary">{{ $stats['total_users'] }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-info">{{ $stats['total_assessments'] }}</div>
            <div class="stat-label">Assessments</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-warning">{{ $stats['high_risk_count'] }}</div>
            <div class="stat-label">High Risk</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-danger">{{ $stats['pending_alerts'] }}</div>
            <div class="stat-label">Pending Alerts</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2 text-primary"></i>Risk Distribution
            </div>
            <div class="card-body">
                <canvas id="riskChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-line me-2 text-success"></i>Assessment Trend (7 days)
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-history me-2"></i>Recent Assessments</span>
        <a href="{{ route('admin.assessments') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Risk Level</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAssessments as $a)
                        <tr>
                            <td>{{ $a->user->name ?? 'N/A' }}</td>
                            <td>
                                <span class="risk-badge risk-{{ $a->current_risk_level }}">
                                    {{ ucfirst($a->current_risk_level) }}
                                </span>
                            </td>
                            <td>{{ $a->running_risk_score }}/100</td>
                            <td>{{ $a->created_at ? $a->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No assessments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var riskEl = document.getElementById('riskChart');
    if (riskEl) {
        new Chart(riskEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Low', 'Moderate', 'High', 'Critical'],
                datasets: [{
                    data: [
                        {{ $riskDistribution['low'] }},
                        {{ $riskDistribution['moderate'] }},
                        {{ $riskDistribution['high'] }},
                        {{ $riskDistribution['critical'] }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#f97316', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    var trendEl = document.getElementById('trendChart');
    if (trendEl) {
        new Chart(trendEl.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($assessmentTrend->pluck('date')) !!},
                datasets: [{
                    label: 'Assessments',
                    data: {!! json_encode($assessmentTrend->pluck('count')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});
</script>
@endpush