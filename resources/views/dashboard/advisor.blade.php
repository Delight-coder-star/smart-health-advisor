@extends('layouts.app')

@section('title', 'Advisor Dashboard')

@section('content')

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-1">Advisor Dashboard</h2>
        <p class="text-muted">Monitor students and manage alerts</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-primary">{{ $stats['total_students'] }}</div>
            <div class="stat-label">Total Students</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-warning">{{ $stats['high_risk'] }}</div>
            <div class="stat-label">High Risk</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-danger">{{ $stats['critical'] }}</div>
            <div class="stat-label">Critical</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini">
            <div class="stat-value text-danger">{{ $stats['pending_alerts'] }}</div>
            <div class="stat-label">Pending Alerts</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-bell me-2 text-danger"></i>Active Alerts
            </div>
            <div class="card-body">
                @forelse($alerts as $alert)
                    <div class="alert alert-{{ $alert->severity === 'critical' ? 'danger' : ($alert->severity === 'warning' ? 'warning' : 'info') }} mb-2">
                        <strong>{{ $alert->title }}</strong>
                        <p class="small mb-1">{{ Str::limit($alert->message, 100) }}</p>
                        <small>{{ $alert->user->name ?? 'N/A' }} · {{ $alert->created_at ? $alert->created_at->diffForHumans() : '' }}</small>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">No active alerts.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>High-Risk Students
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($highRiskStudents as $student)
                        <a href="{{ route('advisor.student.detail', $student) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $student->name }}</strong>
                                    <div class="small text-muted">{{ $student->student_id ?? 'N/A' }}</div>
                                </div>
                                <span class="risk-badge risk-{{ $student->risk_level }}">
                                    {{ ucfirst($student->risk_level) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-muted text-center">No high-risk students.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection