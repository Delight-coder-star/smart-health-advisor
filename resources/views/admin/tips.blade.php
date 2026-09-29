@extends('layouts.app')

@section('title', 'Health Tips')

@section('content')
<h2 class="mb-4">Health Tips</h2>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Title</th><th>Category</th><th>Priority</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($tips as $tip)
                <tr>
                    <td>{{ $tip->title }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($tip->category) }}</span></td>
                    <td>{{ $tip->priority }}</td>
                    <td>
                        @if($tip->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No health tips.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $tips->links() }}</div>
@endsection