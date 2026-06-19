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
                                        @if ($role_id == 2)
                                            <li class="breadcrumb-item"><a
                                                    href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                        @else
                                            <li class="breadcrumb-item"><a
                                                    href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                                        @endif

                                        @if ($role_id == 2)
                                            <li class="breadcrumb-item"><a
                                                    href="{{ route('event.index.admin') }}">Event</a></li>
                                        @else
                                            <li class="breadcrumb-item"><a
                                                    href="{{ route('event.index.employee') }}">Event</a></li>
                                        @endif
                                        
                                        <li class="breadcrumb-item active" aria-current="page">New Event</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Create Event</h3>
                                <p class="text-muted">Fill in the details below to create a new event.</p>
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
                <div class="card-body">
                    <form action="{{ route('event.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <!-- Event Name -->
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Event Name <span class="text-danger">*</span></label>
                            <input type="text" id="event_name" name="event_name"
                                class="form-control @error('event_name') is-invalid @enderror"
                                placeholder="Name of the event" value="{{ old('event_name') }}" required>
                            @error('event_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Date & Time -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label">Event Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="event_date" name="event_date"
                                    class="form-control @error('event_date') is-invalid @enderror"
                                    value="{{ old('event_date') }}" required>
                                @error('event_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="event_time" class="form-label">Event Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" id="event_time" name="event_time"
                                    class="form-control @error('event_time') is-invalid @enderror"
                                    value="{{ old('event_time') }}" required>
                                @error('event_time')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label for="event_location" class="form-label">Location <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="event_location" name="event_location"
                                class="form-control @error('event_location') is-invalid @enderror"
                                placeholder="Location of the event" value="{{ old('event_location') }}" required>
                            @error('event_location')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                rows="4" placeholder="Describe the event" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="event_category_id" class="form-label">Event Category <span
                                    class="text-danger">*</span></label>
                            <select id="event_category_id" name="event_category_id" class="form-select" required>
                                <option value="" disabled selected>Select Category</option>
                                @foreach ($eventCategoriesEnum as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('event_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ ucfirst($cat->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_category_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Event For <span class="text-danger">*</span>
                            </label>

                            <div class="border rounded p-3 bg-white">

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllEmployees">
                                        <label class="form-check-label fw-semibold" for="selectAllEmployees">
                                            All Employees
                                        </label>
                                        <small class="text-muted d-block">
                                            Assign this event to everyone in the company
                                        </small>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Add Department
                                    </label>
                                    <div class="row">
                                        @foreach ($departments as $dept)
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input department-checkbox" type="checkbox"
                                                        value="{{ $dept->id }}" id="dept_{{ $dept->id }}"
                                                        name="department_ids[]">
                                                    <label class="form-check-label" for="dept_{{ $dept->id }}">
                                                        {{ $dept->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        Add Individual Employee
                                    </label>
                                    <select id="employeeSearch" class="form-select">
                                        <option value="">Search employee...</option>
                                        @foreach ($allEmployees as $emp)
                                            <option value="{{ $emp['id'] }}" data-name="{{ $emp['name'] }}"
                                                data-dept="{{ $emp['department'] }}">
                                                {{ $emp['name'] }} ({{ $emp['department'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label text-muted">
                                        Assigned Employees
                                    </label>

                                    <div id="employeeList" class="border rounded p-3 bg-light"
                                        style="min-height: 120px;">
                                        <small class="text-muted">No employees selected</small>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Event Status -->
                        <div class="mb-3">
                            <label for="event_status" class="form-label">Event Status <span
                                    class="text-danger">*</span></label>
                            <select id="event_status" name="event_status"
                                class="form-select @error('event_status') is-invalid @enderror" required>
                                @foreach (['upcoming', 'ongoing', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}"
                                        {{ old('event_status', 'upcoming') === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags <small class="text-muted">(comma
                                    separated)</small></label>
                            <input type="text" id="tags" name="tags"
                                class="form-control @error('tags') is-invalid @enderror"
                                placeholder="e.g. tech, networking, free" value="{{ old('tags') }}">
                            @error('tags')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Event Image</label>
                            <input type="file" id="image" name="image"
                                class="form-control @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">JPG, JPEG or PNG (max 2 MB)</small>
                            @error('image')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ $role_id == 2 ? route('event.index.admin') : route('event.index.employee') }}"
                                class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedEmployees = new Map(); // id => {id, name, dept}
        let selectedDepartments = new Set();
        let departmentEmployees = new Map(); // deptId => Set(empIds)

        // Script to handle "Select All Employees" checkbox
        function toggleManualSelection(disabled) {
            document.querySelectorAll('.department-checkbox').forEach(cb => {
                cb.disabled = disabled;
                cb.checked = disabled;
            });

            document.getElementById('employeeSearch').disabled = disabled;
        }

        document.getElementById('selectAllEmployees').addEventListener('change', function() {
            selectedEmployees.clear();
            departmentEmployees.clear();

            if (this.checked) {
                toggleManualSelection(true);

                // fetch ALL employees
                fetch(`{{ route('employees.all') }}`)
                    .then(res => res.json())
                    .then(employees => {
                        employees.forEach(emp => {
                            selectedEmployees.set(emp.id, emp);
                        });
                        renderEmployees();
                    });

                // hidden input so backend knows this is ALL
                if (!document.getElementById('assign_all')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'assign_all';
                    input.value = '1';
                    input.id = 'assign_all';
                    document.querySelector('form').appendChild(input);
                }
            } else {
                toggleManualSelection(false);
                selectedEmployees.clear();
                renderEmployees();

                document.getElementById('assign_all')?.remove();
            }
        });

        // Script to render selected employees
        function renderEmployees() {
            const container = document.getElementById('employeeList');
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
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            onclick="removeEmployee('${emp.id}')">
                        Remove
                    </button>
                    <input type="hidden" name="employee_ids[]" value="${emp.id}">
                </div>
            `;
            });
        }

        function removeEmployee(id) {
            selectedEmployees.delete(id);
            renderEmployees();
        }

        // Script to add individual employees

        document.getElementById('employeeSearch').addEventListener('change', function() {
            const option = this.selectedOptions[0];
            if (!option.value) return;

            const emp = {
                id: option.value,
                name: option.dataset.name,
                department: option.dataset.dept
            };

            selectedEmployees.set(emp.id, emp);
            renderEmployees();
            this.value = '';
        });

        // Script to add employees by department

        document.querySelectorAll('.department-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const deptId = this.value;

                if (this.checked) {
                    fetch(`/departments/${deptId}/employees`)
                        .then(res => res.json())
                        .then(employees => {
                            const empIds = new Set();

                            employees.forEach(emp => {
                                selectedEmployees.set(emp.id, emp);
                                empIds.add(emp.id);
                            });

                            departmentEmployees.set(deptId, empIds);
                            renderEmployees();
                        });
                } else {
                    // remove employees added by this department
                    const empIds = departmentEmployees.get(deptId) || [];

                    empIds.forEach(id => selectedEmployees.delete(id));
                    departmentEmployees.delete(deptId);

                    renderEmployees();
                }
            });
        });
    </script>
@endpush
