@extends('layouts.master')

@section('content')
<style>
    /* Tab Scrolling for Mobile */
    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }
    .nav-tabs .nav-link { white-space: nowrap; }

    /* Responsive Filter Grid */
    .request-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    @media (max-width: 768px) {
        .request-filter-grid {
            grid-template-columns: 1fr; /* Stack everything on small mobile */
        }
    }

    /* Mobile Table Fix */
    .table-responsive {
        border-radius: 8px;
    }
    
    .table thead th {
        white-space: nowrap;
        background-color: #f8f9fa;
    }
</style>

<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Requests</li>
                    </ol>
                </nav>
                <h3 class="page-title text-primary fw-bold">Leave & Time Slip Requests</h3>
                <p class="text-muted small">Manage pending applications from employees.</p>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs border-bottom-0" id="requestTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link {{ request('tab') != 'timeslip-request' ? 'active' : '' }}" id="leave-request-tab" data-bs-toggle="tab" data-bs-target="#leave-request" type="button">
                Leave Requests
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ request('tab') == 'timeslip-request' ? 'active' : '' }}" id="timeslip-request-tab" data-bs-toggle="tab" data-bs-target="#timeslip-request" type="button">
                Time Slip Requests
            </button>
        </li>
    </ul>

    <div class="tab-content border rounded-bottom p-3 p-md-4 bg-white shadow-sm" id="requestTabsContent" style="min-height: 500px;">

        <div class="tab-pane fade {{ request('tab') != 'timeslip-request' ? 'show active' : '' }}" id="leave-request">
            <form method="GET" action="{{ route('admin.requests') }}">
                <input type="hidden" name="tab" value="leave-request">
                <div class="request-filter-grid">
                    @if (auth()->user()->role_id === 2)
                        <div>
                            <label class="form-label small fw-bold">Employee</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or ID...">
                        </div>
                    @endif
                    <div>
                        <label class="form-label small fw-bold">Type</label>
                        <select name="leave_entitlement_id" class="form-select">
                            <option value="">All Types</option>
                            @foreach ($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_entitlement_id') == $type->id ? 'selected' : '' }}>{{ ucfirst($type->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label small fw-bold">From</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">To</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.requests') }}" class="btn btn-outline-secondary flex-grow-1">Reset</a>
                    </div>
                </div>
            </form>

            <h5 class="card-title mt-4 mb-3">Pending Leave</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Applied</th>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingLeaves as $leave)
                            <tr>
                                <td>{{ $leave->created_at->format('d/m/y') }}</td>
                                <td class="fw-bold">{{ $leave->employee->full_name }}</td>
                                <td><span class="badge bg-info-subtle text-info">{{ ucfirst($leave->entitlement?->name ?? 'Leave') }}</span></td>
                                <td>{{ $leave->days }} days</td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#leaveModal{{ $leave->id }}"><i class="bi bi-eye"></i></button>
                                        <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST">
                                            @csrf
                                            <button name="action" value="approved" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                        <form action="{{ route('leave.destroy.admin', $leave->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No pending leaves</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade {{ request('tab') == 'timeslip-request' ? 'show active' : '' }}" id="timeslip-request">
            <form method="GET" action="{{ route('admin.requests') }}">
                <input type="hidden" name="tab" value="timeslip-request">
                <div class="request-filter-grid">
                    @if (auth()->user()->role_id === 2)
                        <div>
                            <label class="form-label small fw-bold">Employee</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or ID...">
                        </div>
                    @endif
                    <div>
                        <label class="form-label small fw-bold">Slip Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                        <a href="{{ route('admin.requests') }}" class="btn btn-outline-secondary flex-grow-1">Reset</a>
                    </div>
                </div>
            </form>

            <h5 class="card-title mt-4 mb-3">Pending Time Slips</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Applied</th>
                            <th>Employee</th>
                            <th>Slip Date</th>
                            <th>Time</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingTimeSlips as $timeSlip)
                            <tr>
                                <td>{{ $timeSlip->created_at->format('d/m/y') }}</td>
                                <td class="fw-bold">{{ $timeSlip->employee->full_name }}</td>
                                <td>{{ $timeSlip->date->format('d M') }}</td>
                                <td>{{ $timeSlip->time_slip_start->format('g:i A') }} - {{ $timeSlip->time_slip_end->format('g:i A') }}</td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#timeSlipModal{{ $timeSlip->id }}"><i class="bi bi-eye"></i></button>
                                        <form action="{{ route('timeslip.updateStatus', $timeSlip->id) }}" method="POST">
                                            @csrf
                                            <button name="action" value="approved" class="btn btn-sm btn-outline-success"><i class="bi bi-check-lg"></i></button>
                                            <button name="action" value="rejected" class="btn btn-sm btn-outline-warning"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                        <form action="{{ route('timeslip.destroy', $timeSlip->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No pending slips</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($pendingLeaves as $leave)
        <div class="modal fade" id="leaveModal{{ $leave->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">Leave Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="small text-muted d-block">Employee</label>
                            <div class="fw-bold fs-5">{{ $leave->employee->full_name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="small text-muted d-block">Type</label>
                                <span class="badge bg-primary">{{ ucfirst($leave->entitlement?->name ?? 'Leave') }}</span>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted d-block">Duration</label>
                                <span class="fw-bold">{{ $leave->days }} days</span>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded mb-3">
                            <i class="bi bi-calendar-event me-2"></i> {{ $leave->start_date->format('d M') }} to {{ $leave->end_date->format('d M Y') }}
                        </div>
                        <div class="mb-0">
                            <label class="small text-muted d-block">Reason</label>
                            <p class="mb-0 fst-italic">"{{ $leave->leave_reason }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($pendingTimeSlips as $timeSlip)
        @endforeach
</div>
@endsection