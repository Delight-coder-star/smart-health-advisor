@extends('layouts.app')

@section('title', '403 - Forbidden')

@section('content')
<div class="text-center py-5">
    <i class="fas fa-lock fa-5x text-danger mb-4"></i>
    <h1 class="display-4">403</h1>
    <h3>Access Forbidden</h3>
    <p class="text-muted">You don't have permission to access this page.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">
        <i class="fas fa-home me-2"></i>Back to Dashboard
    </a>
</div>
@endsection