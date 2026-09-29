@extends('layouts.app')

@section('title', 'Health Assessment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-check me-2"></i>Health Assessment</span>
                <span class="badge bg-primary" id="progressBadge">Question 1 of {{ count($questions) }}</span>
            </div>
            <div class="card-body">
                {{-- Progress Bar --}}
                <div class="progress mb-4">
                    <div class="progress-bar bg-primary" id="progressBar" style="width: 0%"></div>
                </div>

                <form id="assessmentForm" action="{{ route('assessment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="responses" id="responsesInput">

                    <div id="questionsContainer">
                        @foreach($questions as $index => $question)
                        <div class="question-step" data-index="{{ $index }}" data-key="{{ $question['key'] }}" 
                             style="{{ $index > 0 ? 'display: none;' : '' }}">
                            
                            <h5 class="mb-4">{{ $question['text'] }}</h5>

                            @if($question['type'] === 'select')
                                <div class="options">
                                    @foreach($question['options'] as $option)
                                    <div class="form-check mb-2 p-3 border rounded option-item" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" 
                                               name="q_{{ $question['key'] }}" 
                                               id="q_{{ $question['key'] }}_{{ $option['value'] }}"
                                               value="{{ $option['value'] }}"
                                               data-key="{{ $question['key'] }}"
                                               data-text="{{ $option['label'] }}">
                                        <label class="form-check-label w-100" for="q_{{ $question['key'] }}_{{ $option['value'] }}">
                                            {{ $option['label'] }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            @elseif($question['type'] === 'textarea')
                                <textarea class="form-control" 
                                          name="q_{{ $question['key'] }}" 
                                          data-key="{{ $question['key'] }}"
                                          rows="4" 
                                          placeholder="Please describe your experience..."></textarea>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    {{-- Video Recording Section --}}
                    <div class="question-step" data-index="{{ count($questions) }}" style="display: none;">
                        <h5 class="mb-4">Eye Scan Video (Optional)</h5>
                        <p class="text-muted">Record a 10-second video of your eyes. This helps analyze pupil reactions.</p>
                        
                        <div class="text-center mb-3">
                            <video id="videoPreview" class="rounded" width="320" height="240" autoplay muted style="display: none;"></video>
                            <canvas id="videoCanvas" width="320" height="240" style="display: none;"></canvas>
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-outline-primary me-2" id="startCameraBtn">
                                <i class="fas fa-video me-2"></i>Start Camera
                            </button>
                            <button type="button" class="btn btn-danger me-2" id="stopCameraBtn" style="display: none;">
                                <i class="fas fa-stop me-2"></i>Stop Recording
                            </button>
                        </div>
                        
                        <input type="file" name="video" id="videoInput" accept="video/*" style="display: none;">
                        <p class="text-muted small mt-2 text-center">Video is optional but helps improve analysis accuracy.</p>
                    </div>

                    {{-- Navigation Buttons --}}
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="prevBtn" disabled>
                            <i class="fas fa-arrow-left me-2"></i>Previous
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn">
                            Next<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-check me-2"></i>Submit Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const steps = document.querySelectorAll('.question-step');
    const totalSteps = steps.length;
    let currentStep = 0;
    const responses = {};

    const progressBar = document.getElementById('progressBar');
    const progressBadge = document.getElementById('progressBadge');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const responsesInput = document.getElementById('responsesInput');

    function showStep(index) {
        steps.forEach((step, i) => {
            step.style.display = i === index ? 'block' : 'none';
        });

        const progress = ((index + 1) / totalSteps) * 100;
        progressBar.style.width = progress + '%';
        progressBadge.textContent = `Step ${index + 1} of ${totalSteps}`;

        prevBtn.disabled = index === 0;
        nextBtn.style.display = index < totalSteps - 1 ? 'inline-block' : 'none';
        submitBtn.style.display = index === totalSteps - 1 ? 'inline-block' : 'none';
    }

    function saveCurrentStep() {
        const step = steps[currentStep];
        const key = step.dataset.key;

        if (key) {
            const selectedRadio = step.querySelector('input[type="radio"]:checked');
            const textarea = step.querySelector('textarea');

            if (selectedRadio) {
                responses[key] = {
                    key: key,
                    text: selectedRadio.dataset.text,
                    score: parseInt(selectedRadio.value)
                };
            } else if (textarea && textarea.value.trim()) {
                responses[key] = {
                    key: key,
                    text: textarea.value.trim(),
                    score: null
                };
            }
        }
    }

    // Option click handler
    document.querySelectorAll('.option-item').forEach(item => {
        item.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Visual feedback
            document.querySelectorAll('.option-item').forEach(i => i.classList.remove('border-primary', 'bg-light'));
            this.classList.add('border-primary', 'bg-light');
        });
    });

    nextBtn.addEventListener('click', function() {
        saveCurrentStep();
        if (currentStep < totalSteps - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', function() {
        saveCurrentStep();
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        saveCurrentStep();
        
        // Convert responses object to array
        const responsesArray = Object.values(responses);
        responsesInput.value = JSON.stringify(responsesArray);

        if (responsesArray.length === 0) {
            e.preventDefault();
            alert('Please answer at least one question before submitting.');
        }
    });

    showStep(0);
});
</script>
@endpush