@extends('layouts.app')

@section('title', 'Student Chat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-comments me-2"></i>Secure Chat
            </div>
            <div class="card-body" style="height: 500px; overflow-y: auto;" id="chatBody">
                <div class="text-center text-muted py-5">
                    <i class="fas fa-comment-dots fa-3x mb-3"></i>
                    <p>Chat functionality coming soon.</p>
                    <p class="small">Advisors can communicate with students securely here.</p>
                </div>
            </div>
            <div class="card-footer">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Type a message..." disabled>
                    <button class="btn btn-primary" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection