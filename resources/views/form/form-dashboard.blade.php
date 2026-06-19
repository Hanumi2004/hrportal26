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
                                        @if ($viewType === 'employee')
                                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        @elseif($viewType === 'approver')
                                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        @else
                                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        @endif
                                        <li class="breadcrumb-item active" aria-current="page">Form</li>
                                    </ol>
                                </nav>

                                <h3 class="page-title"><br>Form</h3>
                                @if ($viewType === 'employee')
                                    <p class="text-muted">Submit and track your form requests.</p>
                                @elseif($viewType === 'approver')
                                    <p class="text-muted">Review and approve pending form requests.</p>
                                @else
                                    <p class="text-muted">Manage all form submissions and approvals.</p>
                                @endif
                            </div>

                            {{-- Employee only --}}
                            @if ($viewType === 'employee')
                                {{-- Create Form Dropdown --}}
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-plus-circle me-2"></i>New Form
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('form.work-handover.create') }}">
                                                <i class="bi bi-arrow-repeat me-2"></i>Work Handover Form
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item disabled">
                                                <i class="bi bi-lock me-2"></i>Asset Loan Form (Coming Soon)
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border border-top-0 rounded-bottom p-4 bg-white shadow-sm" style="min-height: 500px;">

        <!-- Filters and Search -->
        <form method="GET" action="{{ route('employee.myrequests') }}">
            <input type="hidden" name="tab" class="active-tab-input" value="leave-request">
            <div class="row g-2 align-items-end">
                @if ($viewType === 'admin')
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Search Employees</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Name or ID...">
                        </div>
                    </div>
                @endif

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label">Form Type</label>
                    <select name="form_type" class="form-control">
                        <option value="">All Form Types</option>
                        @foreach ($formTypes as $type)
                            <option value="{{ $type }}" {{ request('form_type') == $type ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $type)) }} Form
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label">Applied Date</label>
                    <input type="date" name="created_at" value="{{ request('created_at') }}" class="form-control">
                </div>

                <div class="col-12 col-sm-6 col-lg-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
                <div class="col-12 col-sm-6 col-lg-1">
                    <a href="{{ $filterRoute }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="card-title mt-4">Pending Form Requests</div>

        <div class="table-responsive">
            <table class="table text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Applied Date</th>
                        <th>Employee</th>
                        <th>Form Type</th>
                        <th>Status</th>
                        <th>Approval Level</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pendingForms as $form)
                        <tr>
                            <td>{{ $form->created_at->format('d M Y') }}</td>
                            <td>{{ $form->employee->full_name ?? '-' }}
                                <br>
                                <small class="text-muted">{{ $form->employee_id }}</small>
                            </td>
                            <td>{{ ucwords(str_replace('_', ' ', $form->form_type)) }} Form</td>
                            <td>
                                @php
                                    $badge = match ($form->form_status) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">
                                    {{ ucfirst($form->form_status) }}
                                </span>
                            </td>
                            <td>
                                Level {{ $form->approval_level }}
                            </td>
                            <td>
                                {{-- Employee: submitted their own form --}}
                                @if ($viewType === 'employee' && auth()->user()->employee->employee_id === $form->employee_id)
                                    <a href="{{ route('form.show', $form->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                @elseif ($viewType === 'approver' && $form->form_status === 'pending')
                                    {{-- Approver: can approve/reject --}}
                                    <a href="{{ route('form.show', $form->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('form.updateStatus', $form->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" name="action"
                                            value="approved">
                                            <i class="bi bi-check-circle me-1"></i>Approve
                                        </button>
                                        <button type="submit" class="btn btn-sm btn-danger" name="action"
                                            value="rejected">
                                            <i class="bi bi-x-circle me-1"></i>Reject
                                        </button>
                                    </form>
                                @elseif ($viewType === 'admin')
                                    {{-- Admin: can approve/reject and assign approvers --}}
                                    @if ($form->form_status === 'pending')
                                        <a href="{{ route('form.show', $form->id) }}" class="btn btn-sm btn-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#assignApproversModal{{ $form->id }}"
                                            title="Assign Approvers">
                                            <i class="bi bi-person-check"></i>
                                        </button>
                                        <form action="{{ route('form.updateStatus', $form->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" name="action"
                                                value="approved">
                                                <i class="bi bi-check-circle me-1"></i>Approve
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-danger" name="action"
                                                value="rejected">
                                                <i class="bi bi-x-circle me-1"></i>Reject
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No pending form</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODALS: Form Details --}}
    @foreach ($pendingForms as $form)
        <div class="modal fade" id="formDetailsModal{{ $form->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Form Type</th>
                                <td>{{ ucwords(str_replace('_', ' ', $form->form_type)) }}</td>
                            </tr>
                            <tr>
                                <th>Employee</th>
                                <td>{{ $form->employee->full_name }} ({{ $form->employee_id }})</td>
                            </tr>
                            <tr>
                                <th>Submitted</th>
                                <td>{{ $form->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @php
                                        $badge = match ($form->form_status) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($form->form_status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Approval Level</th>
                                <td>Level {{ $form->approval_level }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- MODALS: Assign Approvers (Admin Only) --}}
    @if ($viewType === 'admin')
        @foreach ($pendingForms as $form)
            <div class="modal fade" id="assignApproversModal{{ $form->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Assign Approvers for {{ $form->employee->full_name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('form.approvers.store', $form->employee->employee_id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p class="text-muted small">
                                    Optionally assign approvers for this employee's forms. Admin approval is always required
                                    at Level 0.
                                </p>

                                <div id="approvers-container">
                                    {{-- Check existing form approvers --}}
                                    @php
                                        $existingApprovers = $form->employee->formApprovers ?? collect();
                                    @endphp

                                    @if ($existingApprovers->count() > 0)
                                        @foreach ($existingApprovers as $approver)
                                            <div class="approver-row mb-3 p-3 border rounded">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <label class="form-label">Approver</label>
                                                        <select name="approvers[{{ $loop->index }}][id]"
                                                            class="form-select">
                                                            <option value="">-- Select Approver --</option>
                                                            @foreach ($allEmployees as $emp)
                                                                <option value="{{ $emp->employee_id }}"
                                                                    {{ $emp->employee_id == $approver->pivot->approver_id ? 'selected' : '' }}>
                                                                    {{ $emp->full_name }} ({{ $emp->employee_id }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Level</label>
                                                        <input type="number"
                                                            name="approvers[{{ $loop->index }}][level]"
                                                            class="form-control" value="{{ $approver->pivot->level }}"
                                                            min="1" placeholder="Level">
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-end">
                                                        <button type="button" class="btn btn-sm btn-danger w-100"
                                                            onclick="this.parentElement.parentElement.parentElement.remove()">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="approver-row mb-3 p-3 border rounded">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label class="form-label">Approver</label>
                                                    <select name="approvers[0][id]" class="form-select">
                                                        <option value="">-- Select Approver --</option>
                                                        @foreach ($allEmployees as $emp)
                                                            <option value="{{ $emp->employee_id }}">
                                                                {{ $emp->full_name }} ({{ $emp->employee_id }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Level</label>
                                                    <input type="number" name="approvers[0][level]" class="form-control"
                                                        placeholder="Level" min="1" value="1">
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end">
                                                    <button type="button" class="btn btn-sm btn-danger w-100"
                                                        onclick="this.parentElement.parentElement.parentElement.remove()">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" class="btn btn-sm btn-secondary mt-3" onclick="addApproverRow()">
                                    <i class="bi bi-plus-circle me-1"></i>Add Another Approver
                                </button>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Approvers</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection

@section('scripts')
    <script>
        let approverIndex = 1;

        function addApproverRow() {
            const container = document.getElementById('approvers-container');
            const rowCount = container.querySelectorAll('.approver-row').length;
            const newIndex = rowCount;

            const newRow = document.createElement('div');
            newRow.className = 'approver-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Approver</label>
                        <select name="approvers[${newIndex}][id]" class="form-select">
                            <option value="">-- Select Approver --</option>
                            @foreach ($allEmployees as $emp)
                                <option value="{{ $emp->employee_id }}">{{ $emp->full_name }} ({{ $emp->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Level</label>
                        <input type="number" name="approvers[${newIndex}][level]" class="form-control" placeholder="Level" min="1" value="${newIndex + 1}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-danger w-100" onclick="this.parentElement.parentElement.parentElement.remove()">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
        }
    </script>
@endsection
