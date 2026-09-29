@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if(auth()->user()->isAdmin())
        <div class="text-center py-5">
            <h2>Admin Dashboard</h2>
            <p class="text-muted">Use the navigation menu to manage the system.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Go to Admin Panel</a>
        </div>
    @elseif(auth()->user()->isAdvisor())
        <div class="text-center py-5">
            <h2>Advisor Dashboard</h2>
            <p class="text-muted">Manage students and alerts from the advisor panel.</p>
            <a href="{{ route('advisor.dashboard') }}" class="btn btn-primary">Go to Advisor Panel</a>
        </div>
    @else
        <div class="text-center py-5">
            <h2>Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-muted">Your dashboard is ready.</p>
            <a href="{{ route('assessment.session.start') }}" class="btn btn-primary">Start Assessment</a>
        </div>
    @endif
@endsection