@extends('layouts.master')

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header w-100">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        <li class="breadcrumb-item active" aria-current="page">Work Handover Form</li>
                                    </ol>
                                </nav>

                                <h3 class="page-title"><br>Work Handover Form</h3>
                                <p class="text-muted">Complete the form to hand over your responsibilities.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form action="{{ route('form.work-handover.store') }}" method="POST">
                        @csrf

                        {{-- ================= A. EMPLOYEE DETAILS ================= --}}
                        <h5 class="mb-3">A. Employee Details</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee Name</label>
                                <input type="text" class="form-control"
                                    value="{{ auth()->user()->employee->full_name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employee ID</label>
                                <input type="text" class="form-control"
                                    value="{{ auth()->user()->employee->employee_id ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" class="form-control"
                                    value="{{ auth()->user()->employee->employment?->department?->name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Job / Position Title</label>
                                <input type="text" class="form-control"
                                    value="{{ auth()->user()->employee->employment?->position ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Handover Reason <span class="text-danger">*</span></label>
                            <select name="handover_reason" class="form-select" required>
                                <option value="">Select reason</option>
                                <option value="vacation">Vacation</option>
                                <option value="maternity">Maternity Leave</option>
                                <option value="transfer">Transfer</option>
                                <option value="end_employment">End of Employment</option>
                                <option value="others">Others</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Handover To <span class="text-danger">*</span></label>
                            <select name="handover_to" class="form-select" required>
                                <option value="">Select employee</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->employee_id }}">
                                        {{ $emp->full_name }} ({{ $emp->employment->department->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Handover Notes <span class="text-danger">*</span></label>
                            <textarea name="handover_notes" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Last Working Day <span class="text-danger">*</span></label>
                            <input type="date" name="last_working_day" class="form-control" required>
                        </div>

                        {{-- ================= B. TASK DETAILS ================= --}}
                        <h5 class="mb-3">B. Task(s) Details</h5>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Task</th>
                                    <th width="20%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            <input type="text" name="tasks[{{ $i }}][name]"
                                                class="form-control">
                                        </td>
                                        <td>
                                            <select name="tasks[{{ $i }}][status]" class="form-select">
                                                <option value="">Select</option>
                                                <option value="pending">Pending</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- ================= C. DOCUMENTS ================= --}}
                        <h5 class="mb-3">C. Binder / Box File / Documents</h5>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Binder / Box File / Document</th>
                                    <th>Short Description</th>
                                    <th width="30%">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td><input type="text" name="documents[{{ $i }}][name]"
                                                class="form-control"></td>
                                        <td><input type="text" name="documents[{{ $i }}][desc]"
                                                class="form-control"></td>
                                        <td><input type="text" name="documents[{{ $i }}][location]"
                                                class="form-control"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- ================= D. ELECTRONIC FILES ================= --}}
                        <h5 class="mb-3">D. Electronic Files</h5>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Description</th>
                                    <th width="30%">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td><input type="text" name="efiles[{{ $i }}][desc]"
                                                class="form-control"></td>
                                        <td><input type="text" name="efiles[{{ $i }}][location]"
                                                class="form-control"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- ================= E. PASSWORDS ================= --}}
                        <h5 class="mb-3">E. Passwords</h5>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>System</th>
                                    <th>Password</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td><input type="text" name="passwords[{{ $i }}][system]"
                                                class="form-control"></td>
                                        <td><input type="password" name="passwords[{{ $i }}][password]"
                                                class="form-control"></td>
                                        <td><input type="text" name="passwords[{{ $i }}][location]"
                                                class="form-control"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- ================= F. FINANCIAL COMMITMENTS ================= --}}
                        <h5 class="mb-3">F. Financial Commitments</h5>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Commitment</th>
                                    <th width="20%">Amount</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td><input type="text" name="commitments[{{ $i }}][desc]"
                                                class="form-control"></td>
                                        <td><input type="number" step="0.01"
                                                name="commitments[{{ $i }}][amount]" class="form-control">
                                        </td>
                                        <td><input type="text" name="commitments[{{ $i }}][remarks]"
                                                class="form-control"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- ================= G. INVENTORY ================= --}}
                        <h5 class="mb-3">G. Tools / Equipment</h5>

                        <table class="table table-bordered mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Description</th>
                                    <th width="15%">Qty</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 3; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td><input type="text" name="inventory[{{ $i }}][desc]"
                                                class="form-control"></td>
                                        <td><input type="number" name="inventory[{{ $i }}][qty]"
                                                class="form-control"></td>
                                        <td><input type="text" name="inventory[{{ $i }}][remarks]"
                                                class="form-control"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- ACTIONS --}}
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Submit Form
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
