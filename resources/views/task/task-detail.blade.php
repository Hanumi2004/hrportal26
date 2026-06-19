@php use Illuminate\Support\Facades\Storage; @endphp
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
                                <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('task.index.employee') }}">Tasks</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Task Detail</li>
                            </ol>
                        </nav>
                        <h3 class="page-title mt-2">{{ $task->task_name }}</h3>
                    </div>
                    <div class="d-flex gap-2">
                        @if($isCreator || in_array($role_id, [1, 2]))
                            <a href="{{ route('task.edit', $task->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-1"></i>Edit Task
                            </a>
                        @endif
                        <a href="{{ route('task.index.' . (in_array($role_id, [1, 2]) ? 'admin' : 'employee')) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Task Information</h5>
                    <span class="badge fs-6 px-3 py-1
                        @if($task->task_status === 'completed') bg-success
                        @elseif($task->task_status === 'in-progress') bg-info
                        @elseif(in_array($task->task_status, ['in-review', 'to-review'])) bg-primary
                        @else bg-secondary @endif">
                        {{ ucfirst(str_replace('-', ' ', $task->task_status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Project</strong>
                            <p class="text-muted mb-0">{{ optional($task->project)->project_name ?? 'No Project' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Due Date</strong>
                            <p class="text-muted mb-0">{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Created By</strong>
                            <p class="text-muted mb-0">{{ optional($task->createdBy)->full_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Created At</strong>
                            <p class="text-muted mb-0">{{ $task->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description</strong>
                        <p class="text-muted mb-0">{{ $task->task_desc ?? 'No description' }}</p>
                    </div>

                    @if($task->notes)
                        <div class="mb-3">
                            <strong>Notes</strong>
                            <p class="text-muted mb-0" style="white-space: pre-wrap;">{{ $task->notes }}</p>
                        </div>
                    @endif

                    @php $taskAttachments = is_array($task->attachments) ? $task->attachments : (json_decode($task->attachments, true) ?? []); @endphp
                    @if(!empty($taskAttachments))
                        <div class="mb-3">
                            <strong>Attachments</strong>
                            <div class="mt-1">
                                @foreach($taskAttachments as $path)
                                    <a href="{{ Storage::url($path) }}" target="_blank"
                                       class="btn btn-sm btn-outline-secondary me-1 mb-1">
                                        <i class="bi bi-paperclip"></i> View File
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assigned Employees & Progress --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Assigned Employees</h5>
                </div>
                <div class="card-body">
                    @php
                        $assignments = $task->assignments;
                    @endphp

                    @if($assignments->isEmpty())
                        <p class="text-muted mb-0">No employees assigned yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assign)
                                        @php
                                            $empStatus = $assign->employee_status ?? 'pending';
                                            $progressValue = match($empStatus) {
                                                'completed' => 100,
                                                'in-progress' => 50,
                                                default => 0,
                                            };
                                            $badgeColor = match($empStatus) {
                                                'completed' => 'success',
                                                'in-progress' => 'info',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $assign->employee->full_name ?? '-' }}</td>
                                            <td>{{ optional($assign->employee->employment->department)->name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $badgeColor }}">
                                                    {{ ucfirst(str_replace('-', ' ', $empStatus)) }}
                                                </span>
                                            </td>
                                            <td style="min-width: 120px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $badgeColor }}" style="width: {{ $progressValue }}%"></div>
                                                    </div>
                                                    <small>{{ $progressValue }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $assign->progress_updated_at ? $assign->progress_updated_at->format('d M Y, h:i A') : '-' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Progress History --}}
            @if($task->progressLogs->count())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Progress History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Employee</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($task->progressLogs as $log)
                                        @php
                                            $logColor = $log->employee_status === 'completed'
                                                ? 'success'
                                                : ($log->employee_status === 'in-progress' ? 'info' : 'secondary');
                                        @endphp
                                        <tr>
                                            <td><small>{{ optional($log->progress_updated_at)->format('d M Y, h:i A') }}</small></td>
                                            <td><small>{{ $log->employee->full_name ?? '-' }}</small></td>
                                            <td>
                                                <span class="badge bg-{{ $logColor }}">
                                                    {{ ucfirst(str_replace('-', ' ', $log->employee_status)) }}
                                                </span>
                                            </td>
                                            <td><small>{{ $log->employee_remarks ?? '-' }}</small></td>
                                            <td>
                                                @php $paths = is_array($log->attachment_path) ? $log->attachment_path : (json_decode($log->attachment_path, true) ?? []); @endphp
                                                @if(!empty($paths))
                                                    @foreach($paths as $idx => $path)
                                                        <a href="{{ Storage::url($path) }}" target="_blank"
                                                           class="btn btn-sm btn-outline-primary me-1">
                                                            <i class="bi bi-paperclip"></i> File {{ $idx + 1 }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Column: My Progress (if assignee) --}}
        <div class="col-lg-4">
            @if($myAssignment)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">My Progress</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $currentStatus = $myAssignment->employee_status ?? 'pending';
                            $isLocked = $currentStatus === 'completed';

                            $dueDateEnd = $task->due_date ? \Carbon\Carbon::parse($task->due_date)->endOfDay() : null;
                            $updatedAt = $myAssignment->progress_updated_at;

                            if ($task->due_date) {
                                if ($currentStatus === 'completed' && $updatedAt) {
                                    $kpiStatus = $updatedAt->lte($dueDateEnd) ? 'within_time' : 'late_submission';
                                } elseif ($currentStatus !== 'completed' && now()->gt($dueDateEnd)) {
                                    $kpiStatus = 'not_submitted';
                                } else {
                                    $kpiStatus = 'pending';
                                }
                            } else {
                                $kpiStatus = 'no_due_date';
                            }

                            $kpiBadge = match($kpiStatus) {
                                'within_time' => 'success',
                                'late_submission' => 'warning',
                                'not_submitted' => 'danger',
                                'pending' => 'secondary',
                                default => 'dark',
                            };
                            $kpiLabel = match($kpiStatus) {
                                'within_time' => 'Within Time',
                                'late_submission' => 'Late Submission',
                                'not_submitted' => 'Not Submitted',
                                'pending' => 'Pending',
                                default => 'No Due Date',
                            };
                            $progressValue = $currentStatus === 'completed' ? 100 : 0;
                        @endphp

                        <div class="alert alert-info py-2 px-3">
                            <div class="d-flex justify-content-between">
                                <strong>KPI Status</strong>
                                <span class="badge bg-{{ $kpiBadge }}">{{ $kpiLabel }}</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-{{ $kpiBadge }}" style="width: {{ $progressValue }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>Current Status</strong>
                            <p class="mb-0">
                                <span class="badge bg-{{ $currentStatus === 'completed' ? 'success' : ($currentStatus === 'in-progress' ? 'info' : 'secondary') }} fs-6">
                                    {{ ucfirst(str_replace('-', ' ', $currentStatus)) }}
                                </span>
                            </p>
                        </div>

                        @if($myAssignment->employee_remarks)
                            <div class="mb-3">
                                <strong>Latest Remarks</strong>
                                <p class="text-muted mb-0">{{ $myAssignment->employee_remarks }}</p>
                            </div>
                        @endif

                        @if($isLocked)
                            <div class="alert alert-warning">
                                <i class="bi bi-lock-fill me-1"></i>
                                This task has been completed and locked.
                                @if(in_array($role_id, [1, 2]))
                                    <form action="{{ route('task.reopen.assignment', [$task->id, $employee->employee_id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning mt-2">Reopen Task</button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <hr>
                            <form action="{{ route('task.progress', $task->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="employee_status" class="form-select" required>
                                        <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in-progress" {{ $currentStatus === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $currentStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Remarks <span class="text-danger">*</span></label>
                                    <textarea name="employee_remarks" class="form-control" rows="3" required placeholder="Describe your progress...">{{ $myAssignment->employee_remarks }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Attachments</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save me-1"></i>Save Progress
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
