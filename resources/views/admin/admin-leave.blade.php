@extends('layouts.master')

@section('title', 'Leave Management')

@section('content')
<style>
    /* Responsive Grid for Filters */
    .leave-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    @media (min-width: 1200px) {
        .leave-filter-grid {
            grid-template-columns: repeat(5, 1fr) 120px 120px;
        }
    }

    /* Summary Stats Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
    }

    /* Table Responsiveness */
    .table-responsive-custom {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 8px;
    }

    /* Fixed min-widths for data-heavy tables */
    .leave-table { min-width: 1100px; }
    .report-table { min-width: 1300px; }

    /* Tab Scrolling on Mobile */
    .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }
    .nav-tabs .nav-link { white-space: nowrap; }

    .filter-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eee;
    }
    .filter-card:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .filter-card.active { border-color: var(--primary-color); background-color: rgba(0,123,255,0.05); }

    .stat-number { font-size: 1.5rem; font-weight: 700; }
</style>

<div class="container-fluid p-0">
    <div class="page-header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Leave Management</li>
            </ol>
        </nav>
        <h3 class="page-title text-primary fw-bold">Leave Management</h3>
        <p class="text-muted small">Process applications and view annual reports.</p>
    </div>

    <ul class="nav nav-tabs border-bottom-0" id="leaveTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="application-tab" data-bs-toggle="tab" data-bs-target="#application-content" type="button">Applications</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="report-tab" data-bs-toggle="tab" data-bs-target="#report-content" type="button">Annual Report</button>
        </li>
    </ul>

    <div class="tab-content bg-white border rounded-bottom shadow-sm p-3 p-md-4">
        
        <div class="tab-pane fade show active" id="application-content">
            <form method="GET" action="{{ route('leave.index.admin') }}" class="mb-4">
                <input type="hidden" name="tab" value="leave-application">
                <div class="leave-filter-grid">
                    @if(auth()->user()->role_id === 2)
                    <div>
                        <label class="form-label small fw-bold">Employee</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name/ID">
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
                        <label class="form-label small fw-bold">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('leave.index.admin') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="summary-grid mb-4">
                <div class="card filter-card active" data-status="all">
                    <div class="card-body text-center py-3">
                        <div class="text-primary small fw-bold">Total</div>
                        <div class="stat-number">{{ $totalRequests }}</div>
                    </div>
                </div>
                <div class="card filter-card" data-status="pending">
                    <div class="card-body text-center py-3">
                        <div class="text-warning small fw-bold">Pending</div>
                        <div class="stat-number">{{ $pendingLeaves }}</div>
                    </div>
                </div>
                <div class="card filter-card" data-status="approved">
                    <div class="card-body text-center py-3">
                        <div class="text-success small fw-bold">Approved</div>
                        <div class="stat-number">{{ $approvedLeaves }}</div>
                    </div>
                </div>
                <div class="card filter-card" data-status="rejected">
                    <div class="card-body text-center py-3">
                        <div class="text-danger small fw-bold">Rejected</div>
                        <div class="stat-number">{{ $rejectedLeaves }}</div>
                    </div>
                </div>
            </div>

            <div class="d-none d-lg-block">
                <div class="table-responsive-custom">
                    <table class="table table-hover align-middle leave-table">
                        <thead class="table-light">
                            <tr>
                                <th>Applied Date</th>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Period</th>
                                <th>Days</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="leavesTableDesktop">
                            @forelse ($leaves as $leave)
                                @php
                                    $status = strtolower($leave->leave_status ?? 'pending');
                                    $badge = match($status) {
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-warning text-dark'
                                    };
                                @endphp
                                <tr data-status="{{ $status }}">
                                    <td>{{ $leave->created_at->format('d/m/Y') }}</td>
                                    <td class="fw-bold">{{ $leave->employee->full_name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($leave->entitlement?->name) }}</td>
                                    <td>{{ date('d M', strtotime($leave->start_date)) }} - {{ date('d M Y', strtotime($leave->end_date)) }}</td>
                                    <td>{{ $leave->days }}</td>
                                    <td><span class="badge {{ $badge }}">{{ ucfirst($status) }}</span></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#leaveDetailModal" 
                                                data-employee="{{ $leave->employee->full_name }}" data-type="{{ ucfirst($leave->entitlement?->name) }}"
                                                data-reason="{{ $leave->leave_reason }}" data-start="{{ $leave->start_date }}" data-end="{{ $leave->end_date }}"
                                                data-status="{{ ucfirst($status) }}" data-badge="{{ $badge }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST" class="d-flex gap-1">
                                                @csrf
                                                <button name="action" value="approved" class="btn btn-sm btn-outline-success" {{ $status == 'approved' ? 'disabled' : '' }}><i class="bi bi-check"></i></button>
                                                <button name="action" value="rejected" class="btn btn-sm btn-outline-danger" {{ $status == 'rejected' ? 'disabled' : '' }}><i class="bi bi-x"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted">No applications found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-block d-lg-none" id="leavesCardList">
                @foreach ($leaves as $leave)
                <div class="card mb-3 border-0 shadow-sm leave-card-item" data-status="{{ strtolower($leave->leave_status) }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="fw-bold text-primary">{{ $leave->employee->full_name }}</div>
                            <span class="badge {{ strtolower($leave->leave_status) == 'approved' ? 'bg-success' : (strtolower($leave->leave_status) == 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($leave->leave_status) }}
                            </span>
                        </div>
                        <div class="small text-muted mb-3">{{ ucfirst($leave->entitlement?->name) }} | {{ $leave->days }} Days</div>
                        <div class="small mb-3">
                            <i class="bi bi-calendar3 me-2"></i>{{ date('d/m/Y', strtotime($leave->start_date)) }} - {{ date('d/m/Y', strtotime($leave->end_date)) }}
                        </div>
                        <div class="d-grid gap-2 d-flex">
                            <form action="{{ route('leave.updateStatus', $leave->id) }}" method="POST" class="w-100 d-flex gap-2">
                                @csrf
                                <button name="action" value="approved" class="btn btn-sm btn-success flex-grow-1">Approve</button>
                                <button name="action" value="rejected" class="btn btn-sm btn-danger flex-grow-1">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="tab-pane fade" id="report-content">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <h5 class="fw-bold mb-0">Annual Attendance Report</h5>
                <form method="GET" action="{{ route('leave.index.admin') }}" class="d-flex gap-2">
                    <input type="hidden" name="tab" value="leave-report">
                    <select name="year" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @for ($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <select name="full_name" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->full_name }}" {{ $emp->full_name == $selectedEmployeeName ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="table-responsive-custom">
                <table class="table table-bordered table-sm text-center align-middle report-table">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start ps-3">Leave Category</th>
                            <th style="width: 80px;">Entitle</th>
                            @for ($m = 1; $m <= 12; $m++)
                                <th style="width: 50px;">{{ \Carbon\Carbon::create()->month($m)->shortMonthName }}</th>
                            @endfor
                            <th class="bg-primary-light">Used</th>
                            <th class="bg-light">Bal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveTypes as $lt)
                            @php
                                $typeName = $lt->name;
                                $entitle = $finalEntitlements[$typeName] ?? 0;
                                $used = 0;
                            @endphp
                            <tr>
                                <td class="text-start ps-3 fw-medium">{{ ucwords(str_replace('_', ' ', $typeName)) }}</td>
                                <td class="fw-bold">{{ number_format($entitle, 1) }}</td>
                                @for ($m = 1; $m <= 12; $m++)
                                    @php $val = $reportData[$typeName][$m] ?? 0; $used += $val; @endphp
                                    <td class="{{ $val > 0 ? 'fw-bold text-primary' : 'text-muted' }}">{{ $val > 0 ? $val : '-' }}</td>
                                @endfor
                                <td class="fw-bold text-primary">{{ $used }}</td>
                                <td class="fw-bold {{ ($entitle - $used) < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($entitle - $used, 1) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="small text-muted d-block">Employee</label>
                    <div id="modalEmployee" class="fw-bold fs-5"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="small text-muted d-block">Type</label>
                        <div id="modalType" class="fw-bold"></div>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted d-block">Status</label>
                        <div id="modalStatus"></div>
                    </div>
                </div>
                <div class="mb-3 p-3 bg-light rounded">
                    <label class="small text-muted d-block mb-1">Leave Period</label>
                    <div class="fw-bold"><span id="modalStart"></span> &rarr; <span id="modalEnd"></span></div>
                </div>
                <div>
                    <label class="small text-muted d-block mb-1">Reason</label>
                    <div id="modalReason" class="fst-italic text-dark p-2 border rounded"></div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Modal Data
    const detailModal = document.getElementById('leaveDetailModal');
    detailModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        document.getElementById('modalEmployee').innerText = btn.getAttribute('data-employee');
        document.getElementById('modalType').innerText = btn.getAttribute('data-type');
        document.getElementById('modalStart').innerText = btn.getAttribute('data-start');
        document.getElementById('modalEnd').innerText = btn.getAttribute('data-end');
        document.getElementById('modalReason').innerText = btn.getAttribute('data-reason') || 'No reason provided';
        document.getElementById('modalStatus').innerHTML = `<span class="badge ${btn.getAttribute('data-badge')}">${btn.getAttribute('data-status')}</span>`;
    });

    // Handle Quick Stats Filter
    document.querySelectorAll('.filter-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            const status = this.getAttribute('data-status');

            // Desktop filter
            document.querySelectorAll('#leavesTableDesktop tr[data-status]').forEach(row => {
                row.style.display = (status === 'all' || row.getAttribute('data-status') === status) ? '' : 'none';
            });

            // Mobile filter
            document.querySelectorAll('.leave-card-item').forEach(card => {
                card.style.display = (status === 'all' || card.getAttribute('data-status') === status) ? '' : 'none';
            });
        });
    });
});
</script>
@endsection