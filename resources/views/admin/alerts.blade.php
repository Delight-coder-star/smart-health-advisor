@extends('layouts.app')

@section('title', 'Alerts')

@section('content')
<h2 class="mb-4">Alerts</h2>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Student</th><th>Title</th><th>Severity</th><th>Status</th><th>Created</th></tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr>
                    <td>{{ $alert->user->name ?? 'N/A' }}</td>
                    <td>{{ $alert->title }}</td>
                    <td><span class="badge bg-{{ $alert->severity_color }}">{{ ucfirst($alert->severity) }}</span></td>
                    <td><span class="badge bg-secondary">{{ ucfirst($alert->status) }}</span></td>
                    <td>{{ $alert->created_at ? $alert->created_at->format('M d, Y H:i') : '' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No alerts.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $alerts->links() }}</div>
@endsection