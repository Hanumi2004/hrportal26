@php
    use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.master')

@section('content')

		@php 

			$user = Auth::user(); $employee = $user->employee ?? null; 

		@endphp

<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 w-100">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Task</li>
                                </ol>
                            </nav>
                            <h3 class="page-title mt-2">My Tasks</h3>
                            <p class="text-muted">Track your assigned tasks and progress.</p>
                        </div>

                        @php
							$user     = Auth::user();
							$employee = $user->employee ?? null;
						@endphp

						<div class="d-flex flex-wrap gap-3 w-100 w-lg-auto justify-content-lg-end">
							<div class="btn-group" role="group">
								<button class="btn btn-outline-primary active disabled">Task</button>
								<button class="btn btn-outline-primary" onclick="window.location='{{ route('project.index.employee') }}'">
									Project
								</button>
							</div>

							{{-- New Task: hide untuk President (7) & Others (6) --}}
							@if(!in_array($user->role_id, [6, 7], true))
								<button class="btn btn-new" onclick="window.location='{{ route('task.create') }}'">
									New Task
								</button>
							@endif
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success / error alert intentionally removed here to avoid duplicate flash messages.
         Assume layouts.master already renders flash alerts. --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('task.index.employee') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <label class="form-label">Search</label>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Task name...">
                    </div>

                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-control">
                            <option value="">All Projects</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                        {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-1">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
					<div class="col-12 col-sm-6 col-lg-2">
						<div class="form-check mt-4">
							<input class="form-check-input" type="checkbox" name="created_by_me"
								   value="1" id="createdByMe"
								{{ request('created_by_me') ? 'checked' : '' }}>
							<label class="form-check-label" for="createdByMe">
								Created By Me
							</label>
						</div>
					</div>


                    <div class="col-12 col-sm-6 col-lg-1">
                        <a href="{{ route('task.index.employee') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            @php
                $progressSummaryValue = $myProgress ?? 0;
                $progressSummaryColor = $progressSummaryValue == 100
                    ? 'success'
                    : ($progressSummaryValue >= 75
                        ? 'info'
                        : ($progressSummaryValue >= 50 ? 'warning' : 'danger'));
            @endphp

            <div class="card border-{{ $progressSummaryColor }}" style="border-left: 5px solid;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="mb-0 text-dark">
                                <i class="bi bi-speedometer2 text-{{ $progressSummaryColor }} me-2"></i>My Progress
                            </h5>
                            <small class="text-muted">Average progress for my assigned tasks</small>
                        </div>

                        <div class="text-end">
                            <div class="fs-1 fw-bold text-{{ $progressSummaryColor }}">
                                {{ $progressSummaryValue }}%
                            </div>
                            <small class="text-muted">
                                {{ $myCompletedTasks ?? 0 }} / {{ $myTotalTasks ?? 0 }} tasks completed
                            </small>
                        </div>
                    </div>

                    <div class="progress mt-3" style="height: 20px; background: #e9ecef;">
                        <div class="progress-bar bg-{{ $progressSummaryColor }}"
                             role="progressbar"
                             style="width: {{ $progressSummaryValue }}%;"
                             aria-valuenow="{{ $progressSummaryValue }}"
                             aria-valuemin="0"
                             aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-2 mb-3">
            <div class="card filter-card active" data-status="all">
                <div class="card-body text-center">
                    <i class="bi bi-list-task fs-4 text-primary"></i>
                    <div class="fs-4 fw-bold">{{ $myTotalTasks ?? 0 }}</div>
                    <small class="text-muted">My Total</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-2 mb-3">
            <div class="card filter-card" data-status="pending">
                <div class="card-body text-center">
                    <i class="bi bi-circle fs-4 text-secondary"></i>
                    <div class="fs-4 fw-bold">
                        {{ ($myTotalTasks ?? 0) - ($myCompletedTasks ?? 0) - ($myInProgressTasks ?? 0) }}
                    </div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-2 mb-3">
            <div class="card filter-card" data-status="in-progress">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-repeat fs-4 text-info"></i>
                    <div class="fs-4 fw-bold">{{ $myInProgressTasks ?? 0 }}</div>
                    <small class="text-muted">In Progress</small>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-2 mb-3">
            <div class="card filter-card" data-status="completed">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-4 text-success"></i>
                    <div class="fs-4 fw-bold">{{ $myCompletedTasks ?? 0 }}</div>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Grid -->
    <div id="tasksCard">
        <div class="row">
			@php
				$user = Auth::user();  // ← Define user kat sini
				$employee = $user->employee ?? null;
			@endphp
            @forelse($tasks as $task)
                @php
                    $myStatus = $myTaskStatuses[$task->id] ?? null;
                    $empStatus = $myStatus['status'] ?? 'pending';

                    $statusMap = [
                        'completed' => ['color' => 'success', 'label' => 'Completed'],
                        'in-progress' => ['color' => 'info', 'label' => 'In Progress'],
                        'pending' => ['color' => 'secondary', 'label' => 'Pending'],
                    ];

                    $statusInfo = $statusMap[$empStatus] ?? [
                        'color' => 'secondary',
                        'label' => ucfirst(str_replace('-', ' ', $empStatus)),
                    ];

                    $progressValue = $empStatus === 'completed' ? 100 : 0;

                    $updatedAt = !empty($myStatus['updated_at'])
                        ? \Carbon\Carbon::parse($myStatus['updated_at'])
                        : null;

                    $dueDateEnd = $task->due_date
                        ? \Carbon\Carbon::parse($task->due_date)->endOfDay()
                        : null;

                    if ($task->due_date) {
                        if ($empStatus === 'completed' && $updatedAt) {
                            $submissionKpi = $updatedAt->lte($dueDateEnd)
                                ? 'within_time'
                                : 'late_submission';
                        } elseif ($empStatus !== 'completed' && now()->gt($dueDateEnd)) {
                            $submissionKpi = 'not_submitted';
                        } else {
                            $submissionKpi = 'pending';
                        }
                    } else {
                        $submissionKpi = 'no_due_date';
                    }

                    $kpiColor = match($submissionKpi) {
                        'within_time' => 'success',
                        'late_submission' => 'warning',
                        'not_submitted' => 'danger',
                        'pending' => 'secondary',
                        default => 'dark',
                    };

                    $kpiLabel = match($submissionKpi) {
                        'within_time' => 'Within Time',
                        'late_submission' => 'Late Submission',
                        'not_submitted' => 'Not Submitted',
                        'pending' => 'Pending',
                        default => 'No Due Date',
                    };
                @endphp

                <div class="col-12 col-sm-6 col-xl-4 mb-3 task-item" data-status="{{ $empStatus }}">
                    <div class="card h-100 shadow-sm task-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <h5 class="card-title mb-0">{{ $task->task_name }}</h5>
                                <span class="badge bg-{{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</span>
                            </div>

                            <p class="text-muted small mb-3">
                                {{ \Illuminate\Support\Str::limit($task->task_desc, 80) }}
                            </p>

                            <div class="mb-2 small text-muted">
                                <i class="bi bi-folder me-1"></i>
                                {{ optional($task->project)->project_name ?? 'No Project' }}
                            </div>

                            <div class="mt-2">
                                <span class="badge bg-{{ $kpiColor }}">{{ $kpiLabel }}</span>
                            </div>

                            <div class="mb-2 mt-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>My Progress</span>
                                    <span class="small fw-semibold">
                                        {{ $progressValue }}%
                                        <span class="ms-1 text-muted">{{ $statusInfo['label'] }}</span>
                                    </span>
                                </div>

                                <div class="progress mt-2" style="height: 10px; background: #e9ecef;">
                                    <div class="progress-bar bg-{{ $statusInfo['color'] }}"
                                         role="progressbar"
                                         style="width: {{ $progressValue }}%;"
                                         aria-valuenow="{{ $progressValue }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>

                                @if(!empty($myStatus['updated_at']))
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-clock me-1"></i>
                                        Updated: {{ \Carbon\Carbon::parse($myStatus['updated_at'])->format('d M Y H:i') }}
                                    </div>
                                @endif

                                @if(!empty($myStatus['remarks']))
                                    <div class="small mt-1">
                                        <i class="bi bi-chat-left-text me-1"></i>
                                        {{ \Illuminate\Support\Str::limit($myStatus['remarks'], 60) }}
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}
                                </small>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="task-actions">
                                <a href="{{ route('task.detail', $task->id) }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
								
								 @php
									$isCreator = $task->created_by === ($employee->employee_id ?? null);
								@endphp

								{{-- Edit Button (show if creator OR admin) --}}
								@if($isCreator || in_array($user->role_id, [1, 2]))
									<button class="btn btn-sm btn-primary"
											data-bs-toggle="modal"
											data-bs-target="#taskModal{{ $task->id }}">
										<i class="bi bi-pencil me-1"></i>Edit
									</button>
								@endif

                                @if(isset($myTaskStatuses[$task->id]))
                                <button type="button"
                                        class="btn btn-sm {{ $empStatus === 'completed' ? 'btn-success' : 'btn-primary' }}"
                                        @if($empStatus !== 'completed')
                                            data-bs-toggle="modal"
                                            data-bs-target="#taskProgressModal{{ $task->id }}"
                                        @endif
                                        @disabled($empStatus === 'completed')
                                        aria-disabled="{{ $empStatus === 'completed' ? 'true' : 'false' }}"
                                        title="{{ $empStatus === 'completed' ? 'Task already completed' : 'Update task progress' }}">
                                    <i class="bi {{ $empStatus === 'completed' ? 'bi-check-circle' : 'bi-pencil' }} me-1"></i>
                                    {{ $empStatus === 'completed' ? 'Completed' : 'Update Progress' }}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 text-muted no-tasks-static">
                        <i class="bi bi-inbox display-1"></i>
                        <h5 class="mt-3">No tasks found</h5>
                        <p>You have no assigned tasks</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Edit Modal (Creator/Admin only) --}}
    @foreach($tasks as $task)
        @php
            $taskAssignments = $task->assignedTo;
            $taskTotal = $taskAssignments->count();
            $taskCompleted = $taskAssignments->filter(function($emp) {
                return $emp->pivot && $emp->pivot->employee_status === 'completed';
            })->count();
            $taskPercentage = $taskTotal > 0 ? round(($taskCompleted / $taskTotal) * 100) : 0;
        @endphp

        <div class="modal fade task-modal" id="taskModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task: {{ $task->task_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('task.update', $task->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        <div class="modal-body">
                            @if($taskTotal > 0)
                                <div class="alert alert-info mb-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>Team Progress</strong>
                                        <span class="badge bg-primary">{{ $taskPercentage }}%</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $taskPercentage }}%"></div>
                                    </div>
                                    <small>{{ $taskCompleted }} / {{ $taskTotal }} completed</small>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Task Name</label>
                                        <input type="text" name="task_name" class="form-control" value="{{ $task->task_name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Project</label>
                                        <select name="project_id" class="form-select">
                                            <option value="">No Project</option>
                                            @foreach($projects as $proj)
                                                <option value="{{ $proj->id }}" {{ $task->project_id == $proj->id ? 'selected' : '' }}>{{ $proj->project_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="task_status" class="form-select">
                                            <option value="to-do" {{ $task->task_status === 'to-do' ? 'selected' : '' }}>To-Do</option>
                                            <option value="in-progress" {{ $task->task_status === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="in-review" {{ $task->task_status === 'in-review' ? 'selected' : '' }}>In Review</option>
                                            <option value="to-review" {{ $task->task_status === 'to-review' ? 'selected' : '' }}>To Review</option>
                                            <option value="completed" {{ $task->task_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" name="due_date" class="form-control" value="{{ $task->due_date?->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="task_desc" class="form-control" rows="2">{{ $task->task_desc }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2">{{ $task->notes }}</textarea>
                            </div>

                            <hr>
                            <h6><i class="bi bi-people me-1"></i>Assign Employees</h6>
                            <div class="row mt-2">
                                <div class="col-12 mb-2">
                                    <select class="form-select employee-add-select" data-task-id="{{ $task->id }}">
                                        <option value="">-- Search & Add Employee --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->employee_id }}"
                                                    data-name="{{ $emp->full_name }}"
                                                    data-dept="{{ optional($emp->employment)->department->name ?? '' }}">
                                                {{ $emp->full_name }}
                                                @if(optional($emp->employment)->department)
                                                    - {{ $emp->employment->department->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div id="taskAssignees{{ $task->id }}" class="border rounded p-2 bg-light" style="min-height: 60px;">
                                        @forelse($task->assignedTo as $member)
                                            <div class="d-flex justify-content-between align-items-center border-bottom py-1 assignee-row">
                                                <div>
                                                    <strong>{{ $member->full_name }}</strong>
                                                    @if(optional($member->employment)->department)
                                                        <small class="text-muted">({{ $member->employment->department->name }})</small>
                                                    @endif
                                                    <span class="badge bg-{{
                                                        ($member->pivot->employee_status ?? 'pending') === 'completed' ? 'success' :
                                                        (($member->pivot->employee_status ?? 'pending') === 'in-progress' ? 'warning' : 'secondary')
                                                    }} ms-1">
                                                        {{ ucfirst($member->pivot->employee_status ?? 'pending') }}
                                                    </span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-assignee"
                                                        data-employee-id="{{ $member->employee_id }}"
                                                        data-task-id="{{ $task->id }}">×</button>
                                                <input type="hidden" name="employee_ids[]" value="{{ $member->employee_id }}">
                                            </div>
                                        @empty
                                            <small class="text-muted">No employees assigned</small>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h6><i class="bi bi-pencil-square me-1"></i>Creator Actions</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Extend Deadline</label>
                                        <input type="date" name="new_due_date" class="form-control"
                                               min="{{ $task->due_date?->format('Y-m-d') ?? '' }}">
                                        <small class="text-muted">Current: {{ $task->due_date?->format('d M Y') ?? 'No due date' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Add Remarks (Creator)</label>
                                        <textarea name="creator_remarks" class="form-control" rows="2" placeholder="Add remarks about this task..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h6><i class="bi bi-paperclip me-1"></i>Task Attachments</h6>
                            @php $taskAttachments = is_array($task->attachments) ? $task->attachments : (json_decode($task->attachments, true) ?? []); @endphp
                            @if(!empty($taskAttachments))
                                <div class="mb-2">
                                    @foreach($taskAttachments as $path)
                                        <a href="{{ Storage::url($path) }}" target="_blank"
                                           class="btn btn-sm btn-outline-secondary me-1 mb-1">
                                            <i class="bi bi-paperclip"></i> View File
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label">Upload New Attachments</label>
                                <input type="file" name="task_attachments[]" class="form-control" multiple>
                                <small class="text-muted">Add reference documents for this task</small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Update Progress Modal (Staff B / Assignee) --}}
    @foreach($tasks as $task)
        @php
            $user = Auth::user();
            $currentTaskStatus = $myTaskStatuses[$task->id]['status'] ?? 'pending';
            $currentTaskProgress = $currentTaskStatus === 'completed' ? 100 : 0;
            $currentTaskRemarks = $myTaskStatuses[$task->id]['remarks'] ?? '';
            $progressLogs = $task->progressLogs->sortByDesc('progress_updated_at');

            $currentUpdatedAt = !empty($myTaskStatuses[$task->id]['updated_at'])
                ? \Carbon\Carbon::parse($myTaskStatuses[$task->id]['updated_at'])
                : null;

            $isLocked = $currentTaskStatus === 'completed';

            $dueDateEnd = $task->due_date
                ? \Carbon\Carbon::parse($task->due_date)->endOfDay()
                : null;

            if ($task->due_date) {
                if ($currentTaskStatus === 'completed' && $currentUpdatedAt) {
                    $submissionKpi = $currentUpdatedAt->lte($dueDateEnd)
                        ? 'within_time'
                        : 'late_submission';
                } elseif ($currentTaskStatus !== 'completed' && now()->gt($dueDateEnd)) {
                    $submissionKpi = 'not_submitted';
                } else {
                    $submissionKpi = 'pending';
                }
            } else {
                $submissionKpi = 'no_due_date';
            }

            $badgeClass = match($submissionKpi) {
                'within_time' => 'success',
                'late_submission' => 'warning',
                'not_submitted' => 'danger',
                'pending' => 'secondary',
                default => 'dark',
            };

            $badgeLabel = match($submissionKpi) {
                'within_time' => 'Within Time',
                'late_submission' => 'Late Submission',
                'not_submitted' => 'Not Submitted',
                'pending' => 'Pending',
                default => 'No Due Date',
            };

            $kpiProgress = $currentTaskStatus === 'completed' ? 100 : 0;
        @endphp

        <div class="modal fade task-modal" id="taskProgressModal{{ $task->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Progress: {{ $task->task_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('task.progress', $task->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">
                            <div class="alert alert-info mb-3">
                                <div class="d-flex justify-content-between flex-wrap gap-2">
                                    <strong>Task KPI Submission Status</strong>
                                    <span class="badge bg-{{ $badgeClass }}">{{ $badgeLabel }}</span>
                                </div>
                                <div class="progress mt-2 task-kpi-progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $badgeClass }}"
                                         role="progressbar"
                                         style="width: {{ $kpiProgress }}%;"
                                         aria-valuenow="{{ $kpiProgress }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                                <div class="mt-2 small">
                                    <div><strong>My Progress:</strong> {{ $currentTaskProgress }}%</div>
                                    <div><strong>Status:</strong> {{ ucfirst(str_replace('-', ' ', $currentTaskStatus)) }}</div>
                                    <div><strong>Due Date:</strong> {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</div>
                                    @if($currentUpdatedAt)
                                        <div><strong>Last Updated:</strong> {{ $currentUpdatedAt->format('d M Y h:i A') }}</div>
                                    @endif
                                </div>
                            </div>

                            @if($isLocked)
                                <div class="alert alert-warning">
                                    <i class="bi bi-lock-fill me-1"></i>
                                    This task has been completed and locked. Please contact admin if you need this task reopened.
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <strong>Project</strong>
                                    <div>{{ optional($task->project)->project_name ?? 'No Project' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Due Date</strong>
                                    <div>{{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Description</strong>
                                <p class="mb-0 text-muted">{{ $task->task_desc ?? 'No description' }}</p>
                            </div>

                            @if($task->notes)
                                <div class="mb-3">
                                    <strong>Notes</strong>
                                    <p class="mb-0 text-muted">{{ $task->notes }}</p>
                                </div>
                            @endif

                            <hr>
                            <h6><i class="bi bi-pencil-square me-1"></i>Update My Progress</h6>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">My Status <span class="text-danger">*</span></label>
                                        <select name="employee_status"
                                                class="form-select"
                                                required
                                                {{ $isLocked ? 'disabled' : '' }}>
                                            <option value="">-- Select Status --</option>
                                            <option value="pending" {{ $currentTaskStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in-progress" {{ $currentTaskStatus == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $currentTaskStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        @if($isLocked)
                                            <input type="hidden" name="employee_status" value="{{ $currentTaskStatus }}">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Remarks / Notes <span class="text-danger">*</span></label>
                                <textarea name="employee_remarks"
                                          class="form-control"
                                          rows="4"
                                          placeholder="Please provide your progress remarks..."
                                          required
                                          {{ $isLocked ? 'readonly' : '' }}>{{ old('employee_remarks', $currentTaskRemarks) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Attachments</label>
                                <input type="file"
                                       name="attachments[]"
                                       class="form-control"
                                       multiple
                                       {{ $isLocked ? 'disabled' : '' }}>
                            </div>

                            @if($progressLogs->count())
                                <hr>
                                <h6><i class="bi bi-clock-history me-1"></i>Progress History</h6>
                                <div class="task-history-scroll mt-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date Time</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Attachment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($progressLogs as $log)
                                                    @php
                                                        $logColor = $log->employee_status === 'completed'
                                                            ? 'success'
                                                            : ($log->employee_status === 'in-progress' ? 'info' : 'secondary');
                                                    @endphp
                                                    <tr>
                                                        <td><small>{{ optional($log->progress_updated_at)->format('d M Y, h:i A') }}</small></td>
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
                                                                    <a href="{{ Storage::url($path) }}"
                                                                       target="_blank"
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
                            @endif
                        </div>

                        <div class="modal-footer bg-transparent">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            @if(!$isLocked)
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Save Progress
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('styles')
<style>
    .task-card {
        border-radius: 14px;
    }

    .task-card .card-title,
    .modal-title {
        word-break: break-word;
    }

    .task-card .progress,
    .modal .progress {
        overflow: hidden;
        border-radius: 999px;
    }

    .filter-card {
        cursor: pointer;
        transition: 0.2s ease-in-out;
    }

    .filter-card:hover,
    .filter-card.active {
        transform: translateY(-2px);
        box-shadow: 0 0.35rem 1rem rgba(0, 0, 0, 0.08);
        border-color: #0d6efd;
    }

    .task-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }

    .task-history-scroll {
        max-height: 180px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .task-history-scroll table {
        margin-bottom: 0;
        min-width: 640px;
    }

    .task-modal .modal-dialog {
        overflow-y: initial !important;
    }

    .task-modal .modal-content {
        max-height: 88vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .task-modal .modal-body {
        overflow-y: auto;
        max-height: calc(88vh - 140px);
    }

    .task-modal .modal-header,
    .task-modal .modal-footer {
        flex-shrink: 0;
        background: #fff;
    }

    .task-kpi-progress {
        background-color: #e9ecef !important;
        border-radius: 999px;
        overflow: hidden;
    }

    .task-kpi-progress .progress-bar {
        height: 100%;
        min-width: 0;
        opacity: 1 !important;
    }

    @media (max-width: 767.98px) {
        .modal-dialog {
            margin: 0.5rem;
        }

        .task-actions {
            grid-template-columns: 1fr;
        }

        .task-modal .modal-content {
            max-height: 92vh;
        }

        .task-modal .modal-body {
            max-height: calc(92vh - 130px);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterCards = document.querySelectorAll('.filter-card');
        const taskItems = document.querySelectorAll('.task-item');

        filterCards.forEach(card => {
            card.addEventListener('click', function () {
                filterCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                const status = this.dataset.status;

                taskItems.forEach(item => {
                    if (status === 'all' || item.dataset.status === status) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Task Modal - Employee assignment management
        document.querySelectorAll('.employee-add-select').forEach(function(select) {
            select.addEventListener('change', function() {
                var opt = this.selectedOptions[0];
                if (!opt.value) return;

                var taskId = this.dataset.taskId;
                var container = document.getElementById('taskAssignees' + taskId);

                // Check if already assigned
                if (container.querySelector('input[value="' + opt.value + '"]')) {
                    this.value = '';
                    return;
                }

                var name = opt.dataset.name;
                var dept = opt.dataset.dept || '';

                var row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center border-bottom py-1 assignee-row';
                row.innerHTML =
                    '<div><strong>' + name + '</strong> <small class="text-muted">(' + dept + ')</small></div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger remove-assignee" data-employee-id="' + opt.value + '">×</button>' +
                    '<input type="hidden" name="employee_ids[]" value="' + opt.value + '">';

                container.appendChild(row);

                // Remove "no employees" placeholder
                var placeholder = container.querySelector('.text-muted');
                if (placeholder && container.children.length === 1) {
                    container.innerHTML = '';
                    container.appendChild(row);
                }

                this.value = '';
            });
        });

        // Remove assignee (delegated)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-assignee')) {
                var row = e.target.closest('.assignee-row');
                if (row) row.remove();

                // Show placeholder if empty
                var container = row ? row.closest('[id^="taskAssignees"]') : null;
                if (container && container.children.length === 0) {
                    container.innerHTML = '<small class="text-muted">No employees assigned</small>';
                }
            }
        });
    });
</script>
@endpush