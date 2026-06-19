@extends('layouts.master')

@section('content')
    @php
        $user = Auth::user();
        $employee = $user->employee ?? null;
    @endphp

    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Project</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Project</h3>
                                <p class="text-muted">Manage your projects and track their progress.</p>
                            </div>

                            <div class="d-flex gap-3">
                                <div class="btn-group" role="group">
                                    <button
                                        class="btn btn-outline-primary {{ request()->routeIs('task.index.employee') ? 'active disabled' : '' }}"
                                        onclick="window.location='{{ route('task.index.employee') }}'"
                                        {{ request()->routeIs('task.index.employee') ? 'disabled' : '' }}>
                                        Task
                                    </button>

                                    <button
                                        class="btn btn-outline-primary {{ request()->routeIs('project.index.employee') ? 'active disabled' : '' }}"
                                        onclick="window.location='{{ route('project.index.employee') }}'"
                                        {{ request()->routeIs('project.index.employee') ? 'disabled' : '' }}>
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
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('project.index.employee') }}">
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
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->employee_id }}"
                                    {{ request('created_by') == $emp->employee_id ? 'selected' : '' }}>
                                    {{ $emp->full_name }}
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
                        <a href="{{ route('project.index.employee') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row">
        <!-- Total Projects -->
        <div class="col-12 col-md-2 mb-4">
            <div class="card filter-card active" data-status="all">
                <div class="card-body text-center">
                    <i class="bi bi-list-task"></i>
                    <div class="card-title">Total Projects</div>
                    <span class="stat-number total">{{ $totalProjects }}</span>
                </div>
            </div>
        </div>

        <!-- Not Started Projects -->
        <div class="col-12 col-md-2 mb-4">
            <div class="card filter-card" data-status="not-started">
                <div class="card-body text-center">
                    <i class="bi bi-circle"></i>
                    <div class="card-title">Not Started</div>
                    <span class="stat-number not-started">{{ $notStartedProjects }}</span>
                </div>
            </div>
        </div>

        <!-- In Planning (alias not-started) -->
        <div class="col-12 col-md-2 mb-4">
            <div class="card filter-card" data-status="not-started">
                <div class="card-body text-center">
                    <i class="bi bi-circle"></i>
                    <div class="card-title">In Planning</div>
                    <span class="stat-number not-started">{{ $notStartedProjects }}</span>
                </div>
            </div>
        </div>

        <!-- In-Progress Projects -->
        <div class="col-12 col-md-2 mb-4">
            <div class="card filter-card" data-status="in-progress">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-repeat"></i>
                    <div class="card-title">In Progress</div>
                    <span class="stat-number in-progress">{{ $inProgressProjects }}</span>
                </div>
            </div>
        </div>

        <!-- On Hold Projects -->
        <div class="col-12 col-md-2 mb-4">
            <div class="card filter-card" data-status="on-hold">
                <div class="card-body text-center">
                    <i class="bi bi-eye-fill"></i>
                    <div class="card-title">On Hold</div>
                    <span class="stat-number on-hold">{{ $onHoldProjects }}</span>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="col-12 col-md-2 mb-4">
            <div class="card filter-card" data-status="completed">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle-fill"></i>
                    <div class="card-title">Completed</div>
                    <span class="stat-number completed">{{ $completedProjects }}</span>
                </div>
            </div>
        </div>
    </div>

    <div id="projectsCard">
        <div class="row">
            @forelse ($projects as $project)
                <div class="col-md-12 mb-3 project-item" data-status="{{ $project->project_status }}">
                    <div class="card h-100" data-bs-toggle="modal" data-bs-target="#projectModal{{ $project->id }}"
                         style="cursor:pointer;">

                        <div class="card-body">
                            <div class="row">
                                <!-- Left side: Project details -->
                                <div class="col-8">
                                    <h5 class="fw-bold mb-2 text-dark">{{ $project->project_name }}</h5>
                                    <p class="text-muted mb-3">{{ $project->project_desc }}</p>

                                    <div class="task-meta mb-1">
                                        <i class="bi bi-person-fill me-1 text-secondary"></i>
                                        <strong>Created By:</strong>
                                        {{ optional($project->createdBy)->full_name ?? $project->created_by }}
                                    </div>

                                    <small class="text-muted">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Created: {{ optional($project->created_at)->format('d M Y') ?? '-' }} |
                                        Updated: {{ optional($project->updated_at)->format('d M Y') ?? '-' }}
                                    </small>
                                </div>

                                <!-- Right side: Status & Dates -->
                                <div class="col-md-4 text-md-end">
                                    @switch($project->project_status)
                                        @case('not-started')
                                            <span class="badge bg-danger mb-3">Not-Started</span>
                                            @break

                                        @case('in-progress')
                                            <span class="badge bg-info text-dark mb-3">In-Progress</span>
                                            @break

                                        @case('on-hold')
                                            <span class="badge bg-warning mb-3">On-Hold</span>
                                            @break

                                        @case('completed')
                                            <span class="badge bg-success mb-3">Completed</span>
                                            @break
                                    @endswitch

                                    <div>
                                        <i class="bi bi-calendar-event me-1 text-secondary"></i>
                                        <strong>Start:</strong>
                                        {{ optional($project->start_date)->format('d M Y') ?? '-' }}
                                    </div>
                                    <div>
                                        <i class="bi bi-calendar-event me-1 text-secondary"></i>
                                        <strong>End:</strong>
                                        {{ optional($project->end_date)->format('d M Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted no-projects-static">
                    <i class="bi bi-inbox display-6 mb-2"></i>
                    <p class="mb-0">No projects found</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Update/view project modal --}}
    @foreach ($projects as $project)
        @php
            // President & Others are view-only
            $isPresidentOrOthers = in_array($user->role_id, [6, 7], true);
            $canEditMeta = !$isPresidentOrOthers
                && method_exists($user, 'canManageProjectMeta')
                && $user->canManageProjectMeta($project);
        @endphp

        <div class="modal fade" id="projectModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    @if($canEditMeta)
                        <form action="{{ route('project.update', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                    @endif

                        <div class="modal-header">
                            <h5 class="modal-title">Project Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Project Name</th>
                                    <td>
                                        <input type="text" name="project_name" class="form-control"
                                               value="{{ old('project_name', $project->project_name) }}"
                                               {{ $canEditMeta ? '' : 'readonly' }}>
                                        @error('project_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>
                                        <textarea name="project_desc" class="form-control"
                                              {{ $canEditMeta ? '' : 'readonly' }}>{{ old('project_desc', $project->project_desc) }}</textarea>
                                        @error('project_desc')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created by</th>
                                    <td>{{ optional($project->createdBy)->full_name ?? $project->created_by }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <select id="project_status" name="project_status" class="form-select"
                                                {{ $canEditMeta ? '' : 'disabled' }}>
                                            <option value="" disabled
                                                {{ !$project->project_status ? 'selected' : '' }}>Select Status</option>

                                            <option value="not-started"
                                                {{ old('project_status', $project->project_status) === 'not-started' ? 'selected' : '' }}>
                                                Not Started
                                            </option>

                                            <option value="in-progress"
                                                {{ old('project_status', $project->project_status) === 'in-progress' ? 'selected' : '' }}>
                                                In-Progress
                                            </option>

                                            <option value="on-hold"
                                                {{ old('project_status', $project->project_status) === 'on-hold' ? 'selected' : '' }}>
                                                On-Hold
                                            </option>

                                            <option value="completed"
                                                {{ old('project_status', $project->project_status) === 'completed' ? 'selected' : '' }}>
                                                Completed
                                            </option>
                                        </select>
                                        @error('project_status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>
                                        <input type="date" name="end_date" class="form-control"
                                               value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}"
                                               {{ $canEditMeta ? '' : 'readonly' }}>
                                        @error('end_date')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="modal-footer">
                            @if($canEditMeta)
                                <button type="submit" class="btn btn-primary">Save</button>
                            @endif
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ $canEditMeta ? 'Cancel' : 'Close' }}
                            </button>
                        </div>

                    @if($canEditMeta)
                        </form>
                    @endif
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

                document.querySelectorAll('#projectsCard .project-item, #projectsCard .no-projects-static')
                    .forEach(item => {
                        if (item.classList.contains('no-projects-static')) {
                            item.style.display = 'none';
                        }
                    });

                document.querySelectorAll('#projectsCard .project-item').forEach(item => {
                    if (status === 'all' || item.dataset.status === status) {
                        item.style.display = '';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const existingStaticMsg = document.querySelector('#projectsCard .no-projects-static');
                let noProjectsMessage = document.getElementById('noProjectsMessage');

                if (noProjectsMessage) {
                    noProjectsMessage.remove();
                }

                if (visibleCount === 0) {
                    if (existingStaticMsg) {
                        existingStaticMsg.style.display = '';
                        existingStaticMsg.querySelector('p').textContent =
                            `No ${getProjectStatusText(status).toLowerCase()} projects found`;
                    } else {
                        noProjectsMessage = document.createElement('div');
                        noProjectsMessage.id = 'noProjectsMessage';
                        noProjectsMessage.className = 'text-center py-4 text-muted no-projects-dynamic';
                        noProjectsMessage.innerHTML = `
                            <i class="bi bi-inbox display-6 mb-2"></i>
                            <p class="mb-0">No ${getProjectStatusText(status).toLowerCase()} projects found</p>
                        `;
                        document.querySelector('#projectsCard .row').appendChild(noProjectsMessage);
                    }
                } else {
                    if (existingStaticMsg) {
                        existingStaticMsg.style.display = 'none';
                    }
                }
            });
        });

        function getProjectStatusText(status) {
            const statusMap = {
                'all': 'all',
                'not-started': 'not-started',
                'in-progress': 'in-progress',
                'on-hold': 'on-hold',
                'completed': 'completed'
            };
            return statusMap[status] || status;
        }
    </script>
@endpush