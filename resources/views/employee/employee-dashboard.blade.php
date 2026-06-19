@extends('layouts.master')


@section('content')


    <style>
        .quick-action-card {
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.25s ease;
        }

        .quick-action-card:hover {
            background: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .quick-action-card strong {
            font-size: 1.1rem;
            /* main title */
            font-weight: 600;
            line-height: 1.3;
        }

        .quick-action-card small {
            font-size: 0.9rem;
            /* description */
            line-height: 1.4;
        }

        .quick-action-card .icon {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
    </style>
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <h3 class="page-title"><br>Dashboard</h3>
                                <p class="text-muted">Monitor your activities and work overview</p>
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
										
                                        <a href="{{ route('profile.settings.employee') }}"
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
                                    <div class="col-sm-auto me-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 text-primary me-3">
                                                <i class="bi bi-person fs-5"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block lh-1 mb-1"
                                                    style="font-size: 0.7rem;">EMPLOYEE
                                                    ID</small>
                                                <span class="fw-bold text-dark">{{ $employee->employee_id }}</span>
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
                            <h5 class="mb-0 fw-bold text-dark">Today's Attendance</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            @if ($todayAttendance)
                                <div
                                    class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info-soft rounded-circle me-3 d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px;">
                                            <i class="bi bi-box-arrow-in-right text-info"></i>
                                        </div>
                                        <span class="fw-medium text-dark">Punch In</span>
                                    </div>
                                    <span class="text-dark small fw-bold">
                                        {{ $todayAttendance->time_in ? $todayAttendance->time_in->format('g:i A') : '-' }}
                                    </span>
                                </div>

                                <div
                                    class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info-soft rounded-circle me-3 d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px;">
                                            <i class="bi bi-box-arrow-right text-info"></i>
                                        </div>
                                        <span class="fw-medium text-dark">Punch Out</span>
                                    </div>
                                    <span class="fw-medium text-dark">
                                        {{ $todayAttendance->time_out ? $todayAttendance->time_out->format('g:i A') : '-' }}
                                    </span>
                                </div>

                                <div
                                    class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning-soft rounded-circle me-3 d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px;">
                                            <i class="bi bi-clock-history text-warning"></i>
                                        </div>
                                        <span class="fw-medium text-dark">Attendance Status</span>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge rounded-pill
                                            @if ($todayAttendance->time_in) {{ $todayAttendance->status_time_in === 'Late' ? 'bg-warning' : 'bg-success' }}
                                            @else
                                                bg-danger @endif
                                        ">
                                            @if ($todayAttendance->time_in)
                                                {{ $todayAttendance->status_time_in === 'Late' ? 'Late' : 'Present' }}
                                            @else
                                                Absent
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <p><i class="bi bi-clock-history"></i>
                                        No attendance record for today</p>
                                </div>
                            @endif
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
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- New Task --}}
                            <div class="col-12 col-md-6">
                                <a href="{{ $isReadOnly ? '#' : route('task.create') }}"
								   class="quick-action-card d-flex align-items-center p-3 h-100 text-decoration-none {{ $isReadOnly ? 'disabled pe-none opacity-50' : '' }}"
								   @if($isReadOnly)
									   aria-disabled="true"
									   tabindex="-1"
									   title="Your account is view-only. Please contact HR."
								   @endif
								>
									<div class="icon bg-info-soft text-info me-3">
										<i class="bi bi-ui-checks"></i>
									</div>
									<div>
										<small class="text-muted d-block">Create and assign tasks</small>
										<strong class="text-dark">New Task</strong>
									</div>
								</a>
                            </div>

                            {{-- New Project --}}
                            <div class="col-12 col-md-6">
                                <a href="{{ $isReadOnly ? '#' : route('project.create') }}"
                                    class="quick-action-card d-flex align-items-center p-3 h-100 text-decoration-none {{ $isReadOnly ? 'disabled pe-none opacity-50' : '' }}"
								   @if($isReadOnly)
									   aria-disabled="true"
									   tabindex="-1"
									   title="Your account is view-only. Please contact HR."
								   @endif
								   
								  >
                                    <div class="icon bg-primary-soft text-primary me-3">
                                        <i class="bi bi-ui-checks"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Start a new project</small>
                                        <strong class="text-dark">New Project</strong>
                                    </div>
                                </a>
                            </div>

                            {{-- New Leave --}}
                            <div class="col-12 col-md-6">
                                <a href="{{ $isReadOnly ? '#' : route('leave.create') }}"
                                    class="quick-action-card d-flex align-items-center p-3 h-100 text-decoration-none {{ $isReadOnly ? 'disabled pe-none opacity-50' : '' }}"
								   @if($isReadOnly)
									   aria-disabled="true"
									   tabindex="-1"
									   title="Your account is view-only. Please contact HR."
								   @endif
								   
								  >
                                    <div class="icon bg-info-soft text-info me-3">
                                        <i class="bi bi-airplane"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Submit leave request for approval</small>
                                        <strong class="text-dark">Apply Leave</strong>
                                    </div>
                                </a>
                            </div>

                            {{-- New Event --}}
                            <div class="col-12 col-md-6">
                                <a href="{{ $isReadOnly ? '#' : route('event.create') }}"
                                    class="quick-action-card d-flex align-items-center p-3 h-100 text-decoration-none {{ $isReadOnly ? 'disabled pe-none opacity-50' : '' }}"
								   @if($isReadOnly)
									   aria-disabled="true"
									   tabindex="-1"
									   title="Your account is view-only. Please contact HR."
								   @endif
								   
								  >
                                    <div class="icon bg-primary-soft text-primary me-3">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Create event and invite others</small>
                                        <strong class="text-dark">New Event</strong>
                                    </div>
                                </a>
                            </div>

                            {{-- New Form --}}
                            <div class="col-12 col-md-6">
                                <a href="{{ $isReadOnly ? '#' : route('form.work-handover.create') }}"
                                    class="quick-action-card d-flex align-items-center p-3 h-100 text-decoration-none {{ $isReadOnly ? 'disabled pe-none opacity-50' : '' }}"
								   @if($isReadOnly)
									   aria-disabled="true"
									   tabindex="-1"
									   title="Your account is view-only. Please contact HR."
								   @endif
								  
								  >
                                    <div class="icon bg-info-soft text-info me-3">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Prepare and submit form</small>
                                        <strong class="text-dark">Fill In Form</strong>
                                    </div>
                                </a>
                            </div>

                            {{-- New Time Slip --}}
                            <div class="col-12 col-md-6">
                                <a href="{{ $isReadOnly ? '#' : route('employee.attendance') }}"
                                    class="quick-action-card d-flex align-items-center p-3 h-100 text-decoration-none {{ $isReadOnly ? 'disabled pe-none opacity-50' : '' }}"
								   @if($isReadOnly)
									   aria-disabled="true"
									   tabindex="-1"
									   title="Your account is view-only. Please contact HR."
								   @endif
								   
								  >
                                    <div class="icon bg-primary-soft text-primary me-3">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Submit time slip request for approval</small>
                                        <strong class="text-dark">Apply Time Slip</strong>
                                    </div>
                                </a>
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
                        <a href="{{ route('announcement.index.employee') }}" class="btn btn-outline-primary btn-sm">
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
                        <h5 class="card-title mb-0">My Leave & Time Slip Requests</h5>
                        <a href="{{ route('employee.myrequests') }}" class="btn btn-outline-primary btn-sm">
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
                                {{-- <div class="request-stat-card"
                                    style="cursor: pointer; border-radius: 8px; background: #fff3cd;">
                                    <h4 class="text-warning mb-0" id="empPendingCount">0</h4>
                                    <small class="text-muted">Pending</small>
                                </div> --}}
                            </div>
                            <div class="col-4">
                                <div class="request-stat-card" style="cursor: pointer;">
                                    <h4 class="text-success">{{ $totalApproved }}</h4>
                                    <small class="text-muted">Approved</small>
                                </div>
                                {{-- <div class="request-stat-card p-3"
                                    style="cursor: pointer; border-radius: 8px; background: #d4edda;">
                                    <h4 class="text-success mb-0" id="empApprovedCount">0</h4>
                                    <small class="text-muted">Approved</small>
                                </div> --}}
                            </div>
                            <div class="col-4">
                                <div class="request-stat-card" style="cursor: pointer;">
                                    <h4 class="text-danger">{{ $totalRejected }}</h4>
                                    <small class="text-muted">Rejected</small>
                                </div>
                                {{-- <div class="request-stat-card p-3"
                                    style="cursor: pointer; border-radius: 8px; background: #f8d7da;">
                                    <h4 class="text-danger mb-0" id="empRejectedCount">0</h4>
                                    <small class="text-muted">Rejected</small>
                                </div> --}}
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
                                            <strong>{{ Auth::user()->name }}</strong>
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
                        <h5 class="card-title mb-0">My Recent Activities</h5>
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
                                <div class="text-center text-muted">
                                    <p><i class="bi bi-hourglass-split"></i>
                                        No recent activities</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
