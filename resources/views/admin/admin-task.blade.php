@extends('layouts.master')

@section('content')

@php
    $user     = Auth::user();
    $employee = $user->employee ?? null;
@endphp

<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-sub-header">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Task</li>
                                </ol>
                            </nav>
                            <h3 class="page-title"><br>Task</h3>
                            <p class="text-muted">Manage your tasks and track their progress.</p>
                        </div>
                        @php
							$user = Auth::user();
						@endphp

						<div class="d-flex gap-3">
							<div class="btn-group" role="group">
								<button class="btn btn-outline-primary {{ request()->routeIs('task.index.admin') ? 'active disabled' : '' }}"
									onclick="window.location='{{ route('task.index.admin') }}'"
									{{ request()->routeIs('task.index.admin') ? 'disabled' : '' }}>
									Task
								</button>
								<button class="btn btn-outline-primary {{ request()->routeIs('project.index.admin') ? 'active disabled' : '' }}"
									onclick="window.location='{{ route('project.index.admin') }}'"
									{{ request()->routeIs('project.index.admin') ? 'disabled' : '' }}>
									Project
								</button>
							</div>

							{{-- New Task: semua role kecuali President (7) & Others (6) --}}
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
</div>

<!-- Filters and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('task.index.admin') }}">
            <input type="hidden" name="status" id="filterStatusInput" value="{{ request('status') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label">Search Task</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Task name or ID...">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
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
					<button type="submit" class="btn btn-primary w-100">Filter</button>
				</div>
				<div class="col-12 col-sm-6 col-lg-1">
					<a href="{{ route('task.index.admin') }}" class="btn btn-secondary w-100">Reset</a>
				</div>
            </div>
        </form>
    </div>
</div>

<!-- Stat Cards (clickable) -->
<div class="row mb-4">
    <div class="col-6 col-md-2 mb-3">
        <div class="card filter-card text-center h-100" data-status="all" style="cursor:pointer;">
            <div class="card-body">
                <i class="bi bi-list-task fs-4 text-primary"></i>
                <div class="fs-4 fw-bold">{{ $totalTasks }}</div>
                <small class="text-muted">Total</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2 mb-3">
        <div class="card filter-card text-center h-100" data-status="to-do" style="cursor:pointer;">
            <div class="card-body">
                <i class="bi bi-circle fs-4 text-danger"></i>
                <div class="fs-4 fw-bold">{{ $toDoTasks }}</div>
                <small class="text-muted">To-Do</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2 mb-3">
        <div class="card filter-card text-center h-100" data-status="in-progress" style="cursor:pointer;">
            <div class="card-body">
                <i class="bi bi-arrow-repeat fs-4 text-info"></i>
                <div class="fs-4 fw-bold">{{ $inProgressTasks }}</div>
                <small class="text-muted">In Progress</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2 mb-3">
        <div class="card filter-card text-center h-100" data-status="in-review" style="cursor:pointer;">
            <div class="card-body">
                <i class="bi bi-eye-fill fs-4 text-primary"></i>
                <div class="fs-4 fw-bold">{{ $inReviewTasks }}</div>
                <small class="text-muted">In Review</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2 mb-3">
        <div class="card filter-card text-center h-100" data-status="to-review" style="cursor:pointer;">
            <div class="card-body">
                <i class="bi bi-bell-fill fs-4 text-warning"></i>
                <div class="fs-4 fw-bold">{{ $toReviewTasks }}</div>
                <small class="text-muted">To Review</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-2 mb-3">
        <div class="card filter-card text-center h-100" data-status="completed" style="cursor:pointer;">
            <div class="card-body">
                <i class="bi bi-check-circle fs-4 text-success"></i>
                <div class="fs-4 fw-bold">{{ $completedTasks }}</div>
                <small class="text-muted">Completed</small>
            </div>
        </div>
    </div>
</div>

<!-- Tasks Grid -->
<div id="tasksCard">
    <div class="row">
        @forelse($tasks as $task)
            @php
                $taskAssignments = $task->assignedTo;
                $taskTotal = $taskAssignments->count();
                $taskCompleted = $taskAssignments->filter(function($emp) {
                    return $emp->pivot && $emp->pivot->employee_status === 'completed';
                })->count();
                $taskPercentage = $taskTotal > 0 ? round(($taskCompleted / $taskTotal) * 100) : 0;
            @endphp
            
            <div class="col-md-6 col-xl-4 mb-3 task-item" data-status="{{ $task->task_status }}">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $task->task_name }}</h5>
                            <span class="badge bg-{{ 
                                $task->task_status === 'completed' ? 'success' : 
                                ($task->task_status === 'in-progress' ? 'info' : 
                                ($task->task_status === 'in-review' ? 'primary' : 
                                ($task->task_status === 'to-review' ? 'warning' : 'danger'))) 
                            }}">
                                {{ ucfirst(str_replace('-', ' ', $task->task_status)) }}
                            </span>
                        </div>
                        
                        <p class="text-muted small mb-3">{{ Str::limit($task->task_desc, 80) }}</p>
                        
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="bi bi-folder me-1"></i>
                                {{ optional($task->project)->project_name ?? 'No Project' }}
                            </small>
                        </div>
                        
                        @if($taskTotal > 0)
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-people me-1"></i>
                                    @foreach($task->assignedTo as $idx => $emp)
                                        @if($idx < 2) {{ $emp->full_name }}@if($task->assignedTo->count() > 1 && !$loop->last), @endif @else @break @endif
                                    @endforeach
                                    @if($taskTotal > 2) +{{ $taskTotal - 2 }} more @endif
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progress</span>
                                    <span class="fw-bold">{{ $taskPercentage }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $taskPercentage == 100 ? 'success' : 'primary' }}" 
                                         style="width: {{ $taskPercentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $taskCompleted }}/{{ $taskTotal }} completed</small>
                            </div>
                        @else
                            <div class="alert alert-light py-1 px-2 small mb-2">
                                <i class="bi bi-exclamation-circle me-1"></i>Not Assigned
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $task->due_date ? $task->due_date->format('d M Y') : 'No due date' }}
                            </small>
                            <small class="text-muted">
                                {{ $task->created_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
						
					@php
						$isCreator = $task->created_by === ($employee->employee_id ?? null);
					@endphp
 
					<div class="card-footer bg-transparent">
						{{-- View button (always show) --}}
						<button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
								data-bs-target="#taskViewModal{{ $task->id }}">
							<i class="bi bi-eye me-1"></i>View
						</button>
                    
						@if($isCreator || in_array($user->role_id, [1, 2]))
						<button class="btn btn-sm btn-primary" data-bs-toggle="modal"
								data-bs-target="#taskModal{{ $task->id }}">
							<i class="bi bi-pencil me-1"></i>Edit
						</button>
					@endif
				</div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted no-tasks-static">
                    <i class="bi bi-inbox display-1"></i>
                    <h5 class="mt-3">No tasks found</h5>
                    <p>Create a new task to get started</p>
                    <a href="{{ route('task.create') }}" class="btn btn-primary">Create Task</a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Edit Modal for each task -->
@foreach($tasks as $task)
    @php
        $modalAssignments = $task->assignedTo;
        $modalTotal = $modalAssignments->count();
        $modalCompleted = $modalAssignments->filter(function($emp) {
            return $emp->pivot && $emp->pivot->employee_status === 'completed';
        })->count();
        $modalInProgress = $modalAssignments->filter(function($emp) {
            return $emp->pivot && $emp->pivot->employee_status === 'in-progress';
        })->count();
        $modalPercentage = $modalTotal > 0 ? round(($modalCompleted / $modalTotal) * 100) : 0;
    @endphp
    
    <div class="modal fade" id="taskModal{{ $task->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task: {{ $task->task_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('task.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Progress Summary -->
                        @if($modalTotal > 0)
                            <div class="alert alert-info mb-3">
                                <div class="d-flex justify-content-between">
                                    <strong>Team Progress:</strong>
                                    <span class="badge bg-primary">{{ $modalPercentage }}%</span>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar" style="width: {{ $modalPercentage }}%"></div>
                                </div>
                                <small>{{ $modalCompleted }} completed, {{ $modalInProgress }} in progress</small>
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
                                            <option value="{{ $proj->id }}" {{ $task->project_id == $proj->id ? 'selected' : '' }}>
                                                {{ $proj->project_name }}
                                            </option>
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
                        
                        <!-- Current Assigned Employees -->
                        @if($modalTotal > 0)
                            <hr>
                            <h6><i class="bi bi-people me-1"></i>Currently Assigned ({{ $modalTotal }})</h6>
                            <div class="row mt-2">
                                @foreach($task->assignedTo as $member)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex justify-content-between align-items-center border rounded p-2">
                                            <div>
                                                <strong>{{ $member->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    Status: 
                                                    <span class="badge bg-{{ 
                                                        ($member->pivot->employee_status ?? 'pending') === 'completed' ? 'success' : 
                                                        (($member->pivot->employee_status ?? 'pending') === 'in-progress' ? 'warning' : 'secondary') 
                                                    }}">
                                                        {{ ucfirst($member->pivot->employee_status ?? 'pending') }}
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Reassign Employees -->
                        <hr>
                        <h6><i class="bi bi-person-plus me-1"></i>Reassign Employees</h6>
                        <p class="text-muted small">Select employees to assign to this task</p>
                        
                        <div class="row">
                            @foreach($employees as $emp)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="employee_ids[]" 
                                               value="{{ $emp->employee_id }}"
                                               id="emp{{ $task->id }}_{{ $emp->employee_id }}"
                                               @foreach($task->assignedTo as $assigned)
                                                   @if($assigned->employee_id === $emp->employee_id) checked @endif
                                               @endforeach>
                                        <label class="form-check-label" for="emp{{ $task->id }}_{{ $emp->employee_id }}">
                                            {{ $emp->full_name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						
						<hr>
						<h6><i class="bi bi-pencil-square me-1"></i>Creator Actions</h6>

						@if($task->created_by === ($employee->employee_id ?? null) || in_array($user->role_id, [1, 2]))
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label">Extend Deadline</label>
										<input type="date" name="new_due_date" class="form-control"
											   min="{{ $task->due_date?->format('Y-m-d') ?? '' }}">
										<small class="text-muted">Leave empty to keep current: {{ $task->due_date?->format('d M Y') ?? 'No due date' }}</small>
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="form-label">Add Remarks (Creator)</label>
										<textarea name="creator_remarks" class="form-control" rows="2"
												  placeholder="Add remarks about this task..."></textarea>
									</div>
								</div>
							</div>
						@endif
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        let status = this.dataset.status;
        let visibleCount = 0;

        document.querySelectorAll('#tasksCard .task-item').forEach(item => {
            item.style.display = '';
        });

        document.querySelectorAll('#tasksCard .task-item').forEach(item => {
            if (status === 'all' || item.dataset.status === status) {
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const noTasksStatic = document.querySelector('#tasksCard .no-tasks-static');
        if (noTasksStatic) {
            noTasksStatic.style.display = 'none';
        }
    });
});
</script>
@endpush