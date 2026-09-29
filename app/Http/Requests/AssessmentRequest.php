<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'responses' => ['required', 'array'],
            'responses.*.key' => ['required', 'string'],
            'responses.*.text' => ['nullable', 'string', 'max:5000'],
            'responses.*.score' => ['nullable', 'integer', 'min:1', 'max:5'],
            'video' => ['nullable', 'file', 'mimes:mp4,webm,mov', 'max:51200'],
        ];
    }
}