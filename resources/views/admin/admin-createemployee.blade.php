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
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="{{ route('admin.employee') }}">Employee</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">New Employee</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>New Employee</h3>
                                <p class="text-muted">Create new employee.</p>
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

                    <form action="{{ route('admin.employee.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Enter employee full name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee ID <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="employee_id" name="employee_id" class="form-control"
                                placeholder="Enter employee ID" value="{{ old('employee_id') }}" required>
                            @error('employee_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="Enter employee email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                A verification email will be sent to this email address.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select id="role_id" name="role_id" class="form-select" required>
                                <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>
                                    Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $role->id === 1 ? 'disabled' : '' }}
                                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror

                            <small class="text-muted">
                                Role determines system access level.
                            </small>
                        </div>

                        <div class="mb-3 bg-yellow-50 p-3 rounded text-sm text-yellow-800">
                            A temporary password will be generated automatically and emailed to the user.
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('admin.employee') }}" class="btn btn-secondary me-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Create Employee
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
