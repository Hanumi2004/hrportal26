@extends('layouts.master')

@section('content')
    <style>
        body {
            background-color: #f4f8fb;
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }

        .card:hover {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.08);
        }

        .card-body b {
            font-size: 1.5rem;
            font-weight: 600;
            color: #3498db;
        }

        .card-body g {
            font-size: 1.5rem;
            font-weight: 600;
            color: #40d15d;
        }

        .card-body y {
            font-size: 1.5rem;
            font-weight: 600;
            color: #edd641;
        }

        .btn-info {
            background-color: #5dade2;
            border-color: #5dade2;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 500;
        }

        .btn-info:hover {
            background-color: #3498db;
            border-color: #3498db;
        }

        .card-title {
            font-weight: 600;
            color: #2980b9;
            font-size: 1.25rem;
        }

        .card-header p {
            color: #7f8c8d;
            margin-top: 5px;
            font-size: 0.95rem;
        }
    </style>

    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="{{ route('leave.index.employee') }}">Leave</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Apply Leave</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Apply for Leave</h3>
                                <p class="text-muted">Submit your leave request for supervisor approval.</p>
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

                    <form action="{{ route('leave.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        {{-- enctype tells the browser to send the request body in multipart MIME format, necessary for file uploads --}}
                        @csrf

                        <div class="mb-3">
                            <label for="leave_entitlement_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select id="leave_entitlement_id" name="leave_entitlement_id" class="form-select" required>
                                <option value="" disabled selected>Select Type</option>
                                @foreach ($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_entitlement_id') == $type->id ? 'selected' : '' }}>
                                        {{ ucfirst($type->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_entitlement_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="leave_length" class="form-label">Leave Length <span
                                    class="text-danger">*</span></label>
                            <select id="leave_length" name="leave_length" class="form-select" required>
                                <option value="" disabled selected>Select Length</option>
                                @foreach ($leaveLengthEnum as $length)
                                    <option value="{{ $length }}"
                                        {{ old('leave_length') == $length ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $length)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_length')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="leave_reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea id="leave_reason" name="leave_reason" rows="3" class="form-control"
                                placeholder="Describe the reason for your leave" required>{{ old('leave_reason') }}</textarea>
                            @error('leave_reason')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label">Supporting Document (optional)</label>
                            <input type="file" id="attachment" name="attachment" class="form-control"
                                accept=".pdf,image/*" capture="environment">
                            <small class="text-muted">PDF or image (max 2 MB)</small>
                            @error('attachment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('leave.index.employee') }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
