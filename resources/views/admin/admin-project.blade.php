@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Projects (Admin)</h4>
	<div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Project</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Project</h3>
                                <p class="text-muted">Manage your projects and track their progress.</p>
                            </div>
                            @php
								$user = Auth::user();
							@endphp

							<div class="d-flex gap-3">
								<div class="btn-group" role="group">
									<button
										class="btn btn-outline-primary {{ request()->routeIs('task.index.admin') ? 'active disabled' : '' }}"
										onclick="window.location='{{ route('task.index.admin') }}'"
										{{ request()->routeIs('task.index.admin') ? 'disabled' : '' }}>
										Task
									</button>

									<button
										class="btn btn-outline-primary {{ request()->routeIs('project.index.admin') ? 'active disabled' : '' }}"
										onclick="window.location='{{ route('project.index.admin') }}'"
										{{ request()->routeIs('project.index.admin') ? 'disabled' : '' }}>
										Project
									</button>
								</div>

								{{-- New Project: hide untuk President (7) & Others (6) --}}
								@if(!in_array($user->role_id, [6, 7], true))
									<button class="btn-new" onclick="window.location='{{ route('project.create') }}'">
										New Project
									</button>
								@endif
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	
	<!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('project.index.admin') }}">
                <input type="hidden" name="status" id="filterStatusInput" value="{{ request('status') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Search Project</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Project name or ID...">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Created By</label>
                        <select name="created_by" class="form-control">
                            <option value="">All Employees</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->employee_id }}"
                                    {{ request('created_by') == $employee->employee_id ? 'selected' : '' }}>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-1">
                        <a href="{{ route('project.index.admin') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary cards boleh guna $totalProjects etc --}}

    @forelse($projects as $project)
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">{{ $project->project_name }}</h5>
                    <small class="text-muted">
                        Created by:
                        {{ optional($project->createdBy)->full_name ?? 'N/A' }}
                        &middot;
                        Created: {{ optional($project->created_at)->format('d M Y') }}
                    </small>
                </div>
                <div class="text-end">
                    <span class="badge
                        @if($project->project_status === 'completed') bg-success
                        @elseif($project->project_status === 'in-progress') bg-primary
                        @elseif($project->project_status === 'on-hold') bg-warning
                        @else bg-secondary @endif">
                        {{ strtoupper(str_replace('-', ' ', $project->project_status)) }}
                    </span>
                </div>
            </div>

            <div class="card-body">
                {{-- Project-level progress bar (optional, placeholder) --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Project Progress</small>
                        {{-- Nanti boleh kira % sebenar --}}
                        <small class="text-muted">100% Completed</small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 100%;"></div>
                    </div>
                </div>

                                @php
									$user = Auth::user();
								@endphp

								{{-- Task list under this project --}}
								<h6 class="mb-2">Tasks under this project</h6>

								@forelse($project->tasks as $task)
									<div class="d-flex justify-content-between align-items-center border-bottom py-2">
										<div>
											<div class="fw-semibold">{{ $task->task_name }}</div>
											<small class="text-muted">
												Owner: {{ optional($task->createdBy)->full_name ?? '-' }}
												&middot;
												Due: {{ optional($task->due_date)->format('d M Y') ?? '-' }}
											</small>
										</div>

										<div class="d-flex align-items-center gap-2">
											<span class="badge
												@if($task->task_status === 'completed') bg-success
												@elseif($task->task_status === 'in-progress') bg-primary
												@elseif(in_array($task->task_status, ['in-review', 'to-review'])) bg-info
												@else bg-secondary @endif">
												{{ ucfirst(str_replace('-', ' ', $task->task_status)) }}
											</span>

											{{-- Semua role boleh view progress (President observer included) --}}
											<button class="btn btn-sm btn-outline-primary"
													data-bs-toggle="modal"
													data-bs-target="#taskModal{{ $task->id }}">
												View Progress
											</button>
										</div>
									</div>
								@empty
									<p class="text-muted mb-0">No tasks created under this project yet.</p>
								@endforelse

								{{-- Project-level actions (View / Edit / Delete) --}}
								<div class="d-flex justify-content-end mt-3">
									<a href="{{ route('project.show', $project->id) }}" class="btn btn-sm btn-outline-secondary me-2">
										View
									</a>

									{{-- Edit: ikut helper canManageProjectMeta --}}
									@if(method_exists($user, 'canManageProjectMeta') && $user->canManageProjectMeta($project))
										<button class="btn btn-sm btn-primary me-2"
												data-bs-toggle="modal"
												data-bs-target="#projectEditModal-{{ $project->id }}">
											Edit
										</button>
									@endif

									{{-- Delete: hanya Super Admin --}}
									@if($user->role_id === 1)
										<form action="{{ route('project.destroy', $project->id) }}" method="POST" class="d-inline"
											  onsubmit="return confirm('Delete this project?');">
											@csrf
											@method('DELETE')
											<button class="btn btn-sm btn-danger">
												Delete
											</button>
										</form>
									@endif
								</div>
							</div>
						</div>
					@empty
						<p class="text-muted">No projects found.</p>
					@endforelse
				@endsection