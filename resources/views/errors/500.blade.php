@extends('layouts.app')

@section('title', '500 - Server Error')

@section('content')
<div class="text-center py-5">
    <i class="fas fa-server fa-5x text-danger mb-4"></i>
    <h1 class="display-4">500</h1>
    <h3>Server Error</h3>
    <p class="text-muted">Something went wrong on our end. Please try again later.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
        <i class="fas fa-home me-2"></i>Back to Dashboard
    </a>
</div>
@endsection