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
                                        <li class="breadcrumb-item active" aria-current="page">System Setting</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>System Setting</h3>
                                <p class="text-muted">Manage company-wide rules and master data configurations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- ===================== ATTENDANCE & LEAVE (Row 1) ===================== --}}
            <div class="col-lg-4">
                <form method="POST" action="{{ route('admin.settings.general') }}" class="card h-100 shadow-sm border-0">
                    @csrf
                    <div class="card-header bg-transparent fw-bold border-bottom-0 pt-4 px-4">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Attendance Rules
                    </div>
                    <div class="card-body px-4">
                        <div class="mb-3">
                            <label class="form-label small text-muted text-uppercase fw-bold">Max Time Slip (hours)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="max_timeslip_hours"
                                    value="{{ $settings['max_timeslip_hours'] ?? 3 }}">
                                <span class="input-group-text">hours</span>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm w-100 mt-2">Update Rules</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-8">
                <form method="POST" action="{{ route('admin.settings.leave') }}" class="card h-100 shadow-sm border-0">
                    @csrf
                    <div
                        class="card-header bg-transparent d-flex justify-content-between align-items-center border-bottom-0 pt-4 px-4">
                        <span class="fw-bold"><i class="bi bi-airplane me-2 text-primary"></i>Leave Entitlements</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addLeave()">
                            <i class="bi bi-plus-lg me-1"></i> Add Type
                        </button>
                    </div>
                    <div class="card-body px-4">
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="small py-2">Leave Type Name</th>
                                        <th class="small py-2" width="120">Days/Year</th>
                                    </tr>
                                </thead>
                                <tbody id="leave-entitlements">
                                    @foreach ($leaveEntitlements as $i => $leave)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control form-control-sm border-0 bg-light"
                                                    name="entitlements[{{ $i }}][name]"
                                                    value="{{ $leave->name }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.5"
                                                    class="form-control form-control-sm border-0 bg-light text-center"
                                                    name="entitlements[{{ $i }}][days]"
                                                    value="{{ $leave->full_entitlement }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary btn-sm px-4">Update Leaves</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ===================== MASTER DATA (Row 2) ===================== --}}
            <div class="col-12 mt-4">
                <form method="POST" action="{{ route('admin.settings.master') }}" class="card shadow-sm border-0">
                    @csrf
                    <div class="card-header bg-transparent fw-bold border-bottom-0 pt-4 px-4">
                        <i class="bi bi-database-fill-gear me-2 text-primary"></i>Master Data Management
                    </div>
                    <div class="card-body px-4">
                        @php
                            $blocks = [
                                'Event Categories' => ['event_categories', $eventCategories, 'bi-tags'],
                                'Employment Types' => ['employment_types', $employmentTypes, 'bi-people'],
                                'Employment Statuses' => ['employment_statuses', $employmentStatuses, 'bi-people'],
                                'Company Branches' => ['company_branches', $companyBranches, 'bi-building-fill'],
                                'Departments' => ['departments', $departments, 'bi-diagram-3'],
                            ];
                        @endphp

                        <!-- Column Grid for Master Data -->
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            @foreach ($blocks as $title => [$name, $items, $icon])
                                <div class="col">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <label class="form-label fw-bold small mb-0"><i
                                                    class="bi {{ $icon }} me-2"></i>{{ $title }}</label>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none"
                                                onclick="addField('{{ $name }}')">
                                                <i class="bi bi-plus-circle"></i> Add
                                            </button>
                                        </div>
                                        <div id="{{ $name }}" class="master-data-inputs"
                                            style="max-height: 150px; overflow-y: auto;">
                                            @foreach ($items as $item)
                                                <input type="text"
                                                    class="form-control form-control-sm mb-2 border-0 bg-white shadow-xs"
                                                    name="{{ $name }}[]" value="{{ $item->name }}">
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-center mt-4 border-top pt-4">
                            <button class="btn btn-primary px-5 py-2">Update All Master Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function addField(id) {
            const container = document.getElementById(id);
            const input =
                `<input type="text" class="form-control form-control-sm mb-2 border-0 bg-white shadow-xs" name="${id}[]">`;
            container.insertAdjacentHTML('beforeend', input);
            container.scrollTop = container.scrollHeight;
        }

        function addLeave() {
            document.getElementById('leave-entitlements')
                .insertAdjacentHTML('beforeend',
                    `<tr>
                    <td><input class="form-control form-control-sm border-0 bg-light" name="entitlements[][name]"></td>
                    <td><input class="form-control form-control-sm border-0 bg-light text-center" type="number" step="0.5" name="entitlements[][days]"></td>
                </tr>`
                );
        }
    </script>
@endpush
