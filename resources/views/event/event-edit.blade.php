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
                                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                            </li>
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

                                        <li class="breadcrumb-item active" aria-current="page">Edit Event</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Edit Event</h3>
                                <p class="text-muted">Edit the details of the event below.</p>
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
                    <form action="{{ route('event.update', $event->id) }}" method="POST" enctype="multipart/form-data"
                        novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Event Name -->
                        <div class="mb-3">
                            <label for="event_name" class="form-label">Event Name <span class="text-danger">*</span></label>
                            <input type="text" id="event_name" name="event_name"
                                class="form-control @error('event_name') is-invalid @enderror"
                                placeholder="Name of the event" value="{{ old('event_name', $event->event_name) }}"
                                required>
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
                                    value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}" required>
                                @error('event_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="event_time" class="form-label">Event Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" id="event_time" name="event_time"
                                    class="form-control @error('event_time') is-invalid @enderror"
                                    value="{{ old('event_time', $event->event_time->format('H:i')) }}" required>
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
                                placeholder="Location of the event"
                                value="{{ old('event_location', $event->event_location) }}" required>
                            @error('event_location')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span
                                    class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                rows="4" placeholder="Describe the event" required>{{ old('description', $event->description) }}</textarea>
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
                                            {{ old('event_category_id', $event->event_category_id) == $cat->id ? 'selected' : '' }}>
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
                                                        value="{{ $dept->id }}" name="department_ids[]"
                                                        {{ in_array($dept->id, $selectedDepartmentIds) ? 'checked' : '' }}>
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
                                        {{ old('event_status', $event->event_status) === $status ? 'selected' : '' }}>
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
                                placeholder="e.g. tech, networking, free" value="{{ old('tags', $event->tags) }}">
                            @error('tags')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Image Upload --}}
                        <div class="mb-3">
                            <label class="form-label">Event Image</label>

                            {{-- Preview container --}}
                            <div id="imagePreviewWrapper" class="mb-2">
                                @if ($event->image)
                                    <div class="position-relative d-inline-block">
                                        <img id="imagePreview" src="{{ Storage::url($event->image) }}"
                                            class="img-thumbnail" style="max-height: 180px;">

                                        <button type="button" id="removeImageBtn"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0">
                                            ✕
                                        </button>
                                    </div>
                                @else
                                    <img id="imagePreview" class="img-thumbnail d-none" style="max-height: 180px;">
                                @endif
                            </div>

                            {{-- File input --}}
                            <input type="file" name="image" id="imageInput" class="form-control"
                                accept=".jpg,.jpeg,.png">

                            <small class="text-muted">JPG, JPEG or PNG (max 2 MB)</small>

                            {{-- Hidden input to mark deletion --}}
                            <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                            @error('image')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Upload a new image to replace the existing one
                            </small>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ $role_id == 2 ? route('event.index.admin') : route('event.index.employee') }}"
                                class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* ===============================
                       PRELOADED DATA FROM BACKEND
                    ================================ */
        const preloadEmployees = @json($assignedEmployees ?? []);
        const preselectedDepartments = @json($selectedDepartmentIds ?? []);
        const assignAll = @json($event->assign_all ?? false);

        /* ===============================
           STATE
        ================================ */
        let selectedEmployees = new Map(); // empId => {id, name, department}
        let departmentEmployees = new Map(); // deptId => Set(empIds)

        /* ===============================
           DOM ELEMENTS
        ================================ */
        const selectAllCheckbox = document.getElementById('selectAllEmployees');
        const employeeList = document.getElementById('employeeList');
        const employeeSearch = document.getElementById('employeeSearch');
        const departmentCheckboxes = document.querySelectorAll('.department-checkbox');

        /* ===============================
           HELPERS
        ================================ */
        function toggleManualSelection(disabled) {
            departmentCheckboxes.forEach(cb => {
                cb.disabled = disabled;
                cb.checked = disabled;
            });
            employeeSearch.disabled = disabled;
        }

        function renderEmployees() {
            employeeList.innerHTML = '';

            if (selectedEmployees.size === 0) {
                employeeList.innerHTML = '<small class="text-muted">No employees selected</small>';
                return;
            }

            selectedEmployees.forEach(emp => {
                employeeList.innerHTML += `
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

        /* ===============================
           INIT: PRELOAD SAVED DATA
        ================================ */
        // preload individual + department employees
        preloadEmployees.forEach(emp => {
            selectedEmployees.set(String(emp.id), emp);
        });

        // restore departments
        preselectedDepartments.forEach(deptId => {
            const checkbox = document.querySelector(`.department-checkbox[value="${deptId}"]`);
            if (checkbox) checkbox.checked = true;
        });

        // restore ALL employees
        if (assignAll) {
            selectAllCheckbox.checked = true;
            toggleManualSelection(true);

            fetch(`{{ route('employees.all') }}`)
                .then(res => res.json())
                .then(employees => {
                    employees.forEach(emp => selectedEmployees.set(String(emp.id), emp));
                    renderEmployees();
                });

            // hidden input
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'assign_all';
            input.value = '1';
            input.id = 'assign_all';
            document.querySelector('form').appendChild(input);
        } else {
            renderEmployees();
        }

        /* ===============================
           EVENTS
        ================================ */

        // Select All Employees
        selectAllCheckbox.addEventListener('change', function() {
            selectedEmployees.clear();
            departmentEmployees.clear();

            if (this.checked) {
                toggleManualSelection(true);

                fetch(`{{ route('employees.all') }}`)
                    .then(res => res.json())
                    .then(employees => {
                        employees.forEach(emp => selectedEmployees.set(String(emp.id), emp));
                        renderEmployees();
                    });

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

        // Add individual employee
        employeeSearch.addEventListener('change', function() {
            const option = this.selectedOptions[0];
            if (!option.value) return;

            selectedEmployees.set(option.value, {
                id: option.value,
                name: option.dataset.name,
                department: option.dataset.dept
            });

            renderEmployees();
            this.value = '';
        });

        // Add/remove department employees
        departmentCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const deptId = this.value;

                if (this.checked) {
                    fetch(`/departments/${deptId}/employees`)
                        .then(res => res.json())
                        .then(employees => {
                            const empIds = new Set();

                            employees.forEach(emp => {
                                selectedEmployees.set(String(emp.id), emp);
                                empIds.add(String(emp.id));
                            });

                            departmentEmployees.set(deptId, empIds);
                            renderEmployees();
                        });
                } else {
                    const empIds = departmentEmployees.get(deptId) || [];
                    empIds.forEach(id => selectedEmployees.delete(id));
                    departmentEmployees.delete(deptId);
                    renderEmployees();
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const removeBtn = document.getElementById('removeImageBtn');
            const removeInput = document.getElementById('removeImageInput');
            const previewWrapper = document.getElementById('imagePreviewWrapper');

            // ===== Image selection preview =====
            imageInput?.addEventListener('change', function() {
                const file = this.files[0];

                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');

                    // If image replaced, reset remove flag
                    removeInput.value = 0;

                    // If remove button doesn't exist yet, create it
                    if (!document.getElementById('removeImageBtn')) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.id = 'removeImageBtn';
                        btn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
                        btn.innerHTML = '✕';

                        btn.addEventListener('click', removeImage);

                        imagePreview.parentElement.classList.add('position-relative');
                        imagePreview.parentElement.appendChild(btn);
                    }
                };

                reader.readAsDataURL(file);
            });

            // ===== Remove image =====
            function removeImage() {
                imagePreview.src = '';
                imagePreview.classList.add('d-none');

                imageInput.value = '';
                removeInput.value = 1;

                this.remove();
            }

            // Attach remove handler if button exists on load
            if (removeBtn) {
                removeBtn.addEventListener('click', removeImage);
            }
        });
    </script>
@endpush
