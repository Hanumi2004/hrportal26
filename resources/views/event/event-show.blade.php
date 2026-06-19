@extends('layouts.master')

@section('content')
    <style>
        /* Fix Title/Breadcrumb Overlap */
        .page-header {
            margin-bottom: 2rem;
        }

        .breadcrumb {
            margin-bottom: 0.5rem !important;
        }

        .page-title {
            font-weight: 800;
            font-size: 2.2rem;
            color: #1a233a;
            margin-top: 0;
            display: block;
            /* Ensures it sits below breadcrumb */
        }

        /* Hero Image Container */
        .event-hero-container {
            background: #f0f2f5;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            margin-bottom: 30px;
            position: relative;
        }

        .event-hero-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .hero-placeholder {
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }

        /* Side Passport Card */
        .event-passport {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #dee2e6;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .passport-header {
            background: #2d3748;
            color: white;
            padding: 20px;
            text-align: center;
        }

        /* Info Row Styling */
        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .icon-box {
            width: 42px;
            height: 42px;
            min-width: 42px;
            background: #f8f9fa;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            font-size: 1.2rem;
        }

        /* Attendance Progress */
        .attendance-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 18px;
        }

        .progress-custom {
            height: 8px;
            border-radius: 10px;
            margin: 12px 0;
        }

        .avatar-mini {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #6c757d;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            border: 2px solid #fff;
            margin-right: -8px;
        }

        .btn-custom {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px 20px;
        }
    </style>

    <div class="content container-fluid">
        <!-- Header: Fixed Layout -->
        <div class="page-header d-md-flex justify-content-between align-items-end">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        @if ($role_id == 2)
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                        @endif

                        @if ($role_id == 2)
                            <li class="breadcrumb-item"><a href="{{ route('event.index.admin') }}">Event</a></li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ route('event.index.employee') }}">Event</a></li>
                        @endif

                        <li class="breadcrumb-item active" aria-current="page">{{ $event->event_name }}</li>
                    </ol>
                </nav>
                <h2 class="page-title">{{ $event->event_name }}</h2>
            </div>
            <div class="d-flex gap-2 mb-2">
                <button class="btn btn-outline-secondary btn-custom"><i class="bi bi-share me-2"></i>Share</button>
                <a href="{{ route('event.edit', $event->id) }}" class="btn btn-primary btn-custom"
                    style="background-color: #3b82f6; border-color: #3b82f6;">
                    <i class="bi bi-pencil-square me-2"></i>Edit Event
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Main Column -->
            <div class="col-lg-8">
                <div class="event-hero-container">
                    @if ($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" class="event-hero-image" alt="Event Banner">
                    @else
                        <div class="hero-placeholder">
                            <i class="bi bi-image" style="font-size: 5rem; opacity: 0.2;"></i>
                        </div>
                    @endif
                </div>

                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 20px;">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <span class="badge bg-soft-primary text-primary border px-3 py-2 text-uppercase"
                            style="background: #eef2ff;">{{ $event->category?->name }}</span>
                        <span class="text-{{ $event->event_status === 'cancelled' ? 'danger' : 'success' }} fw-bold">
                            <i class="bi bi-dot" style="font-size: 1.5rem;"></i> {{ ucfirst($event->event_status) }}
                        </span>
                    </div>

                    <h4 class="fw-bold mb-3">About this event</h4>
                    <div class="text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                        {!! nl2br(e($event->description)) !!}
                    </div>

                    @if (!empty($event->tags))
                        <div class="mt-4">
                            @foreach (explode(',', $event->tags) as $tag)
                                <span class="badge border text-dark bg-light rounded-pill px-3 py-2 me-1">
                                    #{{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <div class="event-passport mb-4">
                    <div class="passport-header">
                        <small class="text-uppercase fw-bold opacity-75">Event Ticket</small>
                        <h5 class="mb-0 mt-1">Registration Details</h5>
                    </div>

                    <div class="p-4">
                        <div class="info-row">
                            <div class="icon-box"><i class="bi bi-calendar3"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Date</small>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($event->event_date)->format('D, M j, Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="icon-box"><i class="bi bi-clock"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Time</small>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <small class="text-muted text-uppercase fw-bold"
                                    style="font-size: 0.65rem;">Location</small>
                                <div class="fw-bold">{{ $event->event_location }}</div>
                            </div>
                        </div>

                        <!-- Attendance -->
                        <div class="attendance-container mt-2">
                            @php
                                $totalAssigned = $event->attendees->count();
                                $confirmedCount = $event->attendees->where('response_status', 'confirmed')->count();

                                $percent = $totalAssigned > 0 ? ($confirmedCount / $totalAssigned) * 100 : 0;
                            @endphp

                            <div class="attendance-container mt-2">
                                <div class="d-flex justify-content-between small">
                                    <span class="fw-bold">Attendance</span>
                                    <span class="text-muted">{{ $confirmedCount }}/{{ $totalAssigned }} confirmed</span>
                                </div>
                                <div class="progress progress-custom">
                                    <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                                </div>

                                <div class="d-flex align-items-center mt-2">
                                    @foreach ($event->attendees->where('response_status', 'confirmed')->take(5) as $attendee)
                                        <div class="avatar-mini">
                                            {{ substr($attendee->employee_id, -2) }}
                                        </div>
                                    @endforeach

                                    @if ($confirmedCount > 5)
                                        <span class="ms-2 small text-muted">+{{ $confirmedCount - 5 }} more</span>
                                    @else
                                        <span class="ms-3 small text-muted">Going</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        @php
                            $employeeId = auth()->user()->employee?->employee_id;

                            $myRSVP = $employeeId ? $event->attendees->firstWhere('employee_id', $employeeId) : null;
                        @endphp


                        @if ($myRSVP)
                            <div class="mt-4">
                                @if (!$myRSVP || $myRSVP->response_status === 'pending')
                                    <form action="{{ route('event.attendance.confirm', $event->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100 btn-custom">
                                            <i class="bi bi-check-circle me-2"></i> Confirm Attendance
                                        </button>
                                    </form>
                                    <form action="{{ route('event.attendance.decline', $event->id) }}" method="POST"
                                        class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary w-100 btn-custom">
                                            <i class="bi bi-x-circle me-2"></i> Decline
                                        </button>
                                    </form>
                                @elseif ($myRSVP->response_status === 'confirmed')
                                    <div class="alert alert-success border-0 text-center py-3 mb-0"
                                        style="background: #ecfdf5; color: #065f46; border-radius: 12px;">
                                        <i class="bi bi-check-circle-fill me-2"></i> You're on the list!
                                    </div>
                                @elseif ($myRSVP->response_status === 'declined')
                                    <div class="alert alert-danger border-0 text-center py-3 mb-0"
                                        style="background: #fef2f2; color: #991b1b; border-radius: 12px;">
                                        <i class="bi bi-x-circle-fill me-2"></i> You have declined the invitation.
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Organizer Info -->
                <div class="card border shadow-sm p-3" style="border-radius: 15px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-light text-secondary"><i class="bi bi-person-badge"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Event Created
                                By</small>
                            <div class="fw-bold">{{ $event->createdBy->name }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
