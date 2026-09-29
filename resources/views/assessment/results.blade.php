@extends('layouts.app')

@section('title', 'Assessment Results')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <h2>Assessment Complete</h2>
                <p class="text-muted">Here are your results.</p>
                <div class="row justify-content-center mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Risk Level</h6>
                                <span class="risk-badge risk-{{ $assessment->risk_level }}">{{ ucfirst($assessment->risk_level) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6 class="text-muted">Score</h6>
                                <h3>{{ $assessment->total_score }}/100</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Recommendations</div>
            <div class="card-body">
                @forelse($recommendations as $rec)
                    <div class="mb-3">
                        <h6>{{ $rec->title }}</h6>
                        <p class="text-muted small mb-0">{{ $rec->description }}</p>
                    </div>
                @empty
                    <p class="text-muted">No recommendations.</p>
                @endforelse
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection