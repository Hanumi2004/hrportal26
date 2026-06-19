@extends('layouts.master')

@section('content')
<div class="content container-fluid task-create-page">
    <div class="task-page-wrap">
        {{-- Header --}}
        <div class="page-header task-page-header">
            <div class="task-page-intro">
                <div class="task-page-intro__content">
                    <div class="task-breadcrumb">Task &amp; Project / Task</div>
                    <h2 class="task-page-title">Create New Task</h2>
                    <p class="task-page-subtitle">
                        Create a task, define the scope, and assign it to the right employee.
                    </p>
                </div>
            </div>
        </div>

        {{-- Success --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <strong>Please fix the following:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('task.store') }}" method="POST">
            @csrf

            <div class="task-form-shell">
                {{-- Task Details --}}
                <section class="task-form-card">
                    <div class="task-section-head">
                        <div>
                            <span class="task-section-kicker">Section 01</span>
                            <h5>Task Details</h5>
                            <p>Fill in the core information for this task.</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-block">
                                <label class="form-label">Project</label>
                                <select name="project_id"
                                        class="form-select modern-input @error('project_id') is-invalid @enderror"
                                        id="projectSelect">
                                    <option value="">-- Independent Task / No Project --</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_name }}
                                        </option>
                                    @endforeach
                                    <option value="__create__" {{ old('project_id') == '__create__' ? 'selected' : '' }}>
                                        + Create New Project
                                    </option>
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- New Project inline fields (below project dropdown) --}}
                        <div class="col-12 px-0" id="newProjectFields" style="display: {{ old('project_id') == '__create__' ? 'block' : 'none' }};">
                            <div class="row g-3">
                            <div class="col-12 px-0">
                                <hr class="my-2">
                                <h6 class="fw-bold mb-0">New Project Details</h6>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-block">
                                    <label class="form-label">Project Name <span class="text-danger">*</span></label>
                                    <input type="text" name="new_project_name" value="{{ old('new_project_name') }}"
                                           class="form-control modern-input @error('new_project_name') is-invalid @enderror"
                                           placeholder="Enter project name">
                                    @error('new_project_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-block">
                                    <label class="form-label">Project Description</label>
                                    <textarea name="new_project_desc" rows="2"
                                              class="form-control modern-input modern-textarea @error('new_project_desc') is-invalid @enderror"
                                              placeholder="Describe the project scope">{{ old('new_project_desc') }}</textarea>
                                    @error('new_project_desc')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-block">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="new_project_start_date" value="{{ old('new_project_start_date') }}"
                                           class="form-control modern-input @error('new_project_start_date') is-invalid @enderror">
                                    @error('new_project_start_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-block">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="new_project_end_date" value="{{ old('new_project_end_date') }}"
                                           class="form-control modern-input @error('new_project_end_date') is-invalid @enderror">
                                    @error('new_project_end_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-block">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="new_project_status" class="form-select modern-input @error('new_project_status') is-invalid @enderror">
                                        <option value="">-- Select Status --</option>
                                        <option value="not-started" {{ old('new_project_status') == 'not-started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="in-progress" {{ old('new_project_status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="on-hold" {{ old('new_project_status') == 'on-hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="completed" {{ old('new_project_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('new_project_status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-block">
                                <label class="form-label">
                                    Task Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="task_name"
                                       value="{{ old('task_name') }}"
                                       class="form-control modern-input @error('task_name') is-invalid @enderror"
                                       placeholder="Enter task name">
                                @error('task_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-block">
                                <label class="form-label">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea name="task_desc"
                                          rows="5"
                                          class="form-control modern-input modern-textarea @error('task_desc') is-invalid @enderror"
                                          placeholder="Describe the task scope, expected deliverables, and key instructions">{{ old('task_desc') }}</textarea>
                                @error('task_desc')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-block">
                                <label class="form-label">
                                    Task Status <span class="text-danger">*</span>
                                </label>
                                <select name="task_status"
                                        class="form-select modern-input @error('task_status') is-invalid @enderror"
                                        required>
                                    <option value="">-- Select Status --</option>
                                    <option value="to-do" {{ old('task_status') == 'to-do' ? 'selected' : '' }}>To-Do</option>
                                    <option value="in-progress" {{ old('task_status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="in-review" {{ old('task_status') == 'in-review' ? 'selected' : '' }}>In Review</option>
                                    <option value="to-review" {{ old('task_status') == 'to-review' ? 'selected' : '' }}>To Review</option>
                                    <option value="completed" {{ old('task_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('task_status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-block">
                                <label class="form-label">
                                    Due Date <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="due_date"
                                       value="{{ old('due_date') }}"
                                       class="form-control modern-input @error('due_date') is-invalid @enderror">
                                @error('due_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-block">
                                <label class="form-label">
                                    Notes <span class="text-danger">*</span>
                                </label>
                                <textarea name="notes"
                                          rows="4"
                                          class="form-control modern-input modern-textarea @error('notes') is-invalid @enderror"
                                          placeholder="Add notes, dependencies, reminders, or context for the assignee">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Assignment --}}
                <section class="task-form-card">
                    <div class="task-section-head">
                        <div>
                            <span class="task-section-kicker">Section 02</span>
                            <h5>Assignment</h5>
                            <p>Assign this task according to access level and reporting hierarchy.</p>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="form-block">
                                <label class="form-label">
                                    Assign Employees <span class="text-danger">*</span>
                                </label>

                                <div class="mb-3">
                                    <select id="employeeSearch"
                                            class="form-select modern-input">
                                        <option value="">-- Search & Select Employee --</option>
                                        @foreach($assignableEmployees as $emp)
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

                                <div>
                                    <label class="form-label text-muted">Selected Employees</label>
                                    <div id="employeeList" class="border rounded p-3 bg-light" style="min-height: 80px;">
                                        <small class="text-muted">No employees selected</small>
                                    </div>
                                </div>

                                <div class="helper-card mt-3">
                                    <div class="helper-icon">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div class="helper-text">
                                        You may assign employees at your level
                                        @if(!is_null($currentHierarchyLevel ?? null))
                                            <strong>(Level {{ $currentHierarchyLevel }})</strong>
                                        @endif
                                        and below only.
                                    </div>
                                </div>

                                @error('employee_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Action bar --}}
                <div class="task-action-wrap">
                    <div class="task-action-bar">
                        <a href="{{ route('task.index.employee') }}" class="btn btn-light btn-cancel-task">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-create-task">
                            <i class="bi bi-plus-circle me-1"></i>
                            Create Task
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var projectSelect = document.getElementById('projectSelect');
        var newProjectFields = document.getElementById('newProjectFields');

        if (projectSelect && newProjectFields) {
            projectSelect.addEventListener('change', function () {
                newProjectFields.style.display = this.value === '__create__' ? 'block' : 'none';
            });
        }

        // Employee multi-select
        let selectedEmployees = new Map();

        window.removeEmployee = function(id) {
            selectedEmployees.delete(id);
            renderEmployees();
        };

        function renderEmployees() {
            var container = document.getElementById('employeeList');
            if (!container) return;
            if (selectedEmployees.size === 0) {
                container.innerHTML = '<small class="text-muted">No employees selected</small>';
                return;
            }
            var html = '';
            selectedEmployees.forEach(function(emp) {
                html += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">' +
                    '<div><strong>' + emp.name + '</strong> <small class="text-muted">(' + emp.department + ')</small></div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEmployee(\'' + emp.id + '\')">×</button>' +
                    '<input type="hidden" name="employee_ids[]" value="' + emp.id + '">' +
                    '</div>';
            });
            container.innerHTML = html;
        }

        document.getElementById('employeeSearch').addEventListener('change', function() {
            var opt = this.selectedOptions[0];
            if (!opt.value || selectedEmployees.has(opt.value)) {
                this.value = '';
                return;
            }
            selectedEmployees.set(opt.value, {
                id: opt.value,
                name: opt.dataset.name,
                department: opt.dataset.dept
            });
            renderEmployees();
            this.value = '';
        });

        // Restore old values on validation error
        @if(old('employee_ids'))
            @foreach(old('employee_ids') as $empId)
                @php
                    $emp = $assignableEmployees->firstWhere('employee_id', $empId);
                @endphp
                @if($emp)
                    selectedEmployees.set('{{ $emp->employee_id }}', {
                        id: '{{ $emp->employee_id }}',
                        name: '{{ $emp->full_name }}',
                        department: '{{ optional($emp->employment)->department->name ?? '' }}'
                    });
                @endif
            @endforeach
            renderEmployees();
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    .task-create-page {
        padding: 20px 24px 48px;
        background: #f5f7fb;
    }

    .task-page-wrap {
        max-width: 1240px;
        margin: 0 auto;
    }

    .task-page-header {
        margin-bottom: 28px;
    }

    .task-page-intro {
        background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
        border: 1px solid #e7ecf3;
        border-radius: 24px;
        padding: 30px 34px;
        box-shadow:
            0 8px 24px rgba(15, 23, 42, 0.04),
            0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .task-page-intro__content {
        max-width: 720px;
    }

    .task-breadcrumb {
        display: inline-block;
        margin-bottom: 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #7c8aa5;
    }

    .task-page-title {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.2;
        color: #1f2a44;
    }

    .task-page-subtitle {
        margin: 0;
        font-size: 14px;
        line-height: 1.7;
        color: #667085;
        max-width: 60ch;
    }

    .task-form-shell {
        max-width: 1080px;
        margin: 0 auto;
    }

    .task-form-card {
        background: #ffffff;
        border: 1px solid #e5eaf2;
        border-radius: 22px;
        padding: 30px 32px 26px;
        box-shadow:
            0 12px 30px rgba(15, 23, 42, 0.05),
            0 2px 10px rgba(15, 23, 42, 0.03);
    }

    .task-form-card + .task-form-card {
        margin-top: 30px;
    }

    .task-section-head {
        margin-bottom: 26px;
        padding-bottom: 18px;
        border-bottom: 1px solid #eef2f7;
    }

    .task-section-kicker {
        display: inline-block;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #98a2b3;
    }

    .task-section-head h5 {
        margin: 0 0 6px;
        font-size: 20px;
        font-weight: 700;
        color: #25324b;
    }

    .task-section-head p {
        margin: 0;
        font-size: 13px;
        line-height: 1.6;
        color: #7a869a;
    }

    .form-block {
        margin-bottom: 0;
    }

    .form-label {
        display: inline-block;
        margin-bottom: 9px;
        font-size: 13px;
        font-weight: 600;
        color: #344054;
    }

    .modern-input {
        min-height: 50px;
        border-radius: 14px;
        border: 1px solid #d9e1ec;
        background: #fcfdff;
        padding: 12px 15px;
        font-size: 14px;
        color: #1f2937;
        box-shadow: none;
        transition: all 0.2s ease;
    }

    .modern-input::placeholder {
        color: #98a2b3;
    }

    .modern-input:focus {
        border-color: #4b6bfb;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(75, 107, 251, 0.10);
    }

    .modern-textarea {
        min-height: 132px;
        resize: vertical;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .helper-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 15px;
        background: #f8faff;
        border: 1px solid #e4ebff;
        border-radius: 14px;
    }

    .helper-icon {
        flex: 0 0 auto;
        color: #4b6bfb;
        font-size: 15px;
        line-height: 1;
        margin-top: 2px;
    }

    .helper-text {
        font-size: 13px;
        line-height: 1.6;
        color: #5f6b7a;
    }

    .task-action-wrap {
        max-width: 1080px;
        margin: 28px auto 0;
    }

    .task-action-bar {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 8px;
    }

    .btn-cancel-task {
        min-width: 124px;
        min-height: 46px;
        border-radius: 12px;
        padding: 10px 18px;
        border: 1px solid #d5dbe6;
        background: #ffffff;
        color: #111827;
        font-weight: 600;
    }

    .btn-cancel-task:hover {
        background: #f8fafc;
        border-color: #cdd5df;
    }

    .btn-create-task {
        min-width: 156px;
        min-height: 46px;
        border-radius: 12px;
        padding: 10px 20px;
        border: none;
        font-weight: 600;
        background: #4b6bfb;
        box-shadow: 0 10px 20px rgba(75, 107, 251, 0.18);
    }

    .btn-create-task:hover {
        background: #3f5ef0;
        box-shadow: 0 12px 24px rgba(63, 94, 240, 0.22);
    }

    .alert {
        border-radius: 18px;
    }

    .invalid-feedback {
        margin-top: 6px;
        font-size: 13px;
    }

    @media (max-width: 1199.98px) {
        .task-create-page {
            padding-left: 20px;
            padding-right: 20px;
        }

        .task-page-wrap {
            max-width: 100%;
        }

        .task-form-shell,
        .task-action-wrap {
            max-width: 100%;
        }
    }

    @media (max-width: 991.98px) {
        .task-page-intro {
            padding: 24px 24px;
            border-radius: 20px;
        }

        .task-form-card {
            padding: 24px 22px 22px;
            border-radius: 18px;
        }

        .task-form-card + .task-form-card {
            margin-top: 24px;
        }
    }

    @media (max-width: 767.98px) {
        .task-create-page {
            padding: 16px 14px 32px;
        }

        .task-page-header {
            margin-bottom: 20px;
        }

        .task-page-intro {
            padding: 20px 18px;
            border-radius: 18px;
        }

        .task-page-title {
            font-size: 24px;
        }

        .task-form-card {
            padding: 20px 16px 18px;
            border-radius: 16px;
        }

        .task-section-head {
            margin-bottom: 20px;
            padding-bottom: 14px;
        }

        .task-action-wrap {
            margin-top: 22px;
        }

        .task-action-bar {
            flex-direction: column-reverse;
            gap: 10px;
        }

        .btn-cancel-task,
        .btn-create-task {
            width: 100%;
        }
    }
</style>
@endpush