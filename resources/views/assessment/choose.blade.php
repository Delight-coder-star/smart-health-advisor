@extends('layouts.app')

@section('title', 'Choose Assessment Categories')

@section('content')

<h2 class="mb-2"><i class="fas fa-clipboard-list me-2"></i>Customize Your Assessment</h2>
<p class="text-muted mb-4">
    Choose the categories you'd like to be assessed on. You can pick one or several.
    Questions will be tailored to your choices, and you'll receive personalized advice for each area.
</p>

<form method="POST" action="{{ route('assessment.session.start') }}">
    @csrf

    <div class="row g-3 mb-4">
        @foreach($categories as $cat)
        <div class="col-md-6 col-lg-4">
            <label class="category-card" for="cat_{{ $cat['key'] }}">
                <input
                    type="checkbox"
                    name="categories[]"
                    value="{{ $cat['key'] }}"
                    id="cat_{{ $cat['key'] }}"
                    class="category-checkbox"
                >
                <div class="category-icon">
                    <i class="{{ $cat['icon'] }}"></i>
                </div>
                <div class="category-body">
                    <h6 class="mb-1">{{ $cat['label'] }}</h6>
                    <p class="text-muted small mb-1">{{ $cat['description'] }}</p>
                    <span class="badge bg-light text-dark">{{ $cat['question_count'] }} questions</span>
                </div>
                <i class="fas fa-check-circle check-mark"></i>
            </label>
        </div>
        @endforeach
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-3">Assessment Length</h6>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="mode-option">
                        <input type="radio" name="mode" value="quick">
                        <div>
                            <strong>Quick</strong>
                            <small class="d-block text-muted">~8 questions</small>
                        </div>
                    </label>
                </div>
                <div class="col-md-3">
                    <label class="mode-option">
                        <input type="radio" name="mode" value="adaptive" checked>
                        <div>
                            <strong>Adaptive</strong>
                            <small class="d-block text-muted">~20 questions</small>
                        </div>
                    </label>
                </div>
                <div class="col-md-3">
                    <label class="mode-option">
                        <input type="radio" name="mode" value="targeted">
                        <div>
                            <strong>Targeted</strong>
                            <small class="d-block text-muted">~15 questions</small>
                        </div>
                    </label>
                </div>
                <div class="col-md-3">
                    <label class="mode-option">
                        <input type="radio" name="mode" value="comprehensive">
                        <div>
                            <strong>Comprehensive</strong>
                            <small class="d-block text-muted">All questions</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    @error('categories')
        <div class="alert alert-danger">Please select at least one category.</div>
    @enderror

    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Cancel
        </a>
        <button type="submit" class="btn btn-primary btn-lg" id="startBtn" disabled>
            <i class="fas fa-play me-2"></i>Start Assessment
        </button>
    </div>
</form>

@endsection

@push('styles')
<style>
    .category-card {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        height: 100%;
    }
    .category-card:hover {
        border-color: #a5b4fc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79,70,229,0.1);
    }
    .category-card.selected {
        border-color: #4f46e5;
        background: #eef2ff;
    }
    .category-checkbox {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .category-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .category-body {
        flex: 1;
        min-width: 0;
    }
    .check-mark {
        color: #10b981;
        font-size: 1.5rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .category-card.selected .check-mark {
        opacity: 1;
    }
    .mode-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .mode-option:hover {
        border-color: #a5b4fc;
    }
    .mode-option input {
        accent-color: #4f46e5;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    const startBtn = document.getElementById('startBtn');

    function updateUI() {
        let count = 0;
        checkboxes.forEach(cb => {
            const card = cb.closest('.category-card');
            if (cb.checked) {
                card.classList.add('selected');
                count++;
            } else {
                card.classList.remove('selected');
            }
        });
        startBtn.disabled = count === 0;
        startBtn.innerHTML = count > 0
            ? '<i class="fas fa-play me-2"></i>Start Assessment (' + count + ' ' + (count === 1 ? 'category' : 'categories') + ')'
            : '<i class="fas fa-play me-2"></i>Start Assessment';
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateUI);
    });

    updateUI();
});
</script>
@endpush