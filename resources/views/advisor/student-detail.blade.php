@extends('layouts.app')

@section('title', 'Student Detail')

@section('content')
<a href="{{ route('advisor.students') }}" class="btn btn-sm btn-outline-secondary mb-3">← Back</a>

<h2 class="mb-1">{{ $student->name }}</h2>
<p class="text-muted">{{ $student->email }}</p>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="stat-mini"><div class="stat-value">{{ $student->risk_score }}</div><div class="stat-label">Risk Score</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-value">{{ $student->assessments->count() }}</div><div class="stat-label">Assessments</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-value"><span class="risk-badge risk-{{ $student->risk_level }}">{{ ucfirst($student->risk_level) }}</span></div><div class="stat-label">Level</div></div></div>
</div>

<div class="card">
    <div class="card-header">Recent Assessments</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light"><tr><th>Date</th><th>Risk</th><th>Score</th></tr></thead>
            <tbody>
                @forelse($student->assessments as $a)
                <tr>
                    <td>{{ $a->created_at ? $a->created_at->format('M d, Y') : '' }}</td>
                    <td><span class="risk-badge risk-{{ $a->risk_level }}">{{ ucfirst($a->risk_level) }}</span></td>
                    <td>{{ $a->total_score }}/100</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-3">No assessments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection