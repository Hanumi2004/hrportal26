@extends('layouts.master')
 
@section('content')
	<div class="content container-fluid">
    	<div class="page-header">
        	<div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                            	<nav aria-label="breadcrumb">
                                	<ol class="breadcrumb mb-0">
                                    	<li class="breadcrumb-item"><a href="{{ route('task.index.admin') }}">Tasks</a></li>
                                    	<li class="breadcrumb-item active">Edit Task</li>
                                	</ol>
                            	</nav>
                            	<h3 class="page-title"><br>Edit Task</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
	</div>
 
	<div class="row">
    	<div class="col-12 col-md-8">
        	<div class="card">
                <div class="card-body">
                    <form action="{{ route('task.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
 
                        <div class="mb-3">
                            <label class="form-label">Task Name</label>
                            @if($role_id === 2 || $task->created_by === ($employee->employee_id ?? null))
                            	<input type="text" name="task_name" class="form-control" value="{{ $task->task_name }}" required>
                            @else
                            	<input type="text" class="form-control" value="{{ $task->task_name }}" disabled>
                            	<input type="hidden" name="task_name" value="{{ $task->task_name }}">
                            @endif
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="task_desc" class="form-control" rows="3" {{ ($role_id === 2 || $task->created_by === ($employee->employee_id ?? null)) ? '' : 'disabled' }}>{{ $task->task_desc }}</textarea>
                            @if($role_id !== 2 && $task->created_by !== ($employee->employee_id ?? null))
                            	<input type="hidden" name="task_desc" value="{{ $task->task_desc }}">
                            @endif
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select" {{ ($role_id === 2 || $task->created_by === ($employee->employee_id ?? null)) ? '' : 'disabled' }}>
                            	<option value="">No Project</option>
                                @foreach($projects as $project)
                                	<option value="{{ $project->id }}" {{ $task->project_id == $project->id ? 'selected' : '' }}>
                                    	{{ $project->project_name }}
                                    </option>
                            	@endforeach
                            </select>
                            @if($role_id !== 2)
                            	<input type="hidden" name="project_id" value="{{ $task->project_id }}">
                            @endif
                        </div>
 
                        @if($role_id === 2)
                        {{-- Admin/Creator: Full assignment section --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign To</label>
                            <div class="border rounded p-3 bg-white">
                            	<div class="mb-3">
                                	<label class="form-label text-muted">Departments</label>
                                	<div class="row">
                                        @foreach($departments as $dept)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input department-checkbox" type="checkbox"
                                                        value="{{ $dept->id }}" name="department_ids[]"
                                                        {{ in_array($dept->id, $assignedDepartments) ? 'checked' : '' }}>
                                                    <label class="form-check-label">{{ $dept->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                            	</div>
                            	<hr>
                            	<div class="mb-3">
                                	<label class="form-label text-muted">Employees</label>
                                	<select id="employeeSearch" class="form-select">
                                        <option value="">Search employee...</option>
                                        @foreach($allEmployees as $emp)
                                            <option value="{{ $emp['id'] }}" data-name="{{ $emp['name'] }}" data-dept="{{ $emp['department'] }}">
                                                {{ $emp['name'] }} ({{ $emp['department'] }})
                                            </option>
                                        @endforeach
                                    </select>
                            	</div>
                            	<div>
                                	<label class="form-label text-muted">Assigned Employees</label>
                                	<div id="employeeList" class="border rounded p-3 bg-light" style="min-height: 100px;">
                                        @forelse($assignedEmployees as $assigned)
                                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                                <div>
                                                    <strong>{{ $assigned['full_name'] }}</strong>
                                                    <small class="text-muted">({{ $assigned['department'] }})</small>
                                                    @if($assigned['status'])
                                                        <span class="badge bg-{{ $assigned['status'] === 'completed' ? 'success' : ($assigned['status'] === 'in-progress' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst($assigned['status']) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee('{{ $assigned['employee_id'] }}')">×</button>
                                                <input type="hidden" name="employee_ids[]" value="{{ $assigned['employee_id'] }}">
                                            </div>
                                    	@empty
                                            <small class="text-muted">No employees selected</small>
                                        @endforelse
                                    </div>
                            	</div>
                            </div>
                        </div>
                        @endif
 
                        <div class="mb-3">
                            <label class="form-label">Task Status</label>
                            <select name="task_status" class="form-select" required>
                            	<option value="to-do" {{ $task->task_status === 'to-do' ? 'selected' : '' }}>To-Do</option>
                            	<option value="in-progress" {{ $task->task_status === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            	<option value="in-review" {{ $task->task_status === 'in-review' ? 'selected' : '' }}>In Review</option>
                            	<option value="to-review" {{ $task->task_status === 'to-review' ? 'selected' : '' }}>To Review</option>
                            	<option value="completed" {{ $task->task_status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ $task->due_date?->format('Y-m-d') }}" {{ ($role_id === 2 || $task->created_by === ($employee->employee_id ?? null)) ? '' : 'disabled' }}>
                            @if($role_id !== 2)
                            	<input type="hidden" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}">
                            @endif
                        </div>
 
                        @if($role_id !== 2)
                        {{-- Employee: Progress Update Section --}}
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                            	<h5 class="mb-0">My Progress</h5>
                            </div>
                            <div class="card-body">
                            	@php
                                    $myAssignment = $assignedEmployees->firstWhere('employee_id', $employee->employee_id ?? '');
                            	@endphp
                            	
                            	<div class="mb-3">
                                	<label class="form-label">My Status</label>
                                	<select name="employee_status" class="form-select">
                                        <option value="pending" {{ ($myAssignment['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in-progress" {{ ($myAssignment['status'] ?? '') === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ ($myAssignment['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                            	</div>
                            	
                            	<div class="mb-3">
                                	<label class="form-label">My Remarks / Progress</label>
                                    <textarea name="employee_remarks" class="form-control" rows="4" placeholder="What have you done?">{{ $myAssignment['remarks'] ?? '' }}</textarea>
                                	<small class="text-muted">Describe your progress on this task</small>
                            	</div>
 
                                @if($myAssignment['progress_updated_at'])
                                	<small class="text-muted">
                                    	Last updated: {{ \Carbon\Carbon::parse($myAssignment['progress_updated_at'])->diffForHumans() }}
                                    </small>
                            	@endif
                            </div>
                        </div>
                        @endif
 
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" {{ ($role_id === 2 || $task->created_by === ($employee->employee_id ?? null)) ? '' : 'disabled' }}>{{ $task->notes }}</textarea>
                            @if($role_id !== 2)
                            	<input type="hidden" name="notes" value="{{ $task->notes }}">
                            @endif
                        </div>
 
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('task.index.admin') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
    	</div>
 
    	{{-- Right Side: Assigned Employees Progress --}}
    	<div class="col-12 col-md-4">
        	<div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Team Progress</h5>
                </div>
                <div class="card-body">
                    @forelse($assignedEmployees as $assigned)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $assigned['full_name'] }}</strong>
                            	<span class="badge bg-{{ $assigned['status'] === 'completed' ? 'success' : ($assigned['status'] === 'in-progress' ? 'warning' : 'secondary') }}">
                                	{{ ucfirst($assigned['status'] ?? 'pending') }}
                            	</span>
                            </div>
                            @if($assigned['remarks'])
                            	<p class="text-muted small mb-1 mt-2">{{ Str::limit($assigned['remarks'], 100) }}</p>
                            @endif
                            @if($assigned['progress_updated_at'])
                            	<small class="text-muted">{{ \Carbon\Carbon::parse($assigned['progress_updated_at'])->diffForHumans() }}</small>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No assignments yet</p>
                    @endforelse
                </div>
            </div>
    	</div>
	</div>
 
	<script>
    	let selectedEmployees = new Map();
    	let selectedDepartments = new Set();
    	let departmentEmployees = new Map();
 
    	// Initialize existing assignments
        @foreach($assignedEmployees as $assigned)
            selectedEmployees.set('{{ $assigned['employee_id'] }}', {
            	id: '{{ $assigned['employee_id'] }}',
            	name: '{{ $assigned['full_name'] }}',
                department: '{{ $assigned['department'] }}'
        	});
    	@endforeach
 
        @foreach($assignedDepartments as $deptId)
            selectedDepartments.add('{{ $deptId }}');
    	@endforeach
 
    	function renderEmployees() {
        	const container = document.getElementById('employeeList');
        	if (!container) return;
            container.innerHTML = '';
 
        	if (selectedEmployees.size === 0) {
                container.innerHTML = '<small class="text-muted">No employees selected</small>';
                return;
        	}
 
            selectedEmployees.forEach(emp => {
                container.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>${emp.name}</strong>
                            <small class="text-muted">(${emp.department})</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee('${emp.id}')">×</button>
                        <input type="hidden" name="employee_ids[]" value="${emp.id}">
                    </div>
            	`;
        	});
    	}
 
    	function removeEmployee(id) {
            selectedEmployees.delete(id);
            renderEmployees();
    	}
 
        document.getElementById('employeeSearch')?.addEventListener('change', function() {
        	const option = this.selectedOptions[0];
        	if (!option.value || selectedEmployees.has(option.value)) return;
 
            selectedEmployees.set(option.value, {
            	id: option.value,
            	name: option.dataset.name,
                department: option.dataset.dept
        	});
            renderEmployees();
        	this.value = '';
    	});
 
        document.querySelectorAll('.department-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
            	if (this.checked) {
                    selectedDepartments.add(this.value);
            	} else {
                    selectedDepartments.delete(this.value);
            	}
        	});
    	});
 
        renderEmployees();
	</script>
@endsection

 
