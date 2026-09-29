@extends('layouts.app')

@section('title', 'Add Medication')

@section('content')

<h2 class="mb-3">
    <i class="fas fa-plus me-2"></i>Add Medication
</h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('medications.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Medication Name *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Dosage</label>
                    <input type="text" name="dosage" class="form-control" placeholder="e.g. 500mg" value="{{ old('dosage') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Frequency</label>
                    <input type="text" name="frequency" class="form-control" placeholder="e.g. Twice daily" value="{{ old('frequency') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Condition</label>
                    <input type="text" name="condition" class="form-control" placeholder="e.g. Hypertension" value="{{ old('condition') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->toDateString()) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Instructions</label>
                    <textarea name="instructions" class="form-control" rows="2">{{ old('instructions') }}</textarea>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Reminder Times (up to 4)</label>
                    <div class="row g-2">
                        @for($i = 0; $i < 4; $i++)
                            <div class="col-md-3">
                                <input type="time" name="reminder_times[]" class="form-control">
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Medication
            </button>
            <a href="{{ route('medications.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>

@endsection