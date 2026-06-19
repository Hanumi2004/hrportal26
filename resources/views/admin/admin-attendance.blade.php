@extends('layouts.master')



@section('content')

    <!-- Page Header -->

    <div class="page-header mb-4">

        <div class="row">

            <div class="col-12">

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb mb-0">

                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>

                        <li class="breadcrumb-item active" aria-current="page">Attendance</li>

                    </ol>

                </nav>

                <h3 class="page-title">Attendance</h3>

                <p class="text-muted mb-0">Track all employee daily attendance and working hours.</p>

            </div>

        </div>

    </div>



    <!-- Filter Card -->

    <div class="card mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.attendance') }}">

                <div class="row g-2">

                    <!-- Search -->

                    <div class="col-6 col-lg-2">

                        <label class="form-label fw-bold small mb-1">Search</label>

                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or ID...">

                    </div>

                    <!-- Date -->

                    <div class="col-6 col-lg-2">

                        <label class="form-label fw-bold small mb-1">Date</label>

                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">

                    </div>

                    <!-- Status In -->

                    <div class="col-6 col-lg-2">

                        <label class="form-label fw-bold small mb-1">Status In</label>

                        <select name="status_time_in" class="form-select">

                            <option value="">All</option>

                            @foreach ($statusTimeInOptions as $status)

                                <option value="{{ $status }}" {{ request('status_time_in') == $status ? 'selected' : '' }}>{{ $status }}</option>

                            @endforeach

                        </select>

                    </div>

                    <!-- Status Out -->

                    <div class="col-6 col-lg-2">

                        <label class="form-label fw-bold small mb-1">Status Out</label>

                        <select name="status_time_out" class="form-select">

                            <option value="">All</option>

                            @foreach ($statusTimeOutOptions as $status)

                                <option value="{{ $status }}" {{ request('status_time_out') == $status ? 'selected' : '' }}>{{ $status }}</option>

                            @endforeach

                        </select>

                    </div>

                    <!-- Location In -->

                    <div class="col-6 col-lg-2">

                        <label class="form-label fw-bold small mb-1">Location In</label>

                        <select name="location_in" class="form-select">

                            <option value="">All</option>

                            @foreach ($locationInOptions as $location)

                                <option value="{{ $location }}" {{ request('location_in') == $location ? 'selected' : '' }}>{{ $location }}</option>

                            @endforeach

                        </select>

                    </div>

                    <!-- Location Out -->

                    <div class="col-6 col-lg-2">

                        <label class="form-label fw-bold small mb-1">Location Out</label>

                        <select name="location_out" class="form-select">

                            <option value="">All</option>

                            @foreach ($locationOutOptions as $location)

                                <option value="{{ $location }}" {{ request('location_out') == $location ? 'selected' : '' }}>{{ $location }}</option>

                            @endforeach

                        </select>

                    </div>

                    <!-- Buttons -->

                    <div class="col-12 d-flex gap-2 mt-2">

                        <button type="submit" class="btn btn-primary">

                            <i class="bi bi-funnel me-1"></i>Filter

                        </button>

                        <a href="{{ route('admin.attendance') }}" class="btn btn-outline-secondary">

                            <i class="bi bi-arrow-clockwise me-1"></i>Reset

                        </a>

                        <a href="{{ route('attendance.export') }}" class="btn btn-success ms-auto">

                            <i class="bi bi-file-earmark-excel me-1"></i>Export

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>



    <!-- Desktop Table View -->

    <div class="card d-none d-md-block mb-4">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Employee</th>

                            <th>Date</th>

                            <th>Time In</th>

                            <th>Loc In</th>

                            <th>Time Out</th>

                            <th>Loc Out</th>

                            <th>Time Slip</th>

                            <th width="50">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($attendances as $attendance)

                            <tr>

                                <td class="fw-medium">{{ $attendance->employee->full_name }}</td>

                                <td>{{ $attendance->date?->format('d M Y') }}</td>

                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <span>{{ $attendance->time_in?->format('g:i A') ?? '-' }}</span>

                                        @if($attendance->status_time_in)

                                            <span class="badge bg-{{ $attendance->status_time_in == 'Late' ? 'danger' : 'success' }}">

                                                {{ $attendance->status_time_in }}

                                            </span>

                                        @endif

                                    </div>

                                </td>

                                <td>{{ $attendance->location_in ?? '-' }}</td>

                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <span>{{ $attendance->time_out?->format('g:i A') ?? '-' }}</span>

                                        @if($attendance->status_time_out)

                                            <span class="badge bg-{{ $attendance->status_time_out == 'Early Leave' ? 'danger' : 'success' }}">

                                                {{ $attendance->status_time_out }}

                                            </span>

                                        @endif

                                    </div>

                                </td>

                                <td>{{ $attendance->location_out ?? '-' }}</td>

                                <td>

                                    @if($attendance->time_slip_start && $attendance->time_slip_end)

                                        {{ $attendance->time_slip_start->format('g:i A') }} - {{ $attendance->time_slip_end->format('g:i A') }}

                                    @else

                                        <span class="text-muted">N/A</span>

                                    @endif

                                </td>

                                <td>

                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceModal{{ $attendance->id }}">

                                        <i class="bi bi-pencil-square"></i>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-4 text-muted">

                                    <i class="bi bi-inbox me-2"></i>No attendance records found

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- Mobile Card View -->

    <div class="d-block d-md-none">

        @forelse ($attendances as $attendance)

            <div class="card mb-3">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-2">

                        <div>

                            <div class="fw-bold">{{ $attendance->employee->full_name }}</div>

                            <div class="text-muted small">{{ $attendance->date?->format('d M Y') }}</div>

                        </div>

                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#attendanceModal{{ $attendance->id }}">

                            <i class="bi bi-pencil-square"></i>

                        </button>

                    </div>

                    <div class="row">

                        <div class="col-6">

                            <div class="small text-muted">IN</div>

                            <div class="d-flex align-items-center gap-2 mb-1">

                                <span>{{ $attendance->time_in?->format('g:i A') ?? '-' }}</span>

                                @if($attendance->status_time_in)

                                    <span class="badge bg-{{ $attendance->status_time_in == 'Late' ? 'danger' : 'success' }}">{{ $attendance->status_time_in }}</span>

                                @endif

                            </div>

                            <div class="small text-muted">{{ $attendance->location_in ?? '-' }}</div>

                        </div>

                        <div class="col-6">

                            <div class="small text-muted">OUT</div>

                            <div class="d-flex align-items-center gap-2 mb-1">

                                <span>{{ $attendance->time_out?->format('g:i A') ?? '-' }}</span>

                                @if($attendance->status_time_out)

                                    <span class="badge bg-{{ $attendance->status_time_out == 'Early Leave' ? 'danger' : 'success' }}">{{ $attendance->status_time_out }}</span>

                                @endif

                            </div>

                            <div class="small text-muted">{{ $attendance->location_out ?? '-' }}</div>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="text-center py-5 text-muted">

                <i class="bi bi-inbox display-4"></i>

                <p class="mt-2">No attendance records found</p>

            </div>

        @endforelse

    </div>



    <!-- Modals -->

    @foreach ($attendances as $attendance)

        <div class="modal fade" id="attendanceModal{{ $attendance->id }}" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-dialog-scrollable modal-lg">

                <div class="modal-content">

                    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">

                        @csrf

                        @method('PUT')

                        <div class="modal-header">

                            <h5 class="modal-title">Attendance Details ({{ $attendance->date?->format('d M Y') }})</h5>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                        </div>

                        <div class="modal-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">Punch In Location</label>

                                    <div id="map-in-{{ $attendance->id }}" data-lat="{{ $attendance->time_in_lat }}" data-lng="{{ $attendance->time_in_lng }}" style="height: 200px;" class="rounded bg-light"></div>

                                    <small class="text-muted">{{ $attendance->location_in ?? 'No location' }}</small>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold">Punch Out Location</label>

                                    <div id="map-out-{{ $attendance->id }}" data-lat="{{ $attendance->time_out_lat }}" data-lng="{{ $attendance->time_out_lng }}" style="height: 200px;" class="rounded bg-light"></div>

                                    <small class="text-muted">{{ $attendance->location_out ?? 'No location' }}</small>

                                </div>

                            </div>

                            <div class="mb-3">

                                <label class="form-label fw-bold">Late Reason</label>

                                @if($attendance->status_time_in === 'Late')

                                    <textarea name="late_reason" class="form-control" rows="2" placeholder="Reason for being late">{{ $attendance->late_reason }}</textarea>

                                @else

                                    <p class="text-muted mb-0">N/A</p>

                                @endif

                            </div>

                            <div class="mb-3">

                                <label class="form-label fw-bold">Early Leave Reason</label>

                                @if($attendance->status_time_out === 'Early Leave')

                                    <textarea name="early_leave_reason" class="form-control" rows="2" placeholder="Reason for leaving early">{{ $attendance->early_leave_reason }}</textarea>

                                @else

                                    <p class="text-muted mb-0">N/A</p>

                                @endif

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                            <button type="submit" class="btn btn-primary">Save Changes</button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endforeach



    <!-- Google Maps API -->

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}"></script>

    <script>

        document.addEventListener('shown.bs.modal', function(event) {

            const modal = event.target;

            modal.querySelectorAll('[id^="map-"]').forEach(el => {

                const lat = parseFloat(el.dataset.lat);

                const lng = parseFloat(el.dataset.lng);

                if (lat && lng) {

                    const map = new google.maps.Map(el, { zoom: 15, center: { lat, lng }, disableDefaultUI: true });

                    new google.maps.Marker({ position: { lat, lng }, map: map });

                }

            });

        });

    </script>

@endsection