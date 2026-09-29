@extends('layouts.app')

@section('title', '419 - Session Expired')

@section('content')
<div class="text-center py-5">
    <i class="fas fa-clock fa-5x text-warning mb-4"></i>
    <h1 class="display-4">419</h1>
    <h3>Session Expired</h3>
    <p class="text-muted">Your session has expired. Please refresh and try again.</p>
    <a href="{{ route('login') }}" class="btn btn-primary mt-3">
        <i class="fas fa-sign-in-alt me-2"></i>Login Again
    </a>
</div>
@endsection