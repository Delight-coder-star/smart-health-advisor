@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<h2 class="mb-4">Manage Users</h2>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Risk</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @if($user->isStudent())
                            <span class="risk-badge risk-{{ $user->risk_level }}">{{ ucfirst($user->risk_level) }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $users->links() }}</div>
@endsection