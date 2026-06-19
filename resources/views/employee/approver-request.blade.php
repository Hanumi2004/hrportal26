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
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Request</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Leave & Time Slip Request</h3>
                                <p class="text-muted">Approve/reject pending leave and time slip requests.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs navigation -->
    <ul class="nav nav-tabs" id="requestTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="leave-request-tab" data-bs-toggle="tab" data-bs-target="#leave-request"
                type="button" role="tab" aria-controls="leave-request" aria-selected="true">
                Leave Request
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="timeslip-request-tab" data-bs-toggle="tab" data-bs-target="#timeslip-request"
                type="button" role="tab" aria-controls="timeslip-request" aria-selected="false">
                Time Slip Request
            </button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content border border-top-0 rounded-bottom p-4 bg-white shadow-sm" id="requestTabsContent"
        style="min-height: 500px;">

        <!-- Leave Request Tab -->
        <div class="tab-pane fade show active" id="leave-request" role="tabpanel" aria-labelledby="leave-request-tab">
            <!-- Filters and Search -->
            <form method="GET" action="{{ route('employee.requests') }}">
                <input type="hidden" name="tab" class="active-tab-input" value="leave-request">
                <div class="row g-2 align-items-end">
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
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Leave Type</label>
                        <select name="leave_entitlement_id" class="form-control">
                            <option value="">All Leave Types</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ request('leave_entitlement_id') == $type->id ? 'selected' : '' }}>
                                    {{ ucfirst($type->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
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
                        <a href="{{ route('employee.requests') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- <div class="col-md-6 mt-4">
                <div class="alert alert-info mb-0" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Approving a leave request will automatically adjust the employee's leave balance.
                </div>
            </div> --}}
            <div class="card-title mt-4">Pending Leave Requests</div>

            <div class="table-responsive">
                <table class="table text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Applied Date</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Dates</th>
                            <th>Duration</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingLeaves as $leave)
                            <tr>
                                <td>{{ $leave->created_at->format('d M Y') }}</td>
                                <td>{{ $leave->employee->full_name }}</td>
                                <td>{{ ucfirst($leave->entitlement?->name ?? 'Leave') }}</td>
                                <td>{{ $leave->start_date->format('d M Y') }} →
                                    {{ $leave->end_date->format('d M Y') }}</td>
                                <td>{{ $leave->days }} days</td>
                                <td>{{ $leave->reason }}</td>

                                <td>
                                    <div class="btn-group" role="group" aria-label="Leave actions">
                                        <!-- View Button: Opens modal (client-side, no form needed) -->
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#leaveModal{{ $leave->id }}" title="View Details"
                                            type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <!-- Approve Button: Server-side action (wrapped in form) -->
                                        @if ($leave->leave_status === 'pending')
                                            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to approve this leave request?');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success" type="submit"
                                                    name="action" value="approved" title="Approve Leave">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to reject this leave request?');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    name="action" value="rejected" title="Reject Leave">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-3">No pending leave requests</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Time Slip Tab -->
        <div class="tab-pane fade" id="timeslip-request" role="tabpanel" aria-labelledby="timeslip-request-tab">
            <!-- Filters and Search -->
            <form method="GET" action="{{ route('employee.requests') }}">
                <input type="hidden" name="tab" class="active-tab-input" value="timeslip-request">
                <div class="row g-2 align-items-end">
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
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="form-control">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-1">
                        <a href="{{ route('employee.requests') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="card-title mt-4">Pending Time Slip Requests</div>
            <div class="table-responsive">
                <table class="table text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Applied Date</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pendingTimeSlips as $timeSlip)
                            <tr>
                                <td>{{ $timeSlip->created_at->format('d M Y') }}</td>
                                <td>{{ $timeSlip->employee->full_name }}</td>
                                <td>{{ $timeSlip->date->format('d M Y') }}</td>
                                <td>{{ $timeSlip->time_slip_start->format('g:i A') }} -
                                    {{ $timeSlip->time_slip_end->format('g:i A') }}</td>
                                <td>{{ $timeSlip->time_slip_reason }}</td>

                                <td>
                                    <div class="btn-group" role="group" aria-label="Leave actions">
                                        <!-- View Button: Opens modal (client-side, no form needed) -->
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#timeSlipModal{{ $timeSlip->id }}" title="View Details"
                                            type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <!-- Approve Button: Server-side action (wrapped in form) -->
                                        @if ($timeSlip->time_slip_status === 'pending')
                                            <form action="{{ route('timeslip.updateStatus', $timeSlip->id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to approve this time slip request?');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success" type="submit"
                                                    name="action" value="approved" title="Approve Time Slip">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('timeslip.updateStatus', $timeSlip->id) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to reject this time slip request?');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger" type="submit"
                                                    name="action" value="rejected" title="Reject Time Slip">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-3">No pending time slips</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($pendingLeaves as $leave)
        <div class="modal fade" id="leaveModal{{ $leave->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Leave Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Employee</th>
                                <td>{{ $leave->employee->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Leave Type</th>
                                <td>{{ ucfirst($leave->entitlement?->name ?? 'Leave') }}</td>
                            </tr>
                            <tr>
                                <th>Start Date</th>
                                <td>{{ $leave->start_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>End Date</th>
                                <td>{{ $leave->end_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Duration</th>
                                <td>{{ $leave->days }} days</td>
                            </tr>
                            <tr>
                                <th>Reason</th>
                                <td>{{ $leave->reason }}</td>
                            </tr>
                            <tr>
                                <th>Date Applied</th>
                                <td>{{ $leave->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="modal-footer">
                        @if ($leave->leave_status === 'pending')
                            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to approve this leave request?');">
                                @csrf
                                <button class="btn btn-success" type="submit" name="action"
                                    value="approved">Approve</button>
                            </form>

                            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to reject this leave request?');">
                                @csrf
                                <button class="btn btn-danger" type="submit" name="action"
                                    value="rejected">Reject</button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    @foreach ($pendingTimeSlips as $timeSlip)
        <div class="modal fade" id="timeSlipModal{{ $timeSlip->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Time Slip Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Employee</th>
                                <td>{{ $timeSlip->employee->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Dates</th>
                                <td>{{ $timeSlip->date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Time Slip Start</th>
                                <td>{{ $timeSlip->time_slip_start->format('g:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Time Slip End</th>
                                <td>{{ $timeSlip->time_slip_end->format('g:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Reason</th>
                                <td>{{ $timeSlip->time_slip_reason }}</td>
                            </tr>
                            <tr>
                                <th>Date Applied</th>
                                <td>{{ $timeSlip->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="modal-footer">
                        @if ($timeSlip->time_slip_status === 'pending')
                            <form action="{{ route('timeslip.updateStatus', $timeSlip->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to approve this time slip request?');">
                                @csrf
                                <button class="btn btn-success" type="submit" name="action"
                                    value="approved">Approve</button>
                            </form>

                            <form action="{{ route('timeslip.updateStatus', $timeSlip->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to reject this time slip request?');">
                                @csrf
                                <button class="btn btn-danger" type="submit" name="action"
                                    value="rejected">Reject</button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
