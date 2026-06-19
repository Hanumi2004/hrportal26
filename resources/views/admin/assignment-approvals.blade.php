@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header d-flex justify-content-between align-items-center">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Assignment Approvals</li>
                                </ol>
                            </nav>
                            <h3 class="page-title"><br>Assignment Approvals</h3>
                            <p class="text-muted">
                                Review and approve task assignments for your department.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                @if($pendingAssignments->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox display-6 mb-2"></i>
                        <p class="mb-0">No pending assignments for your department.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Assignee</th>
                                    <th>Department</th>
                                    <th>Assigned By</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingAssignments as $assignment)
                                    <tr>
                                        <td>
                                            <strong>{{ $assignment->task->task_name }}</strong><br>
                                            <small class="text-muted">
                                                {{ \Illuminate\Support\Str::limit($assignment->task->task_desc, 60) }}
                                            </small>
                                        </td>
                                        <td>{{ $assignment->employee->full_name }}</td>
                                        <td>{{ optional($assignment->employee->employment->department)->name ?? '-' }}</td>
                                        <td>{{ optional($assignment->assignedByEmployee)->full_name ?? '-' }}</td>
                                        <td>{{ optional($assignment->created_at)->format('d M Y, h:i A') ?? '-' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('task.assignment.approve', $assignment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Approve this assignment?');">
                                                    Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('task.assignment.reject', $assignment->id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Reject this assignment?');">
                                                    Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection