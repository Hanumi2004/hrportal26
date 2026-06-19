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
                                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Attendance</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Attendance</h3>
                                <p class="text-muted">Track your daily attendance and working hours.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('employee.attendance') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Time In</label>
                        <select name="status_time_in" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach ($statusTimeInOptions as $status)
                                <option value="{{ $status }}"
                                    {{ request('status_time_in') == $status ? 'selected' : '' }}>{{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Time Out</label>
                        <select name="status_time_out" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach ($statusTimeOutOptions as $status)
                                <option value="{{ $status }}"
                                    {{ request('status_time_out') == $status ? 'selected' : '' }}>{{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Location In</label>
                        <select name="location_in" class="form-select">
                            <option value="">All Locations</option>
                            @foreach ($locationInOptions as $location)
                                <option value="{{ $location }}"
                                    {{ request('location_in') == $location ? 'selected' : '' }}>
                                    {{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Location Out</label>
                        <select name="location_out" class="form-select">
                            <option value="">All Locations</option>
                            @foreach ($locationOutOptions as $location)
                                <option value="{{ $location }}"
                                    {{ request('location_out') == $location ? 'selected' : '' }}>
                                    {{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('employee.attendance') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Total Requests -->
        <div class="col-12 col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    {{-- makes content flexible row-pushes text left, icon right --}}

                    <div class="card-title">Today's Attendance</div>

                    <div class="datetime-punch mt-2">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-clock-history text-secondary me-2"></i>
                            <div class="datetime-time fw-bold" id="currentTime"></div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="datetime-date" id="currentDate"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Time In:
                            <span class="text-primary" id="timeInDisplay">
                                {{ $todayAttendance?->time_in?->format('g:i:s A') ?? '—' }}
                                {{-- the model attributes are Carbon instances, echoing them directly uses Carbon’s default string (which includes date + time). 
                                format explicitly in Blade: use ->format(...) or ->toDateString() and null-safe operator --}}
                            </span>
                        </h5>
                        <h5>
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Time Out:
                            <span class="text-primary" id="timeOutDisplay">
                                {{ $todayAttendance?->time_out?->format('g:i:s A') ?? '—' }}
                            </span>
                        </h5>
                    </div>

                    <div id="punchContainer">
                        @php
                            $attendance = $todayAttendance;
                            $hasSlip = $attendance?->time_slip_start && $attendance?->time_slip_end;
                            $slipStatus = $attendance?->time_slip_status;
                        @endphp

                        {{-- =============================
                            CASE 1: No Attendance Today
                        ============================= --}}
                        @if (!$attendance)
                            @if ($hasSlip && $slipStatus === 'pending')
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark">Time slip pending</span>
                                    <small class="text-muted ms-2">Cannot punch in while request is pending.</small>
                                </div>
                            @elseif (!$hasSlip)
                                {{-- Normal Punch In --}}
                                <button class="btn btn-punch mb-2" id="punchInBtn">
                                    <i class="bi bi-hand-index-thumb-fill me-1"></i> Punch In
                                </button>
                            @endif

                            {{-- =============================
                            CASE 2: Attendance exists
                        ============================= --}}
                        @elseif ($attendance->time_in && !$attendance->time_out)
                            {{-- Punched in only → punch out --}}
                            <button class="btn btn-punch mb-2" id="punchOutBtn">
                                <i class="bi bi-hand-index-thumb-fill me-1"></i> Punch Out
                            </button>

                            {{-- =============================
                            CASE 3: Fully Completed
                        ============================= --}}
                        @elseif ($attendance->time_in && $attendance->time_out)
                            <span class="text-success mb-2 d-block">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                You have punched out for today.
                            </span>
                        @endif

                        @if ($hasSlip && $slipStatus === 'pending')
                            {{-- Employee can cancel slip --}}
                            <form action="{{ route('timeslip.destroy', $attendance->id) }}" method="POST"
                                onsubmit="return confirm('Cancel time slip request?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Cancel Slip</button>
                            </form>

                            {{-- Time Slip Request Button --}}
                        @else
                            <button class="btn btn-punch" data-bs-toggle="modal" data-bs-target="#timeSlipModal">
                                <i class="bi bi-receipt me-1"></i> Request Time Slip
                            </button>
                        @endif
                    </div>

                    <small class="mt-4">Your current location:</small>
                    {{-- Google Maps Container --}}
                    <div id="attendanceMap" style="height: 250px; width: 100%; border-radius: 8px;" class="mt-3">
                    </div>

                </div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="col-12 col-md-9 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="card-title">Attendance History</div>

                        <a href="{{ route('attendance.export', ['from' => request('from'), 'to' => request('to')]) }}"
                            class="btn btn-success">
                            <i class="bi bi-file-earmark-excel"></i> Export to Excel
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Location In</th>
                                    <th>Time Out</th>
                                    <th>Location Out</th>
                                    <th>Time Slip</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceHistoryTable">
                                @foreach ($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->date?->format('d M Y') ?? '—' }}</td>

                                        <td>{{ $attendance->time_in?->format('g:i:s A') ?? '-' }}
                                            @if ($attendance->status_time_in === 'On Time')
                                                <span
                                                    class="badge bg-success ms-2">{{ $attendance->status_time_in }}</span>
                                            @elseif ($attendance->status_time_in === 'Late')
                                                <span
                                                    class="badge bg-danger ms-2">{{ $attendance->status_time_in }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $attendance->location_in ?? '-' }}</td>

                                        <td>{{ $attendance->time_out?->format('g:i:s A') ?? '-' }}
                                            @if ($attendance->status_time_out === 'On Time')
                                                <span
                                                    class="badge bg-success ms-2">{{ $attendance->status_time_out }}</span>
                                            @elseif ($attendance->status_time_out === 'Early Leave')
                                                <span
                                                    class="badge bg-danger ms-2">{{ $attendance->status_time_out }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $attendance->location_out ?? '-' }}</td>

                                        <td>
                                            @if ($attendance->time_slip_start && $attendance->time_slip_end)
                                                {{ $attendance->time_slip_start->format('g:i A') }} -
                                                {{ $attendance->time_slip_end->format('g:i A') }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif

                                            @if ($attendance->time_slip_status)
                                                @if ($attendance->time_slip_status === 'pending')
                                                    <span
                                                        class="badge bg-warning ms-2">{{ ucfirst($attendance->time_slip_status) }}</span>
                                                @elseif ($attendance->time_slip_status === 'rejected')
                                                    <span
                                                        class="badge bg-danger ms-2">{{ ucfirst($attendance->time_slip_status) }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-success ms-2">{{ ucfirst($attendance->time_slip_status) }}</span>
                                                @endif
                                            @endif
                                        </td>

                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#attendanceModal{{ $attendance->id }}"
                                                title="View Details">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- No data message row -->
                                @if ($attendances->count() == 0)
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox me-2"></i>No attendance record found
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="attendanceModalContainer">
        @foreach ($attendances as $attendance)
            <div class="modal fade" id="attendanceModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">Attendance Details
                                    ({{ $attendance->date?->format('d M Y') }})
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Date</th>
                                        <td>{{ $attendance->date?->format('d M Y') ?? '—' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Time In</th>
                                        <td>{{ $attendance->time_in?->format('g:i:s A') ?? '-' }}
                                            @if ($attendance->status_time_in === 'On Time')
                                                <span
                                                    class="badge bg-success ms-2">{{ $attendance->status_time_in }}</span>
                                            @elseif ($attendance->status_time_in === 'Late')
                                                <span
                                                    class="badge bg-danger ms-2">{{ $attendance->status_time_in }}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Late Reason</th>
                                        <td>
                                            @if ($attendance->status_time_in === 'Late')
                                                <textarea name="late_reason" class="form-control" rows="1"
                                                    placeholder="Please provide a reason for being late">{{ $attendance->late_reason }}</textarea>
                                            @else
                                                <span class="text-muted fst-italic">N/A</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Location In</th>
                                        <td>{{ $attendance->location_in ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Punch In Location</th>
                                        <td>
                                            <div id="map-in-{{ $attendance->id }}"
                                                data-lat="{{ $attendance->time_in_lat }}"
                                                data-lng="{{ $attendance->time_in_lng }}"
                                                style="height:250px; width: 100%; border-radius: 8px;"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Time Out</th>
                                        <td>{{ $attendance->time_out?->format('g:i:s A') ?? '-' }}
                                            @if ($attendance->status_time_out === 'On Time')
                                                <span
                                                    class="badge bg-success ms-2">{{ $attendance->status_time_out }}</span>
                                            @elseif ($attendance->status_time_out === 'Early Leave')
                                                <span
                                                    class="badge bg-danger ms-2">{{ $attendance->status_time_out }}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr class="early-leave-row">
                                        <th>Early Leave Reason</th>
                                        <td>
                                            @if ($attendance->status_time_out === 'Early Leave')
                                                <textarea name="early_leave_reason" class="form-control" rows="1"
                                                    placeholder="Please provide a reason for leaving early">{{ $attendance->early_leave_reason }}</textarea>
                                            @else
                                                <span class="text-muted fst-italic">N/A</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Location Out</th>
                                        <td>{{ $attendance->location_out ?? '-' }}</td>
                                    </tr>

                                    <tr>
                                        <th>Punch Out Location</th>
                                        <td>
                                            <div id="map-out-{{ $attendance->id }}"
                                                data-lat="{{ $attendance->time_out_lat }}"
                                                data-lng="{{ $attendance->time_out_lng }}"
                                                style="height:250px; width: 100%; border-radius: 8px;"></div>
                                        </td>
                                    </tr>

                                </table>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="modal fade" id="timeSlipModal" tabindex="-1" aria-hidden="true" aria-labelledby="timeSlipModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('attendance.time-slip') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="timeSlipModalLabel">Request Time Slip
                            ({{ $todayAttendance?->date?->format('d M Y') ?? now()->format('d M Y') }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="time_slip_start" class="form-label">Start Time</label>
                            <input type="time" name="time_slip_start" id="time_slip_start" class="form-control"
                                required value="{{ old('time_slip_start', $todayAttendance->time_slip_start ?? '') }}">
                            @error('time_slip_start')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="time_slip_end" class="form-label">End Time</label>
                            <input type="time" name="time_slip_end" id="time_slip_end" class="form-control" required
                                value="{{ old('time_slip_end', $todayAttendance->time_slip_end ?? '') }}">
                            @error('time_slip_end')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @else
                                <small class="text-muted">Maximum time slip duration is 2 hours</small>
                            @enderror
                            <!-- Error container for JavaScript validation -->
                            <div class="alert alert-danger mt-2 d-none" id="timeSlipError"></div>
                        </div>

                        <div class="mb-3">
                            <label for="time_slip_reason" class="form-label">Reason</label>
                            <textarea name="time_slip_reason" id="time_slip_reason" class="form-control" rows="2"
                                placeholder="Explain briefly (e.g. short errand, clinic visit)" required>{{ old('time_slip_reason', $todayAttendance->time_slip_reason ?? '') }}</textarea>
                            @error('time_slip_reason')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}"></script>

    {{-- Real-time Date & Time Script --}}
    <script>
        function updateDateTime() {
            const now = new Date();

            // Format date
            const dateStr = now.toLocaleDateString(undefined, {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Format time (HH:MM:SS)
            const timeStr = now.toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            document.getElementById('currentTime').textContent = timeStr;
            document.getElementById('currentDate').textContent = dateStr;
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // reusable punch function
        function sendPunch(url, mapId, punchType) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    fetch(url, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                latitude: lat,
                                longitude: lng
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            // --------------------------
                            // Update Punch In/Out Buttons
                            // --------------------------
                            const container = document.getElementById('punchContainer');
                            if (data.action === 'punchIn') {
                                container.innerHTML = `
                        <button class="btn btn-punch" id="punchOutBtn">
                            <i class="bi bi-hand-index-thumb-fill me-1"></i> Punch Out
                        </button>
                    `;
                            }
                            if (data.action === 'punchOut') {
                                container.innerHTML = `
                        <span class="text-success mt-3">
                            <i class="bi bi-check-circle-fill me-1"></i> You have punched out for today.
                        </span>
                    `;
                            }

                            // --------------------------
                            // Update Today’s Attendance Card
                            // --------------------------
                            if (data.action === 'punchIn') {
                                document.getElementById('timeInDisplay').textContent = data.time.split(" ")[1];
                            }
                            if (data.action === 'punchOut') {
                                document.getElementById('timeOutDisplay').textContent = data.time.split(" ")[1];
                            }

                            // --------------------------
                            // Update Attendance History
                            // --------------------------
                            const historyTable = document.getElementById('attendanceHistoryTable');

                            if (data.action === 'punchIn') {
                                const modalId = "attendanceModal" + data.id;

                                // New row
                                const newRow = `
                        <tr class="text-start">
                            <td class="py-3 px-3 border-b border-gray-100">${data.time.split(" ")[0]}</td> 
                            <td class="py-3 px-3 border-b border-gray-100">${data.time.split(" ")[1]}</td>
                            <td class="py-3 px-3 border-b border-gray-100">
                                ${data.status_time_in === 'On Time'
                                    ? '<span class="badge bg-success">On Time</span>'
                                    : '<span class="badge bg-danger">Late</span>'}
                            </td>
                            <td class="py-3 px-3 border-b border-gray-100">—</td>
                            <td class="py-3 px-3 border-b border-gray-100">—</td>
                            <td class="py-3 px-3 border-b border-gray-100">${data.status}</td>
                            <td class="py-3 px-3 border-b border-gray-100">
                                <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#${modalId}" title="View Details">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                                historyTable.insertAdjacentHTML("afterbegin", newRow);

                                // Modal HTML
                                const modalHTML = `
                        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" action="/attendance/${data.id}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="PUT">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Attendance Details (${data.time.split(" ")[0]})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <table class="table table-sm">
                                                <tr><th>Date</th><td>${data.time.split(" ")[0]}</td></tr>
                                                <tr><th>Time In</th><td>${data.time.split(" ")[1]}</td></tr>
                                                <tr><th>Status Time In</th><td>${data.status_time_in}</td></tr>
                                                <tr><th>Time Out</th><td>-</td></tr>
                                                <tr><th>Status Time Out</th><td>-</td></tr>
                                                <tr><th>Status</th><td>${data.status}</td></tr>
                                                <tr><th>Late Reason</th>
                                                    <td>
                                                        ${data.status_time_in === 'Late'
                                                            ? `<textarea name="late_reason" class="form-control" rows="1"></textarea>`
                                                            : `<span class="text-muted fst-italic">Not Applicable</span>`}
                                                    </td>
                                                </tr>
                                                <tr><th>Early Leave Reason</th>
                                                    <td><span class="text-muted fst-italic">Not Applicable</span></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                                document.getElementById('attendanceModalContainer').insertAdjacentHTML(
                                    "afterbegin", modalHTML);
                            }

                            // --------------------------
                            // Punch Out → Update existing modal
                            // --------------------------
                            if (data.action === 'punchOut') {
                                // Find first row (today)
                                const firstRow = historyTable.querySelector("tr");
                                if (firstRow) {
                                    const cells = firstRow.querySelectorAll("td");
                                    cells[3].textContent = data.time.split(" ")[1]; // Time Out
                                    cells[4].innerHTML = data.status_time_out === 'On Time' ?
                                        '<span class="badge bg-success">On Time</span>' :
                                        '<span class="badge bg-danger">Early Leave</span>';
                                }

                                // Update modal Early Leave Reason
                                const modal = document.querySelector("#attendanceModal" + data.id);
                                if (modal) {
                                    const rows = modal.querySelectorAll("tr");
                                    const earlyLeaveRow = modal.querySelector(
                                        ".early-leave-row td"
                                    ); // finds the right element directly by class name
                                    if (data.status_time_out === 'Early Leave') {
                                        earlyLeaveRow.innerHTML =
                                            `<textarea name="early_leave_reason" class="form-control" rows="1"></textarea>`;
                                    } else {
                                        earlyLeaveRow.innerHTML =
                                            `<span class="text-muted fst-italic">Not Applicable</span>`;
                                    }

                                    // Update Time Out + Status in modal
                                    rows[3].querySelector("td").textContent = data.time.split(" ")[
                                        1]; // Time Out
                                    rows[4].querySelector("td").textContent = data.status_time_out;
                                }
                            }

                            // --------------------------
                            // Success Alert
                            // --------------------------
                            alert(
                                `You ${punchType} at: ${data.time}, Status: ${data.status ?? data.status_time_in}`
                            );
                        })
                        .catch(err => console.error(err));
                });
            } else {
                alert("Geolocation is not supported by your browser.");
            }
        }

        // bind punch in if exists
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'punchInBtn') {
                sendPunch("{{ route('attendance.punchIn') }}", "map", "punched in");
            }
            if (e.target && e.target.id === 'punchOutBtn') {
                sendPunch("{{ route('attendance.punchOut') }}", "mapOut", "punched out");
            }
        });
    </script>

    {{-- Re-open modal if there were validation errors for the time slip --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->has('time_slip_start') || $errors->has('time_slip_end') || $errors->has('time_slip_reason'))
                var slipModalEl = document.getElementById('timeSlipModal');
                if (slipModalEl) {
                    var slipModal = new bootstrap.Modal(slipModalEl);
                    slipModal.show();
                }
            @endif
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timeSlipForm = document.querySelector('#timeSlipModal form');
            const timeSlipStart = document.getElementById('time_slip_start');
            const timeSlipEnd = document.getElementById('time_slip_end');
            const errorContainer = document.getElementById('timeSlipError');

            // No need to appendChild - element already exists
            // Just ensure it starts hidden
            errorContainer.classList.add('d-none');

            // Validate time difference on form submit
            timeSlipForm.addEventListener('submit', function(e) {
                const startTime = timeSlipStart.value;
                const endTime = timeSlipEnd.value;

                if (startTime && endTime) {
                    const start = new Date('2000-01-01T' + startTime + ':00');
                    const end = new Date('2000-01-01T' + endTime + ':00');
                    const diffMs = end - start;
                    const diffHours = diffMs / (1000 * 60 * 60);

                    if (diffHours > 2) {
                        e.preventDefault();
                        errorContainer.textContent = 'Time slip cannot exceed 2 hours.';
                        errorContainer.classList.remove('d-none');
                        timeSlipEnd.focus();
                    } else {
                        errorContainer.classList.add('d-none');
                    }
                }
            });

            // Also validate on input change for better UX
            timeSlipEnd.addEventListener('change', function() {
                const startTime = timeSlipStart.value;
                const endTime = timeSlipEnd.value;

                if (startTime && endTime) {
                    const start = new Date('2000-01-01T' + startTime + ':00');
                    const end = new Date('2000-01-01T' + endTime + ':00');
                    const diffMs = end - start;
                    const diffHours = diffMs / (1000 * 60 * 60);

                    if (diffHours > 2) {
                        errorContainer.textContent = 'Time slip cannot exceed 2 hours.';
                        errorContainer.classList.remove('d-none');
                    } else {
                        errorContainer.classList.add('d-none');
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener('shown.bs.modal', function(event) {
            const modal = event.target;

            modal.querySelectorAll('[id^="map-"]').forEach(el => {
                const lat = parseFloat(el.dataset.lat);
                const lng = parseFloat(el.dataset.lng);

                if (!lat || !lng) {
                    el.innerHTML = '<span class="text-muted">Location not available</span>';
                    return;
                }

                const map = new google.maps.Map(el, {
                    zoom: 16,
                    center: {
                        lat,
                        lng
                    }
                });

                new google.maps.Marker({
                    position: {
                        lat,
                        lng
                    },
                    map,
                    title: el.id.includes('map-in') ? 'Punch In' : 'Punch Out'
                });
            });
        });
    </script>

    <script>
        let map, marker;

        function initAttendanceMap(lat, lng) {
            const userLocation = {
                lat: lat,
                lng: lng
            };

            map = new google.maps.Map(document.getElementById("attendanceMap"), {
                zoom: 16,
                center: userLocation,
            });

            marker = new google.maps.Marker({
                position: userLocation,
                map: map,
                title: "Your location",
            });
        }

        // Load map on page load
        document.addEventListener("DOMContentLoaded", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        initAttendanceMap(
                            position.coords.latitude,
                            position.coords.longitude
                        );
                    },
                    () => alert("Location permission required for attendance.")
                );
            }
        });
    </script>

@endsection
