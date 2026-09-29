@extends('layouts.app')

@section('title', 'Assessment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-check fa-4x text-primary mb-4"></i>
                <h3>Mental Health Assessment</h3>
                <p class="text-muted mb-4">About 5-10 minutes. Your responses are confidential.</p>
                <div class="alert alert-warning text-start mb-4">
                    <h6><i class="fas fa-shield-alt me-2"></i>Privacy</h6>
                    <ul class="mb-0 small">
                        <li>Responses are encrypted and stored securely</li>
                        <li>Only counselors see high-risk alerts</li>
                    </ul>
                </div>
                <a href="{{ route('assessment.questions') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-play me-2"></i>Begin Assessment
                </a>
            </div>
        </div>
    </div>
</div>
@endsection