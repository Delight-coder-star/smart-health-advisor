@extends('layouts.app')

@section('title', 'Medications')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">
        <i class="fas fa-pills me-2"></i>My Medications
    </h2>
    <a href="{{ route('medications.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Medication
    </a>
</div>

@if($medications->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-pills fa-3x text-muted mb-3"></i>
            <h5>No medications yet</h5>
            <p class="text-muted">Add your medications to get daily reminders.</p>
            <a href="{{ route('medications.create') }}" class="btn btn-primary">
                Add your first medication
            </a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($medications as $med)
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-capsules me-2"></i>{{ $med->name }}</span>
                    <span class="badge bg-{{ $med->is_active ? 'success' : 'secondary' }}">
                        {{ $med->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Dosage:</strong> {{ $med->dosage ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Frequency:</strong> {{ $med->frequency ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Condition:</strong> {{ $med->condition ?? 'General' }}</p>

                    @if($med->instructions)
                        <p class="mb-1"><strong>Instructions:</strong> {{ $med->instructions }}</p>
                    @endif

                    @if($med->reminders->count() > 0)
                        <p class="mb-2 mt-2">
                            <strong>Reminders:</strong>
                            @foreach($med->reminders as $r)
                                <span class="badge bg-info">{{ substr($r->reminder_time, 0, 5) }}</span>
                            @endforeach
                        </p>
                    @endif

                    <div class="d-flex gap-2 mt-3">
                        <form method="POST" action="{{ route('medications.log', $med) }}">
                            @csrf
                            <input type="hidden" name="status" value="taken">
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check me-1"></i>Taken
                            </button>
                        </form>

                        <form method="POST" action="{{ route('medications.log', $med) }}">
                            @csrf
                            <input type="hidden" name="status" value="missed">
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times me-1"></i>Missed
                            </button>
                        </form>

                        <form method="POST" action="{{ route('medications.destroy', $med) }}"
                              onsubmit="return confirm('Delete this medication?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
				<div id="notificationBanner" class="alert alert-info d-none">
    <div class="d-flex align-items-center">
        <i class="fas fa-bell fa-2x me-3"></i>
        <div class="flex-grow-1">
            <strong>Enable browser notifications</strong>
            <div class="small">
                Get reminded about your medications while the site is open.
            </div>
        </div>
        <button id="enableNotifications" class="btn btn-primary btn-sm">
            Enable
        </button>
    </div>
</div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Store the user's medication reminders
   var reminders = @json($browserReminders);

    // Show banner if notifications aren't enabled
    var banner = document.getElementById('notificationBanner');
    if ('Notification' in window && Notification.permission === 'default' && reminders.length > 0) {
        banner.classList.remove('d-none');
    }

    // Enable button
    var enableBtn = document.getElementById('enableNotifications');
    if (enableBtn) {
        enableBtn.addEventListener('click', function () {
            Notification.requestPermission().then(function (perm) {
                if (perm === 'granted') {
                    banner.classList.add('d-none');
                    new Notification('Notifications enabled', {
                        body: 'You will be reminded about your medications.',
                        icon: '{{ asset("images/logo.png") }}'
                    });
                }
            });
        });
    }

    // Check every 30 seconds for due reminders
    if ('Notification' in window && Notification.permission === 'granted') {
        var notified = {};

        setInterval(function () {
            var now = new Date();
            var hh = String(now.getHours()).padStart(2, '0');
            var mm = String(now.getMinutes()).padStart(2, '0');
            var current = hh + ':' + mm;

            reminders.forEach(function (r) {
                if (r.time === current && !notified[r.time + '|' + r.name]) {
                    notified[r.time + '|' + r.name] = true;
                    new Notification('Time for ' + r.name, {
                        body: r.dosage ? 'Take ' + r.dosage : 'Take your medication',
                        icon: '{{ asset("images/logo.png") }}',
                        tag: 'med-' + r.name
                    });
                }
            });
        }, 30000);
    }
});
</script>
@endpush