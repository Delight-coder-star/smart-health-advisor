@extends('layouts.app')

@section('title', 'Assessments')

@section('content')
<h2 class="mb-4">All Assessments</h2>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Student</th><th>Mode</th><th>Risk</th><th>Score</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($assessments as $a)
                <tr>
                    <td>{{ $a->user->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($a->mode) }}</td>
                    <td><span class="risk-badge risk-{{ $a->current_risk_level }}">{{ ucfirst($a->current_risk_level) }}</span></td>
                    <td>{{ $a->running_risk_score }}/100</td>
                    <td>{{ $a->created_at ? $a->created_at->format('M d, Y H:i') : '' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No assessments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $assessments->links() }}</div>
@endsection