@extends('layouts.app')

@section('title', 'Eye Scan Video')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-eye me-2"></i>Eye Scan Recording (Optional)
            </div>
            <div class="card-body text-center">
                <p class="text-muted">Record a 10-second video of your eyes. This is optional.</p>
                <video id="preview" class="rounded bg-dark mb-3" width="480" height="360" autoplay muted playsinline></video>
                <div>
                    <button id="recordBtn" class="btn btn-primary">Start Recording</button>
                    <button id="stopBtn" class="btn btn-danger" disabled>Stop</button>
                    <a href="{{ route('assessment.questions') }}" class="btn btn-outline-secondary">Skip</a>
                </div>
                <div id="statusMessage" class="mt-3 text-muted"></div>
            </div>
        </div>
    </div>
</div>
@endsection