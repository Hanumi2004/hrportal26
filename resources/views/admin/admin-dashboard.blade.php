@extends('layouts.master')

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h3 class="page-title"><br>Dashboard</h3>
                                <p class="text-muted">Monitor team activities and system overview</p>
                            </div>
                            <div class="datetime-punch text-end">
                                <div class="datetime-time" id="currentTime"></div>
                                <div class="datetime-date" id="currentDate"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Summary & System Status Section -->
        <div class="row mb-4 g-4">
            <!-- Profile Card -->
            <div class="col-12 col-lg-8">
                <div class="card card-fixed-height border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4 d-flex flex-column justify-content-center">
                        <!-- Centered content vertically -->
                        <div class="d-flex align-items-center flex-column flex-md-row">
                            <!-- Avatar Section -->
                            <div class="position-relative mb-3 mb-md-0 me-md-4">
                                <img src="{{ Auth::user()->profile_photo_path ? asset('storage/' . Auth::user()->profile_photo_url) : asset('img/default-avatar.png') }}"
                                    alt="{{ Auth::user()->name }}"
                                    class="rounded-circle border border-4 border-light shadow-sm"
                                    style="width:120px; height:120px; object-fit:cover;">
                            </div>

                            <!-- User Info Section -->
                            <div class="flex-grow-1 text-center text-md-start">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                                    <div>
                                        <small>Welcome,<br></small>
                                        <h3 class="mb-0 fw-bold text-dark mb-2">{{ Auth::user()->name }}</h3>
                                        <span class="badge bg-primary-soft text-primary rounded-pill px-3 py-1 mb-3"
                                            style="font-size: 0.75rem;">
                                            <i class="bi bi-shield-check me-1"></i>
                                            {{ ucfirst(Auth::user()->role->role_name) }}
                                        </span>
                                    </div>
                                    <div class="mt-md-0">
                                        <a href="{{ route('profile.settings.admin') }}"
                                            class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm">
                                            <i class="bi bi-gear-fill me-1"></i> Account Settings
                                        </a>
                                    </div>
                                </div>

                                <!-- Details Section (Adjusted spacing) -->
                                <div class="row g-3 mt-1 pt-3 border-top">
                                    <div class="col-sm-auto me-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 text-primary me-3">
                                                <i class="bi bi-person-badge fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block lh-1 mb-1" style="font-size: 0.7rem;">USER
                                                    ID</small>
                                                <span class="fw-bold text-dark">#{{ Auth::user()->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 text-primary me-3">
                                                <i class="bi bi-envelope fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block lh-1 mb-1" style="font-size: 0.7rem;">EMAIL
                                                    ADDRESS</small>
                                                <span class="fw-bold text-dark">{{ Auth::user()->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status Card -->
            <div class="col-12 col-lg-4">
                <div class="card card-fixed-height border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark">System Overview</h5>
                            <span class="badge bg-soft-success text-success px-3 py-1">Live</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            <div
                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-soft rounded-circle me-3 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="bi bi-database text-primary"></i>
                                    </div>
                                    <span class="fw-medium text-dark">Database</span>
                                </div>
                                <span class="text-success small fw-bold"><i class="bi bi-circle-fill me-1"
                                        style="font-size: 6px;"></i> Connected</span>
                            </div>

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info-soft rounded-circle me-3 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="bi bi-people text-info"></i>
                                    </div>
                                    <span class="fw-medium text-dark">Active Users</span>
                                </div>
                                <span
                                    class="badge bg-light text-dark fw-bold rounded-pill px-3">{{ $totalEmployees ?? 0 }}</span>
                            </div>

                            <div
                                class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning-soft rounded-circle me-3 d-flex align-items-center justify-content-center"
                                        style="width: 36px; height: 36px;">
                                        <i class="bi bi-clock-history text-warning"></i>
                                    </div>
                                    <span class="fw-medium text-dark">Sync Status</span>
                                </div>
                                <div class="text-end">
                                    <div class="text-dark small fw-bold lh-1">{{ now()->format('G:i A') }}</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">Last update</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-4">
            <!-- Left: Today's Attendance -->
            <div class="col-12 col-lg-8">
                <div class="card card-fixed-height h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Today's Attendance</h5>
                        <div class="text-end">
                            <div class="text-muted small">{{ \Carbon\Carbon::today()->format('l') }}</div>
                            <div class="text-primary fw-semibold">
                                {{ \Carbon\Carbon::today()->format('F j, Y') }}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Attendance Stats with Clickable Cards -->
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="attendance-stat-card present-card" data-filter="present">
                                    <h4 class="text-success">{{ $presentToday }}</h4>
                                    <small class="text-muted">Present</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="attendance-stat-card absent-card" data-filter="absent">
                                    <h4 class="text-danger">{{ $absentToday }}</h4>
                                    <small class="text-muted">Absent</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="attendance-stat-card late-card" data-filter="late">
                                    @php
                                        $lateToday = \App\Models\Attendance::whereDate('date', \Carbon\Carbon::today())
                                            ->where('status_time_in', 'Late')
                                            ->count();
                                    @endphp
                                    <h4 class="text-warning">{{ $lateToday }}</h4>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Details Section -->
                        <div class="attendance-details">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0" id="attendanceTitle">All Employees</h6>
                                <button class="btn btn-sm btn-outline-secondary" id="showAllEmployees">
                                    <i class="bi bi-arrow-counterclockwise"></i> Show All
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Status</th>
                                            <th>Punch In</th>
                                            <th>Punch Out</th>
                                            <th>Department</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTable">
                                        <!-- Data will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                            <div id="noAttendanceData" class="text-center text-muted py-4 d-none">
                                <i class="bi bi-people display-4"></i>
                                <p class="mt-2">No employees found</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Announcements -->
            <div class="col-12 col-lg-4">
                <div class="card card-fixed-height h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Announcements</h5>
                        <a href="{{ route('announcement.index.admin') }}" class="btn btn-outline-primary btn-sm">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="announcement-list">
                            @forelse($announcements as $announcement)
                                <div class="announcement-item mb-3 p-2 hover-bg" style="border-radius: 8px;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0">{{ $announcement->title }}</h6>
                                        <span
                                            class="badge 
                                            @if ($announcement->priority == 'high') bg-danger 
                                            @elseif($announcement->priority == 'medium') bg-warning 
                                            @else bg-info @endif">
                                            {{ ucfirst($announcement->priority) }}
                                        </span>
                                    </div>
                                    <p class="text-muted mb-1 small">{{ Str::limit($announcement->description, 100) }}</p>
                                    <small class="text-muted">Posted at:
                                        {{ $announcement->created_at->format('M j, g:i A') }}</small>
                                </div>
                            @empty
                                <p class="text-muted small">No recent announcements found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4 g-4">

            <!-- Left: Leave/Time Slip Requests -->
            <div class="col-12 col-lg-8">
                <div class="card card-fixed-height h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Leave & Time Slip Requests</h5>
                        <a href="{{ route('admin.requests') }}" class="btn btn-outline-primary btn-sm">
                            View All <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Pending Requests Summary -->
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="request-stat-card" style="cursor: pointer;">
                                    <h4 class="text-warning">{{ $totalPending }}</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="request-stat-card" style="cursor: pointer;">
                                    <h4 class="text-success">{{ $totalApproved }}</h4>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="request-stat-card" style="cursor: pointer;">
                                    <h4 class="text-danger">{{ $totalRejected }}</h4>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Requests -->
                        <h6 class="mb-4">Recent Requests</h6>
                        <div class="request-list">
                            @forelse($recentRequests as $request)
                                <div class="request-item d-flex justify-content-between align-items-center mb-3 p-2 hover-bg"
                                    onclick="window.location.href='{{ route('admin.requests') }}'"
                                    style="cursor: pointer; border-radius: 8px;">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-person text-muted"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $request['employee'] }}</strong>
                                            <div class="text-muted small">
                                                {{ $request['type'] }}
                                                @if ($request['is_time_slip'])
                                                    <span class="badge bg-info ms-1">Time Slip</span>
                                                @else
                                                    <span class="badge bg-primary ms-1">Leave</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $request['duration'] }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge 
                                            @if ($request['status'] == 'pending') bg-warning 
                                            @elseif($request['status'] == 'approved') bg-success 
                                            @else bg-danger @endif">
                                            {{ ucfirst($request['status']) }}
                                        </span>
                                        <div class="text-muted small mt-1">{{ $request['submitted_date'] }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small">No pending requests found.</p>
                            @endforelse
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right: Recent Activities -->
            <div class="col-12 col-lg-4">
                <div class="card card-fixed-height h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-timeline">
                            @forelse($recentActivities as $activity)
                                <div class="activity-item d-flex">
                                    <div class="activity-icon me-3">
                                        <i class="bi bi-{{ $activity['icon'] }} text-primary"></i>
                                    </div>
                                    <div class="activity-content">
                                        <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                        <p class="text-muted mb-1">{{ $activity['description'] }}</p>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    <i class="bi bi-inbox"></i>
                                    <p class="mt-2">No recent activities</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sample data - replace with real data from controller
            const employees = @json($allEmployees);
            const attendanceData = @json($todayAttendance);

            const attendanceTable = document.getElementById('attendanceTable');
            const noAttendanceData = document.getElementById('noAttendanceData');
            const attendanceTitle = document.getElementById('attendanceTitle');

            // function to render employee table
            function renderEmployees(filter = 'all') {
                let filteredEmployees = employees;

                if (filter === 'present') {
                    filteredEmployees = employees.filter(emp =>
                        attendanceData.some(att => att.employee_id === emp.employee_id && att.time_in)
                    );
                    attendanceTitle.textContent = 'Present Employees';
                } else if (filter === 'absent') {
                    filteredEmployees = employees.filter(emp =>
                        !attendanceData.some(att => att.employee_id === emp.employee_id && att.time_in)
                    );
                    attendanceTitle.textContent = 'Absent Employees';
                } else if (filter === 'late') {
                    filteredEmployees = employees.filter(emp => {
                        const empAttendance = attendanceData.find(att => att.employee_id === emp
                            .employee_id);
                        return empAttendance && empAttendance.status_time_in === 'Late';
                    });
                    attendanceTitle.textContent = 'Late Employees';
                } else {
                    attendanceTitle.textContent = 'All Employees';
                }

                // Clear table
                attendanceTable.innerHTML = '';

                if (filteredEmployees.length === 0) {
                    noAttendanceData.classList.remove('d-none');
                    return;
                }

                noAttendanceData.classList.add('d-none');

                // Populate table
                filteredEmployees.forEach(employee => {
                    const attendance = attendanceData.find(att => att.employee_id === employee.employee_id);

                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-person text-muted"></i>
                        </div>
                        <div>
                            <strong>${employee.full_name}</strong>
                            <br>
                            <small class="text-muted">${employee.employee_id}</small>
                            <br>
                            <small class="text-muted">${employee.position}</small>
                        </div>
                    </div>
                </td>
                <td>
                    ${attendance ? 
                        `<span class="badge bg-success">Present</span>` : 
                        `<span class="badge bg-danger">Absent</span>`
                    }
                    ${attendance && attendance.status_time_in === 'Late' ? 
                        `<br><small class="text-warning">Late</small>` : ''
                    }
                </td>
                <td>${attendance ? attendance.time_in : '-'}</td>
                <td>${attendance ? (attendance.time_out || '-') : '-'}</td>
                <td>${employee.department}</td>
            `;
                    attendanceTable.appendChild(row);
                });
            }

            // Click handlers for attendance cards
            document.querySelectorAll('.attendance-stat-card').forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    renderEmployees(filter);

                    // Update active state
                    document.querySelectorAll('.attendance-stat-card').forEach(c => {
                        c.style.opacity = '1';
                    });
                    this.style.opacity = '0.8';
                });
            });

            // Show all employees
            document.getElementById('showAllEmployees').addEventListener('click', function() {
                renderEmployees('all');
                document.querySelectorAll('.attendance-stat-card').forEach(c => {
                    c.style.opacity = '1';
                });
            });

            // Initial render
            renderEmployees('all');
        });

        // Update date and time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            document.getElementById('currentDate').textContent = now.toLocaleDateString(undefined, dateOptions);
            document.getElementById('currentTime').textContent = now.toLocaleTimeString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        document.addEventListener('DOMContentLoaded', function() {
            // Make request items clickable
            document.querySelectorAll('.request-item').forEach(item => {
                item.addEventListener('click', function() {
                    // Navigation is handled by onclick attribute
                });
            });
        });
    </script>
@endsection
