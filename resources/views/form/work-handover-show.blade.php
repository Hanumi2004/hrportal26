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
                                <p class="text-muted">View submitted work handover form details.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FORM (READ-ONLY) --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div>

                        {{-- ================= A. EMPLOYEE DETAILS ================= --}}
                        <h5 class="mb-3">A. Employee Details</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employee Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $form->employee->full_name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employee ID</label>
                                <input type="text" class="form-control"
                                    value="{{ $form->employee->employee_id ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text" class="form-control"
                                    value="{{ $form->employee->department->name ?? '' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Job / Position Title</label>
                                <input type="text" class="form-control"
                                    value="{{ $form->employee->position ?? '' }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Handover Reason</label>
                            <input type="text" class="form-control"
                                value="{{ ucfirst(str_replace('_', ' ', $workHandover->handover_reason ?? '')) }}" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Handover To</label>
                            <input type="text" class="form-control"
                                value="{{ $workHandover->handoverTo->full_name ?? '' }}" readonly>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Handover Notes</label>
                            <textarea class="form-control" rows="3" readonly>{{ $workHandover->handover_notes ?? '' }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Last Working Day</label>
                            <input type="date" class="form-control"
                                value="{{ $workHandover->last_working_day ?? '' }}" readonly>
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
                                @php
                                    $tasks = $workHandover->tasks ?? [];
                                @endphp
                                @forelse ($tasks as $idx => $task)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $task['name'] ?? '' }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ ucfirst(str_replace('_', ' ', $task['status'] ?? '')) }}" readonly>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No tasks added</td>
                                    </tr>
                                @endforelse
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
                                @php
                                    $documents = $workHandover->documents ?? [];
                                @endphp
                                @forelse ($documents as $idx => $doc)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><input type="text" class="form-control" value="{{ $doc['name'] ?? '' }}" readonly></td>
                                        <td><input type="text" class="form-control" value="{{ $doc['desc'] ?? '' }}" readonly></td>
                                        <td><input type="text" class="form-control" value="{{ $doc['location'] ?? '' }}" readonly></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No documents added</td>
                                    </tr>
                                @endforelse
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
                                @php
                                    $efiles = $workHandover->electronic_files ?? [];
                                @endphp
                                @forelse ($efiles as $idx => $efile)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><input type="text" class="form-control" value="{{ $efile['desc'] ?? '' }}" readonly></td>
                                        <td><input type="text" class="form-control" value="{{ $efile['location'] ?? '' }}" readonly></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No files added</td>
                                    </tr>
                                @endforelse
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
                                @php
                                    $passwords = $workHandover->passwords ?? [];
                                @endphp
                                @forelse ($passwords as $idx => $pwd)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><input type="text" class="form-control" value="{{ $pwd['system'] ?? '' }}" readonly></td>
                                        <td><input type="password" class="form-control" value="{{ $pwd['password'] ?? '' }}" readonly></td>
                                        <td><input type="text" class="form-control" value="{{ $pwd['location'] ?? '' }}" readonly></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No passwords added</td>
                                    </tr>
                                @endforelse
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
                                @php
                                    $commitments = $workHandover->financial_commitments ?? [];
                                @endphp
                                @forelse ($commitments as $idx => $commitment)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><input type="text" class="form-control" value="{{ $commitment['desc'] ?? '' }}" readonly></td>
                                        <td><input type="number" step="0.01" class="form-control" value="{{ $commitment['amount'] ?? '' }}" readonly></td>
                                        <td><input type="text" class="form-control" value="{{ $commitment['remarks'] ?? '' }}" readonly></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No commitments added</td>
                                    </tr>
                                @endforelse
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
                                @php
                                    $inventory = $workHandover->inventory ?? [];
                                @endphp
                                @forelse ($inventory as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td><input type="text" class="form-control" value="{{ $item['desc'] ?? '' }}" readonly></td>
                                        <td><input type="number" class="form-control" value="{{ $item['qty'] ?? '' }}" readonly></td>
                                        <td><input type="text" class="form-control" value="{{ $item['remarks'] ?? '' }}" readonly></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items added</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- ACTIONS --}}
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="badge bg-{{ match($form->form_status) { 'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger' } }}">
                                    {{ ucfirst($form->form_status) }} - Level {{ $form->approval_level }}
                                </span>
                            </div>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
