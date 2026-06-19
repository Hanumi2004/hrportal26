@extends('layouts.master')

@section('content')
    <style>
        .employee-page {
            width: 100%;
            max-width: 100%;
            min-width: 0;
        }

        .employee-page,
        .employee-page * {
            box-sizing: border-box;
        }

        .employee-page .page-sub-header {
            width: 100%;
        }

        .employee-page .page-title {
            margin: 0;
            line-height: 1.2;
            white-space: normal;
        }

        .employee-page .page-sub-header .text-muted {
            line-height: 1.4;
        }

        .employee-page .employee-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .employee-page .employee-filter-grid>div {
            min-width: 0;
        }

        .employee-page .employee-filter-grid .input-group,
        .employee-page .employee-filter-grid .form-control,
        .employee-page .employee-filter-grid .form-select {
            min-width: 0;
        }

        .employee-page .desktop-table {
            display: block;
        }

        .employee-page .mobile-employee-card {
            display: none;
        }

        .employee-page .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .employee-page .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }

        .employee-page .table-responsive {
            min-width: 0;
        }

        .employee-page .table td,
        .employee-page .table th {
            vertical-align: middle;
        }

        .employee-page .employee-avatar {
            width: 38px;
            height: 38px;
            object-fit: cover;
        }

        .employee-page .mobile-avatar {
            width: 45px;
            height: 45px;
            object-fit: cover;
        }

        .employee-page .action-btn-group .btn {
            min-width: 34px;
        }

        .employee-page .security-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            margin-top: .35rem;
        }

        .employee-page .security-badges .badge {
            font-size: .7rem;
            font-weight: 600;
        }

        .employee-page .dropdown-menu form {
            display: block;
            margin: 0;
        }

        .employee-page .dropdown-menu .dropdown-item {
            cursor: pointer;
        }

        @media (max-width: 1199.98px) {
            .employee-page .employee-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .employee-page .desktop-table {
                display: none;
            }

            .employee-page .mobile-employee-card {
                display: block;
            }
        }

        @media (max-width: 767.98px) {
            .employee-page .employee-filter-grid {
                grid-template-columns: 1fr;
            }

            .employee-page .page-header .page-sub-header .d-flex {
                align-items: stretch !important;
            }

            .employee-page .page-header .btn {
                width: 100%;
            }

            .employee-page .page-title {
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="employee-page">
        <div class="content container-fluid">
            <div class="page-header mb-4">
                <div class="row">
                    <div class="col-12">
                        <div class="page-sub-header w-100">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 w-100">
                                <div>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb mb-1">
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                            </li>
                                            <li class="breadcrumb-item active" aria-current="page">Employee</li>
                                        </ol>
                                    </nav>

                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <h3 class="page-title fw-bold text-primary mb-0">Employee Management</h3>
                                        <p class="text-muted small mb-0">
                                            Manage your team members and their information
                                        </p>
                                    </div>
                                </div>

                                <div>
                                    <button class="btn btn-warning fw-bold px-4 shadow-sm"
                                        onclick="window.location='{{ route('admin.employee.create') }}'">
                                        <i class="bi bi-person-plus me-2"></i>Add New
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee') }}">
                        <div class="employee-filter-grid">
                            <div>
                                <label class="form-label small fw-bold">Search</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control border-start-0" placeholder="Name or ID...">
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-bold">Department</label>
                                <select name="department_name" class="form-select">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept }}"
                                            {{ request('department_name') == $dept ? 'selected' : '' }}>
                                            {{ $dept }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label small fw-bold">Status</label>
                                <select name="employment_status_id" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach ($employmentStatuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ request('employment_status_id') == $status->id ? 'selected' : '' }}>
                                            {{ ucfirst($status->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-grow-1" type="submit">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.employee') }}" class="btn btn-outline-secondary flex-grow-1">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-12 col-md-4 mb-3">
                    <div class="card stat-card h-100 border-0 shadow-sm border-start border-primary border-4"
                        onclick="window.location='{{ route('admin.employee') }}'" style="cursor:pointer;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold">Total Employees</h6>
                                    <h3 class="fw-bold">{{ $totalEmployees }}</h3>
                                </div>
                                <i class="bi bi-people-fill fs-1 text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-3">
                    <div class="card stat-card h-100 border-0 shadow-sm border-start border-warning border-4"
                        onclick="window.location='{{ route('admin.employee', ['filter' => 'ending']) }}'"
                        style="cursor:pointer;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold">Ending Soon</h6>
                                    <h3 class="fw-bold">{{ $employmentEnding }}</h3>
                                </div>
                                <i class="bi bi-exclamation-triangle fs-1 text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4 mb-3">
                    <div class="card stat-card h-100 border-0 shadow-sm border-start border-info border-4"
                        onclick="window.location='{{ route('admin.employee', ['filter' => 'new']) }}'"
                        style="cursor:pointer;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-muted small text-uppercase fw-bold">New Hires</h6>
                                    <h3 class="fw-bold">{{ $newThisMonth }}</h3>
                                </div>
                                <i class="bi bi-person-plus fs-1 text-info opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm desktop-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Employee</th>
                                    <th>ID</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Join Date</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    @php
                                        $user = $employee->user;
                                        $status = $employee->employment->status?->name ?? 'active';
                                        $color = match ($status) {
                                            'active' => 'success',
                                            'terminated' => 'danger',
                                            'on_leave' => 'warning',
                                            'probation' => 'info',
                                            default => 'secondary',
                                        };

                                        $twoFactorEnabled = $user &&
                                            !empty($user->two_factor_secret) &&
                                            (!property_exists($user, 'two_factor_confirmed_at') || $user->two_factor_confirmed_at !== null);
                                    @endphp

                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <img src="{{ $employee->user && $employee->user->profile_photo_path ? $employee->user->profile_photo_url : asset('img/default-avatar.png') }}"
                                                        class="rounded-circle shadow-sm employee-avatar"
                                                        alt="{{ $employee->full_name }}">
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $employee->full_name }}</div>
                                                    <small class="text-muted">{{ $employee->email }}</small>

                                                    <div class="security-badges">
                                                        @if ($user)
                                                            <span class="badge bg-light text-dark border">
                                                                User ID: {{ $user->id }}
                                                            </span>

                                                            @if ($twoFactorEnabled)
                                                                <span class="badge bg-warning text-dark">
                                                                    2FA Enabled
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    2FA Off
                                                                </span>
                                                            @endif

                                                            @if (!empty($user->force_password_reset))
                                                                <span class="badge bg-danger">
                                                                    Force Reset On
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-danger">
                                                                No user account
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $employee->employee_id }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $employee->employment->department->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $employee->employment->position ?? 'Staff' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span>
                                        </td>
                                        <td>
                                            {{ optional($employee->employment)->date_of_employment ? \Carbon\Carbon::parse($employee->employment->date_of_employment)->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group action-btn-group">
                                                <button class="btn btn-sm btn-outline-primary"
                                                    onclick="window.location='{{ route('profile.editEmployment', $employee->employee_id) }}'"
                                                    title="Edit Employee">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <button class="btn btn-sm btn-outline-info"
                                                    onclick="window.location='{{ route('profile.show', $employee->employee_id) }}'"
                                                    title="View Profile">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                @if ($user)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#resetPasswordModal{{ $user->id }}"
                                                        title="Reset Password">
                                                        <i class="bi bi-shield-lock"></i>
                                                    </button>

                                                    <form action="{{ route('admin.employee.disable2fa', $user->id) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Disable 2FA for {{ $employee->full_name }}?')">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-warning"
                                                            title="Disable 2FA"
                                                            {{ !$twoFactorEnabled ? 'disabled' : '' }}>
                                                            <i class="bi bi-phone-vibrate"></i>
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('admin.employee.forcePasswordReset', $user->id) }}"
                                                        method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @if ($user->force_password_reset)
                                                            <input type="hidden" name="action" value="reset">
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-success"
                                                                title="Clear Force Password Reset">
                                                                <i class="bi bi-unlock"></i>
                                                            </button>
                                                        @else
                                                            <input type="hidden" name="action" value="force">
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                title="Force Password Reset On Next Login">
                                                                <i class="bi bi-key"></i>
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($employees->count() === 0)
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No employee records found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mobile-employee-card">
                @foreach ($employees as $employee)
                    @php
                        $user = $employee->user;
                        $mobileStatus = $employee->employment->status?->name ?? 'active';
                        $twoFactorEnabled = $user &&
                            !empty($user->two_factor_secret) &&
                            (!property_exists($user, 'two_factor_confirmed_at') || $user->two_factor_confirmed_at !== null);
                    @endphp

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $employee->user && $employee->user->profile_photo_path ? $employee->user->profile_photo_url : asset('img/default-avatar.png') }}"
                                        class="rounded-circle me-3 mobile-avatar"
                                        alt="{{ $employee->full_name }}">
                                    <div>
                                        <div class="fw-bold fs-5">{{ $employee->full_name }}</div>
                                        <div class="text-muted small">ID: {{ $employee->employee_id }}</div>

                                        <div class="security-badges">
                                            @if ($user)
                                                @if ($twoFactorEnabled)
                                                    <span class="badge bg-warning text-dark">
                                                        2FA Enabled
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        2FA Off
                                                    </span>
                                                @endif

                                                @if (!empty($user->force_password_reset))
                                                    <span class="badge bg-danger">
                                                        Force Reset On
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('profile.editEmployment', $employee->employee_id) }}">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('profile.show', $employee->employee_id) }}">
                                                <i class="bi bi-eye me-2"></i>View Profile
                                            </a>
                                        </li>

                                        @if ($user)
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#resetPasswordModal{{ $user->id }}">
                                                    <i class="bi bi-shield-lock me-2"></i>Reset Password
                                                </button>
                                            </li>

                                            <li>
                                                <form action="{{ route('admin.employee.disable2fa', $user->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Disable 2FA for {{ $employee->full_name }}?')">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"
                                                        {{ !$twoFactorEnabled ? 'disabled' : '' }}>
                                                        <i class="bi bi-phone-vibrate me-2"></i>Disable 2FA
                                                    </button>
                                                </form>
                                            </li>

                                            <li>
                                                <form action="{{ route('admin.employee.forcePasswordReset', $user->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @if ($user->force_password_reset)
                                                        <input type="hidden" name="action" value="reset">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-unlock me-2"></i>Clear Force Reset
                                                        </button>
                                                    @else
                                                        <input type="hidden" name="action" value="force">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-key me-2"></i>Force Password Reset
                                                        </button>
                                                    @endif
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            <div class="row g-2 text-center border-top pt-3">
                                <div class="col-4 border-end">
                                    <div class="text-muted small mb-1">Dept</div>
                                    <div class="small fw-bold text-truncate">
                                        {{ $employee->employment->department->name ?? 'N/A' }}
                                    </div>
                                </div>
                                <div class="col-4 border-end">
                                    <div class="text-muted small mb-1">Status</div>
                                    <span
                                        class="badge bg-{{ match ($mobileStatus) {
                                            'active' => 'success',
                                            'probation' => 'info',
                                            'terminated' => 'danger',
                                            'on_leave' => 'warning',
                                            default => 'secondary',
                                        } }}">
                                        {{ ucfirst($mobileStatus) }}
                                    </span>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted small mb-1">Contact</div>
                                    <a href="tel:{{ $employee->phone_number }}"
                                        class="btn btn-sm btn-outline-success border-0">
                                        <i class="bi bi-telephone"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($employees->count() === 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-4">
                            No employee records found.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Reset Password Modals --}}
    @foreach ($employees as $employee)
        @if ($employee->user)
            <div class="modal fade" id="resetPasswordModal{{ $employee->user->id }}" tabindex="-1"
                aria-labelledby="resetPasswordModalLabel{{ $employee->user->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.employee.resetPassword', $employee->user->id) }}"
                            method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title"
                                    id="resetPasswordModalLabel{{ $employee->user->id }}">
                                    Reset Password - {{ $employee->full_name }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="alert alert-warning small">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    This will manually reset the employee password. Share the new password securely.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">New Password</label>
                                    <input type="password"
                                        name="password"
                                        class="form-control"
                                        minlength="8"
                                        required>
                                    <small class="text-muted">Minimum 8 characters.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Confirm Password</label>
                                    <input type="password"
                                        name="password_confirmation"
                                        class="form-control"
                                        minlength="8"
                                        required>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        name="force_password_reset"
                                        value="1"
                                        id="forcePasswordReset{{ $employee->user->id }}"
                                        checked>
                                    <label class="form-check-label"
                                        for="forcePasswordReset{{ $employee->user->id }}">
                                        Force employee to change password on next login
                                    </label>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                    class="btn btn-light border"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-shield-lock me-1"></i>Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection