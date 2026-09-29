@extends('layouts.app')

@section('title', 'Students')

@section('content')
<h2 class="mb-4">Students</h2>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Student ID</th><th>Risk</th><th>Last Assessment</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->student_id ?? '-' }}</td>
                    <td><span class="risk-badge risk-{{ $student->risk_level }}">{{ ucfirst($student->risk_level) }}</span></td>
                    <td>{{ $student->last_assessment_at ? $student->last_assessment_at->diffForHumans() : 'Never' }}</td>
                    <td><a href="{{ route('advisor.student.detail', $student) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $students->links() }}</div>
@endsection