@extends('layouts.app')

@section('title', 'Ask Health Advisor')

@section('content')

<h2 class="mb-3">
    <i class="fas fa-stethoscope me-2"></i>Ask the Health Advisor
</h2>

<p class="text-muted">
    Describe what you're feeling or experiencing. Our AI will suggest possible conditions, advice, and guidance.
</p>

<div class="alert alert-warning">
    <strong><i class="fas fa-exclamation-triangle me-2"></i>Important:</strong>
    This is not a diagnosis. Always consult a qualified medical professional for serious or persistent symptoms.
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('symptom.ask') }}">
            @csrf
            <textarea
                name="question"
                class="form-control mb-3"
                rows="5"
                placeholder="Example: I have had a headache and blurred vision for two days. Could this be high blood pressure?"
                required
                minlength="5"
                maxlength="2000">{{ old('question') }}</textarea>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Get Advice
            </button>
        </form>
    </div>
</div>

@if($recent->count() > 0)
<div class="card">
    <div class="card-header">
        <i class="fas fa-history me-2"></i>Recent Questions
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th>Question</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent as $q)
                <tr>
                    <td>{{ \Illuminate\Support\Str::limit($q->question, 70) }}</td>
                    <td>{{ ucfirst($q->category ?? 'general') }}</td>
                    <td>
                        <span class="badge bg-{{ $q->urgency_color }}">
                            {{ ucfirst($q->urgency) }}
                        </span>
                    </td>
                    <td>{{ $q->created_at->format('M d, H:i') }}</td>
                    <td>
                        @if($q->response)
                            <a href="{{ route('symptom.response', $q->response) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection