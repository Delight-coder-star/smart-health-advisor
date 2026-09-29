{{-- Reusable question partial --}}
<div class="question-step" data-index="{{ $index }}" data-key="{{ $question['key'] }}"
     style="{{ $index > 0 ? 'display: none;' : '' }}">
    <h5 class="mb-4">{{ $question['text'] }}</h5>

    @if(($question['type'] ?? 'select') === 'select')
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
    @elseif(($question['type'] ?? 'select') === 'textarea')
        <textarea class="form-control"
                  name="q_{{ $question['key'] }}"
                  data-key="{{ $question['key'] }}"
                  rows="4"
                  placeholder="Please describe your experience..."></textarea>
    @endif
</div>