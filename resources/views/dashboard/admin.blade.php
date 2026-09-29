@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-1">Admin Dashboard</h2>
        <p class="text-muted">System overview</p>
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

<div class="card">
    <div class="card-header"><i class="fas fa-history me-2"></i>Recent Assessments</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Student</th><th>Risk</th><th>Score</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($recentAssessments as $a)
                <tr>
                    <td>{{ $a->user->name ?? 'N/A' }}</td>
                    <td><span class="risk-badge risk-{{ $a->current_risk_level }}">{{ ucfirst($a->current_risk_level) }}</span></td>
                    <td>{{ $a->running_risk_score }}/100</td>
                    <td>{{ $a->created_at ? $a->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No assessments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection