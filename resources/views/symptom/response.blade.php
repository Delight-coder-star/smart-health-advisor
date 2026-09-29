@extends('layouts.app')

@section('title', 'Health Advice')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">

        @if($response->symptomQuery->urgency === 'critical')
        <div class="alert alert-danger mb-4">
            <h5><i class="fas fa-ambulance me-2"></i>URGENT — Seek Emergency Care</h5>
            <p class="mb-0">
                Your symptoms may indicate a serious condition. Call emergency services or go to the nearest hospital immediately.
            </p>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-header">Your Question</div>
            <div class="card-body">
                <p class="mb-0"><em>"{{ $response->symptomQuery->question }}"</em></p>
                <small class="text-muted">{{ $response->created_at->format('M d, Y H:i') }}</small>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-comments me-2"></i>Advisor Summary
            </div>
            <div class="card-body">
                <p style="white-space: pre-line;">{{ $response->response_text }}</p>
            </div>
        </div>

        @if(!empty($response->possible_conditions))
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-notes-medical me-2"></i>Possible Related Conditions
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach($response->possible_conditions as $c)
                        <li>{{ $c }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="row g-3 mb-3">
            @if(!empty($response->recommended_actions))
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header text-primary">
                        <i class="fas fa-check-circle me-2"></i>What You Can Do
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($response->recommended_actions as $a)
                                <li>{{ $a }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            @if(!empty($response->when_to_see_doctor))
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header text-danger">
                        <i class="fas fa-user-md me-2"></i>When to See a Doctor
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($response->when_to_see_doctor as $w)
                                <li>{{ $w }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>

        @if(!empty($response->prevention_tips))
        <div class="card mb-3">
            <div class="card-header text-success">
                <i class="fas fa-shield-alt me-2"></i>Prevention
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach($response->prevention_tips as $p)
                        <li>{{ $p }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if(!empty($response->red_flags))
        <div class="card mb-3 border-danger">
            <div class="card-header text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Red Flags — Seek Help Immediately
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach($response->red_flags as $f)
                        <li>{{ $f }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if(!empty($response->related_videos))
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-play-circle me-2"></i>Recommended Videos
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($response->related_videos as $v)
                    <div class="col-md-6">
                        <a href="{{ $v['url'] }}" target="_blank" rel="noopener" class="text-decoration-none">
                            <div class="border rounded p-3 h-100">
                                <i class="fab fa-youtube text-danger me-2"></i>
                                <strong>{{ $v['title'] }}</strong>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="alert alert-secondary small">
            <strong>Disclaimer:</strong> This advice is generated automatically and is not a substitute for professional medical diagnosis or treatment.
        </div>

        <div class="text-center">
            <a href="{{ route('symptom.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ask Another Question
            </a>
        </div>

    </div>
</div>

@endsection