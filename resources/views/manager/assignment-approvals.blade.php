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
                                    <th>Actions</th>
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
                                        <td>
                                            {{ $assignment->employee->full_name }}
                                        </td>
                                        <td>
                                            {{ optional($assignment->employee->employment->department)->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ optional($assignment->assignedByEmployee)->full_name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ optional($assignment->created_at)->format('d M Y, h:i A') ?? '-' }}
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewTaskModal{{ $assignment->id }}">
                                                <i class="bi bi-eye me-1"></i>View
                                            </button>

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

    {{-- View Task Modals --}}
    @foreach($pendingAssignments as $assignment)
        <div class="modal fade" id="viewTaskModal{{ $assignment->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $assignment->task->task_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Project</strong>
                                <p class="text-muted mb-0">{{ optional($assignment->task->project)->project_name ?? 'No Project' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Due Date</strong>
                                <p class="text-muted mb-0">{{ $assignment->task->due_date ? $assignment->task->due_date->format('d M Y') : 'No due date' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Created By</strong>
                                <p class="text-muted mb-0">{{ optional($assignment->task->createdBy)->full_name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Task Status</strong>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $assignment->task->task_status === 'completed' ? 'success' : ($assignment->task->task_status === 'in-progress' ? 'info' : 'secondary') }}">
                                        {{ ucfirst(str_replace('-', ' ', $assignment->task->task_status)) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Description</strong>
                            <p class="text-muted mb-0">{{ $assignment->task->task_desc ?? 'No description' }}</p>
                        </div>

                        @if($assignment->task->notes)
                            <div class="mb-3">
                                <strong>Notes</strong>
                                <p class="text-muted mb-0" style="white-space: pre-wrap;">{{ $assignment->task->notes }}</p>
                            </div>
                        @endif

                        <hr>
                        <h6><i class="bi bi-people me-1"></i>Assigned Employees</h6>
                        <div class="row mt-2">
                            @forelse($assignment->task->assignedTo as $emp)
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-2">
                                        <strong>{{ $emp->full_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            Dept: {{ optional($emp->employment->department)->name ?? '-' }}
                                            &middot;
                                            Status:
                                            <span class="badge bg-{{ ($emp->pivot->employee_status ?? 'pending') === 'completed' ? 'success' : (($emp->pivot->employee_status ?? 'pending') === 'in-progress' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($emp->pivot->employee_status ?? 'pending') }}
                                            </span>
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No employees assigned.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('task.assignment.approve', $assignment->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Approve this assignment?');">
                                <i class="bi bi-check-circle me-1"></i>Approve
                            </button>
                        </form>
                        <form action="{{ route('task.assignment.reject', $assignment->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Reject this assignment?');">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection