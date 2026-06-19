@extends('layouts.master')

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
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
                                            <li class="breadcrumb-item"><a href="{{ route('admin.employee') }}">Employees</a>
                                            </li>
                                        @endif

                                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Profile</h3>
                                <p class="text-muted">Manage your personal information and settings.</p>
                            </div>

                            <div class="d-flex gap-2">
                                @if (Auth::user()->id === $employee?->user_id)
                                    <button class="btn btn-primary"
                                        onclick="window.location='{{ route('profile.settings.employee') }}'">
                                        <i class="bi bi-gear-fill me-2"></i>Profile Setting
                                    </button>
                                @endif

                                <button class="btn btn-primary"
                                    onclick="window.location='{{ route('profile.print', $employee->employee_id) }}'">
                                    <i class="bi bi-file-pdf me-2"></i>Download PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Top Two Cards - Equal Height -->
        <div class="row mb-4">
            <!-- Left Card - Profile Picture and Basic Info -->
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body text-center p-4">
                        {{-- If viewing employee → use employee’s user info
                        Else → fallback to logged-in user (own profile) 
                        only for columns in users table: name, email, role_id/role_name, user_id, profile_photo_url --}}
                        @php
                            $profileUser = $employee?->user ?? $user;
                        @endphp

                        @if ($employee)
                            <!-- Profile Picture with Hover Effect -->
                            <form method="POST" action="{{ route('admin.employee.updatePhoto', $employee->employee_id) }}"
                                enctype="multipart/form-data" id="profilePhotoForm">

                                @csrf
                                @method('PUT')

                                <div class="profile-pic mb-3 d-flex justify-content-center">
                                    <label class="profile-photo-wrapper position-relative">

                                        <img src="{{ $profileUser->profile_photo_path ? asset('storage/' . $profileUser->profile_photo_url) : asset('img/default-avatar.png') }}"
                                            alt="{{ $profileUser->name }}" class="rounded-circle"
                                            style="width:120px;height:120px;object-fit:cover;cursor:pointer;">

                                        <!-- Hover overlay -->
                                        @if (auth()->user()->role_id === 2)
                                            <div class="photo-overlay d-flex align-items-center justify-content-center">
                                                <i class="bi bi-camera-fill"></i>
                                            </div>

                                            <!-- Auto-submit when file selected -->
                                            <input type="file" name="profile_photo" class="d-none" accept="image/*"
                                                onchange="document.getElementById('profilePhotoForm').submit();">
                                        @endif
                                    </label>
                                </div>
                            </form>
                        @else
                            <div class="profile-pic mb-3 d-flex justify-content-center">
                                <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="rounded-circle"
                                    style="width:120px;height:120px;object-fit:cover;">
                            </div>
                        @endif

                        <!-- Full Name -->
                        <h4 class="employee-name">{{ $profileUser->name }}</h4>

                        <!-- Position -->
                        <p class="employee-position text-muted">{{ $employment->position ?? 'Staff' }}</p>

                        <!-- Divider Line -->
                        <hr class="my-3">

                        <!-- Employee ID and Date of Employment -->
                        <div class="employee-details small">
                            <div class="detail-row d-flex justify-content-between align-items-center mb-2">
                                <span class="detail-label text-muted">User ID:</span>
                                <span class="detail-value fw-semibold">
                                    {{ $profileUser->id ?? '-' }}
                                </span>
                            </div>

                            <div class="detail-row d-flex justify-content-between align-items-center">
                                <span class="detail-label text-muted">Role:</span>
                                <span class="detail-value fw-semibold">
                                    {{ $profileUser->role?->role_name ?? '-' }}
                                </span>
                            </div>

                            @if ($employee)
                                <div class="detail-row d-flex justify-content-between align-items-center mb-2">
                                    <span class="detail-label text-muted">Employee ID:</span>
                                    <span class="detail-value fw-semibold">
                                        {{ $employee->employee_id ?? '-' }}
                                    </span>
                                </div>

                                <div class="detail-row d-flex justify-content-between align-items-center mb-2">
                                    <span class="detail-label text-muted">Employment Status:</span>
                                    <span class="detail-value fw-semibold">
                                        {{ ucfirst($employment->status?->name ?? '-') }}
                                    </span>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Card - Contact Information -->
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h5 class="section-title mb-3 text-primary">
                            <i class="bi bi-telephone-fill me-3"></i>Contact Information
                        </h5>

                        <form class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted">Full Name</label>
                                <div class="contact-display d-flex align-items-center">
                                    <i class="bi bi-person text-muted me-2"></i>
                                    <span
                                        class="{{ empty($profileUser->name) ? 'text-muted fst-italic' : 'fw-semibold' }}">
                                        {{ $profileUser->name ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted">Email Address</label>
                                <div class="contact-display d-flex align-items-center">
                                    <i class="bi bi-envelope text-muted me-2"></i>
                                    <span class="{{ empty($profileUser->email) ? 'text-muted fst-italic' : '' }}">
                                        {{ $profileUser->email ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted">Phone Number</label>
                                <div class="contact-display d-flex align-items-center">
                                    <i class="bi bi-telephone text-muted me-2"></i>
                                    <span class="{{ empty($employee->phone_number) ? 'text-muted fst-italic' : '' }}">
                                        {{ $employee->phone_number ?? 'Not provided' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-semibold text-muted">Address</label>
                                <div class="contact-display d-flex align-items-start">
                                    <i class="bi bi-geo-alt text-muted me-2"></i>
                                    <span class="{{ empty($employee->address) ? 'text-muted fst-italic' : '' }}">
                                        {{ $employee->address ?? 'Not provided' }}
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                @if ($employee)
                    <!-- Tabs navigation -->
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab"
                                data-bs-target="#personal" type="button" role="tab" aria-controls="personal"
                                aria-selected="true">
                                Personal
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="employment-tab" data-bs-toggle="tab"
                                data-bs-target="#employment" type="button" role="tab" aria-controls="employment"
                                aria-selected="false">
                                Employment
                            </button>
                        </li>
                    </ul>
                @endif

            </div>

            @if ($employee)
                <!-- Tabs content -->
                <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm" id="profileTabsContent"
                    style="min-height: 500px;">
                    <!-- Personal tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel"
                        aria-labelledby="personal-tab">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title mb-3 text-primary">
                                    <i class="bi bi-person-fill me-3"></i>Personal Information
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Gender</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->gender) ? 'text-muted' : '' }}">
                                                {{ ucfirst($employee->gender ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Birthday</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->birthday) ? 'text-muted' : '' }}">
                                                {{ $employee->birthday ? $employee->birthday->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Marital Status</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->marital_status) ? 'text-muted' : '' }}">
                                                {{ ucfirst($employee->marital_status ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Nationality</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->nationality) ? 'text-muted' : '' }}">
                                                {{ ucfirst($employee->nationality ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Emergency Contact Name</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->emergency_contact_name) ? 'text-muted' : '' }}">
                                                {{ ucwords($employee->emergency_contact_name ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Emergency Contact Number</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->emergency_contact_number) ? 'text-muted' : '' }}">
                                                {{ $employee->emergency_contact_number ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Emergency Contact Relationship</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->emergency_contact_relationship) ? 'text-muted' : '' }}">
                                                {{ ucwords($employee->emergency_contact_relationship ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">IC Number</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->ic_number) ? 'text-muted' : '' }}">
                                                {{ $employee->ic_number ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Highest Education Level</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->highest_education_level) ? 'text-muted' : '' }}">
                                                {{ ucwords($employee->highest_education_level ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Highest Education Institution</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->highest_education_institution) ? 'text-muted' : '' }}">
                                                {{ ucwords($employee->highest_education_institution ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Graduation Year</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employee->graduation_year) ? 'text-muted' : '' }}">
                                                {{ $employee->graduation_year ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                @if (Auth::user()->role_id !== 2)
                                    <button class="btn btn-primary"
                                        onclick="window.location='{{ route('profile.editPersonal', $employee->employee_id) }}'">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Personal Details
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Employment Tab -->
                    <div class="tab-pane fade" id="employment" role="tabpanel" aria-labelledby="employment-tab">
                        <!-- Employment Information Row -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="section-title mb-3 text-primary">
                                    <i class="bi bi-briefcase-fill me-3"></i>Employment Information
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Employment Type</div>
                                            <div class="detail-value fw-semibold">
                                                {{ ucwords(str_replace('_', ' ', $employment->type?->name ?? '-')) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Date of Employment</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employment?->date_of_employment?->format('d M Y') ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Company Branch</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employment->branch?->name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Report To</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employment?->reportToEmployee?->full_name ?? '-' }}
                                                <span class="text-muted small">
                                                    ({{ $employment?->reportToEmployee?->employment?->position ?? 'N/A' }})
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Department</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employee->employment?->department?->name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Position</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employment->position ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Work Start</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employment?->work_start_time?->format('g:i A') ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Work End</div>
                                            <div class="detail-value fw-semibold">
                                                {{ $employment?->work_end_time?->format('g:i A') ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dates Information Row -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="section-title mb-3 text-primary">
                                    <i class="bi bi-calendar-fill me-3"></i>Dates Information
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Contract Start</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->contract_start) ? 'text-muted' : '' }}">
                                                {{ $employment?->contract_start ? $employment?->contract_start->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Contract End</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->contract_end) ? 'text-muted' : '' }}">
                                                {{ $employment?->contract_end ? $employment?->contract_end->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Probation Start</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->probation_start) ? 'text-muted' : '' }}">
                                                {{ $employment?->probation_start ? $employment?->probation_start->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Probation End</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->probation_end) ? 'text-muted' : '' }}">
                                                {{ $employment?->probation_end ? $employment?->probation_end->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Suspension Start</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->suspension_start) ? 'text-muted' : '' }}">
                                                {{ $employment?->suspension_start ? $employment?->suspension_start->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Suspension End</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->suspension_end) ? 'text-muted' : '' }}">
                                                {{ $employment?->suspension_end ? $employment?->suspension_end->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Resignation Date</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->resignation_date) ? 'text-muted' : '' }}">
                                                {{ $employment?->resignation_date ? $employment?->resignation_date->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Last Working Day</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->last_working_day) ? 'text-muted' : '' }}">
                                                {{ $employment?->last_working_day ? $employment?->last_working_day->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label text-muted small">Termination Date</div>
                                            <div
                                                class="detail-value fw-semibold {{ empty($employment->termination_date) ? 'text-muted' : '' }}">
                                                {{ $employment?->termination_date ? $employment?->termination_date->format('d M Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            @if (Auth::user()->role_id == 2)
                                <button class="btn btn-primary"
                                    onclick="window.location='{{ route('profile.editEmployment', $employee->employee_id) }}'">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Employment Details
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button'))
            triggerTabList.forEach(function(triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl)

                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault()
                    tabTrigger.show()
                })
            })
        });
    </script>
@endsection
