@extends('layouts.app')

@section('title', 'Assessment Questions')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="fas fa-clipboard-check me-2"></i>Health Assessment</span>
                <span class="badge bg-primary" id="progressBadge">Step 1 of {{ count($questions) }}</span>
            </div>
            <div class="card-body">
                <div class="progress mb-4">
                    <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                </div>

                <form id="assessmentForm" action="{{ route('assessment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="responses" id="responsesInput">

                    <div id="questionsContainer">
                        @foreach($questions as $index => $question)
                        <div class="question-step" data-index="{{ $index }}" data-key="{{ $question['key'] }}" style="{{ $index > 0 ? 'display:none;' : '' }}">
                            <h5 class="mb-4">{{ $question['text'] }}</h5>
                            @if($question['type'] === 'select')
                                @foreach($question['options'] as $option)
                                <div class="form-check mb-2 p-3 border rounded" style="cursor:pointer;">
                                    <input class="form-check-input" type="radio" name="q_{{ $question['key'] }}"
                                           id="q_{{ $question['key'] }}_{{ $option['value'] }}"
                                           value="{{ $option['value'] }}"
                                           data-key="{{ $question['key'] }}"
                                           data-text="{{ $option['label'] }}">
                                    <label class="form-check-label w-100" for="q_{{ $question['key'] }}_{{ $option['value'] }}">
                                        {{ $option['label'] }}
                                    </label>
                                </div>
                                @endforeach
                            @elseif($question['type'] === 'textarea')
                                <textarea class="form-control" name="q_{{ $question['key'] }}" data-key="{{ $question['key'] }}" rows="4" placeholder="Describe your experience..."></textarea>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled>Previous</button>
                        <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection