@extends('layouts.app')

@section('title', '404 - Not Found')

@section('content')
<div class="text-center py-5">
    <i class="fas fa-search fa-5x text-warning mb-4"></i>
    <h1 class="display-4">404</h1>
    <h3>Page Not Found</h3>
    <p class="text-muted">The page you're looking for doesn't exist.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
        <i class="fas fa-home me-2"></i>Back to Dashboard
    </a>
</div>
@endsection