@extends('layouts.app')

@section('title', 'Advisor Panel')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-md fa-4x text-primary mb-4"></i>
                <h3>Advisor Panel</h3>
                <p class="text-muted mb-4">
                    Manage student assessments, alerts, and recommendations.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('advisor.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('advisor.students') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>View Students
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection