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
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('admin.employee') }}">Employees</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Edit Employment</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Edit Employment Details</h3>
                                <p class="text-muted">Update employee's employment details below.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card">
                <div class="card-body justify-content-between">
                    {{-- makes content flexible row-pushes text left, icon right --}}

                    <form action="{{ route('profile.updateEmployment', $employee->employee_id) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="col-md-12 mb-3">
                            {{-- mb-3 = margin-bottom 1rem
                            mt-3 = margin-top 1rem
                            g-3 = gap 1rem --}}
                            <label for="employment_id" class="form-label">Employee ID <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="employee_id" name="employee_id" class="form-control"
                                placeholder="Enter your employee ID"
                                value="{{ old('employee_id', $employee->employee_id) }}" required>
                            {{-- Using for="event_name" links the label to the input’s id, so clicking the label focuses the input. --}}
                            @error('employee_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" id="position" name="position" class="form-control"
                                    placeholder="Enter Position" value="{{ old('position', $employment?->position) }}">
                                @error('position')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_of_employment" class="form-label">Date of Employment</label>
                                <input type="date" id="date_of_employment" name="date_of_employment" class="form-control"
                                    value="{{ old('date_of_employment', $employment?->date_of_employment?->format('Y-m-d')) }}">
                                @error('date_of_employment')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="department_id" class="form-label">Department <span
                                    class="text-danger">*</span></label>
                            <select id="department_id" name="department_id" class="form-select" required>
                                <option value="" disabled
                                    {{ old('department_id', $employment?->department_id) ? '' : 'selected' }}>
                                    Select department
                                </option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id', $employment?->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ ucfirst($department->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="company_branch_id" class="form-label">Company Branch <span
                                    class="text-danger">*</span></label>
                            <select id="company_branch_id" name="company_branch_id" class="form-select" required>
                                <option value="" disabled {{ old('company_branch_id') ? '' : 'selected' }}>Select
                                    branch
                                </option>
                                @foreach ($companyBranches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('company_branch_id', $employment?->company_branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ ucfirst($branch->name) }}</option>
                                @endforeach
                            </select>
                            @error('company_branch_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="report_to" class="form-label">Report To <span class="text-danger">*</span></label>
                            <select id="report_to" name="report_to" class="form-select" required>
                                <option value="" disabled {{ old('report_to') ? '' : 'selected' }}>Select
                                    employee
                                </option>
                                @php
                                    $employees = \App\Models\Employee::where(
                                        'employee_id',
                                        '!=',
                                        $employee->employee_id,
                                    )->get();
                                @endphp
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->employee_id }}"
                                        {{ old('report_to', $employment?->report_to) === $emp->employee_id ? 'selected' : '' }}>
                                        {{ ucfirst($emp->full_name) }}</option>
                                @endforeach
                            </select>
                            @error('report_to')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="work_start_time" class="form-label">Work Start Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" id="work_start_time" name="work_start_time" class="form-control"
                                    value="{{ old('work_start_time', $employment?->work_start_time?->format('H:i')) }}"
                                    required>
                                @error('work_start_time')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="work_end_time" class="form-label">Work End Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" id="work_end_time" name="work_end_time" class="form-control"
                                    value="{{ old('work_end_time', $employment?->work_end_time?->format('H:i')) }}"
                                    required>
                                @error('work_end_time')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="employment_type_id" class="form-label">
                                Employment Type <span class="text-danger">*</span>
                            </label>
                            <select id="employment_type_id" name="employment_type_id" class="form-select" required>
                                <option value="" disabled {{ old('employment_type_id') ? '' : 'selected' }}>
                                    Select type
                                </option>
                                @foreach ($employmentTypes as $type)
                                    <option value="{{ $type->id }}" data-name="{{ strtolower($type->name) }}"
                                        {{ old('employment_type_id', $employment?->employment_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ ucfirst($type->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employment_type_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="employment_status_id" class="form-label">Employment Status <span
                                    class="text-danger">*</span></label>
                            <select id="employment_status_id" name="employment_status_id" class="form-select" required>
                                <option value="" disabled {{ old('employment_status_id') ? '' : 'selected' }}>Select
                                    status
                                </option>
                                @foreach ($employmentStatuses as $status)
                                    <option value="{{ $status->id }}" data-name="{{ strtolower($status->name) }}"
                                        {{ old('employment_status_id', $employment?->employment_status_id) == $status->id ? 'selected' : '' }}>
                                        {{ ucfirst($status->name) }}</option>
                                @endforeach
                            </select>
                            @error('employment_status_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3 date-group" data-type="contract intern">
                            <div class="col-md-6">
                                <label for="contract_start" class="form-label">Contract Start</label>
                                <input type="date" id="contract_start" name="contract_start" class="form-control"
                                    value="{{ old('contract_start', $employment?->contract_start) }}">
                                @error('contract_start')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contract_end" class="form-label">Contract End</label>
                                <input type="date" id="contract_end" name="contract_end" class="form-control"
                                    value="{{ old('contract_end', $employment?->contract_end) }}">
                                @error('contract_end')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3 date-group" data-status="probation">
                            <div class="col-md-6">
                                <label for="probation_start" class="form-label">Probation Start</label>
                                <input type="date" id="probation_start" name="probation_start" class="form-control"
                                    value="{{ old('probation_start', $employment?->probation_start) }}">
                                @error('probation_start')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="probation_end" class="form-label">Probation End</label>
                                <input type="date" id="probation_end" name="probation_end" class="form-control"
                                    value="{{ old('probation_end', $employment?->probation_end) }}">
                                @error('probation_end')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3 date-group" data-status="suspended">
                            <div class="col-md-6">
                                <label for="suspension_start" class="form-label">Suspension Start</label>
                                <input type="date" id="suspension_start" name="suspension_start" class="form-control"
                                    value="{{ old('suspension_start', $employment?->suspension_start) }}">
                                @error('suspension_start')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="suspension_end" class="form-label">Suspension End</label>
                                <input type="date" id="suspension_end" name="suspension_end" class="form-control"
                                    value="{{ old('suspension_end', $employment?->suspension_end) }}">
                                @error('suspension_end')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3 date-group" data-status="resigned">
                            <div class="col-md-6">
                                <label for="resignation_date" class="form-label">Resignation Date</label>
                                <input type="date" id="resignation_date" name="resignation_date" class="form-control"
                                    value="{{ old('resignation_date', $employment?->resignation_date) }}">
                                @error('resignation_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_working_day" class="form-label">Last Working Day</label>
                                <input type="date" id="last_working_day" name="last_working_day" class="form-control"
                                    value="{{ old('last_working_day', $employment?->last_working_day) }}">
                                @error('last_working_day')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3 date-group" data-status="terminated">
                            <div class="col-md-12">
                                <label for="termination_date" class="form-label">Termination Date</label>
                                <input type="date" id="termination_date" name="termination_date" class="form-control"
                                    value="{{ old('termination_date', $employment?->termination_date) }}">
                                @error('termination_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('profile.show', $employee->employee_id) }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            {{-- later add if/else for employee/admin --}}
                            <button type="submit" class="btn btn-primary">
                                Update Profile
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('request.approvers.store', $employee) }}">
                        @csrf

                        {{-- LEVEL 0 (ADMIN – READ ONLY) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Level 0 Approver</label>
                                <input type="text" class="form-control" value="Admin" disabled>
                                <small class="text-muted">
                                    Default system approver (cannot be changed)
                                </small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="level1_approver" class="form-label">
                                    Level 1 Approver <span class="text-danger">*</span>
                                </label>
                                <select name="approvers[0][id]" class="form-control" required>
                                    <option value="" disabled
                                        {{ old('approvers.0.id', $level1Approver?->employee_id) ? '' : 'selected' }}>
                                        Select approver 1
                                    </option>
                                   @foreach ($approverCandidates as $approver)
										<option value="{{ $approver->employee_id }}"
											{{ old('approvers.0.id', $level1Approver?->employee_id) == $approver->employee_id ? 'selected' : '' }}>
											{{ $approver->full_name }} 
											({{ $approver->user->role_id == 4 ? 'Manager' : 'Staff' }})
										</option>
									@endforeach
                                </select>
                                <input type="hidden" name="approvers[0][level]" value="1">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="level2_approver" class="form-label">
                                    Level 2 Approver <span class="text-muted">(Optional)</span>
                                </label>
                                <select name="approvers[1][id]" class="form-control">
                                    <option value="" disabled
                                        {{ old('approvers.1.id', $level2Approver?->employee_id) ? '' : 'selected' }}>
                                        Select approver 2
                                    </option>
                                    @foreach ($approverCandidates as $approver)
                                        <option value="{{ $approver->employee_id }}"
                                            {{ old('approvers.1.id', $level2Approver?->employee_id) == $approver->employee_id ? 'selected' : '' }}>
                                            {{ $approver->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="approvers[1][level]" value="2">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('profile.show', $employee->employee_id) }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            {{-- later add if/else for employee/admin --}}
                            <button type="submit" class="btn btn-primary">
                                Update Approvers
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('employment_type_id');
            const statusSelect = document.getElementById('employment_status_id');
            const groups = document.querySelectorAll('.date-group');

            function updateVisibility() {
                const type = typeSelect.selectedOptions[0]?.dataset.name || '';
                const status = statusSelect.selectedOptions[0]?.dataset.name || '';

                groups.forEach(group => {
                    const types = group.dataset.type?.split(' ') || [];
                    const statuses = group.dataset.status?.split(' ') || [];

                    const showByType = types.length === 0 || types.includes(type);
                    const showByStatus = statuses.length === 0 || statuses.includes(status);

                    group.style.display = (showByType && showByStatus) ? '' : 'none';
                });
            }

            typeSelect.addEventListener('change', updateVisibility);
            statusSelect.addEventListener('change', updateVisibility);

            // 🔥 Run once on page load (EDIT form support)
            updateVisibility();
        });
    </script>
@endpush
