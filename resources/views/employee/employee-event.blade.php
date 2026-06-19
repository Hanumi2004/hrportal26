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
                                        <li class="breadcrumb-item active" aria-current="page">Event</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Event</h3>
                                <p class="text-muted">Manage your events and schedule.</p>
                            </div>
                            <button class="btn-new" onclick="window.location='{{ route('event.create') }}'">
                                New Event
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-4">

        <!-- Filters and Search -->
        <form method="GET" action="{{ route('event.index.employee') }}">
            <input type="hidden" name="tab" id="activeTabInput" value="event">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Search Events</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Name or tags...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="event_category_id" class="form-control">
                        <option value="">All Categories</option>
                        @foreach ($eventCategories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('event_category_id') == $category->id ? 'selected' : '' }}>
                                {{ ucfirst($category->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="event_status" class="form-control">
                        <option value="">All Status</option>
                        @foreach ($eventStatuses as $status)
                            <option value="{{ $status }}" {{ request('event_status') == $status ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="event_date" value="{{ request('event_date') }}" class="form-control">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('event.index.employee') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
        <div class="event-grid mt-4">
            @forelse($events as $event)
                @php
                    $now = \Carbon\Carbon::now();
                    $isPast = $event->event_date->lt($now);
                    $eventImage = $event->image
                        ? Storage::url($event->image) // public/storage/events
                        : asset('img/event-corporate.jpg'); // public/img-default image
                @endphp

                <div class="event-card">
                    {{-- Top Section: Image & Overlay Badges --}}
                    <div class="event-image-wrapper" onclick="window.location='{{ route('event.show', $event->id) }}'"
                        style="cursor: pointer;">
                        <img src="{{ $eventImage }}" alt="{{ $event->event_name }}">

                        {{-- TOP-RIGHT ACTION --}}
                        @if ($event->created_by === auth()->id())
                            <div class="event-actions dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                            data-bs-target="#deleteEventModal" data-event-id="{{ $event->id }}"
                                            data-event-name="{{ $event->event_name }}">
                                            <i class="bi bi-trash me-2"></i> Delete Event
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        <div class="event-category-badge">
                            {{ $event->category?->name }}
                        </div>

                        <span class="status-pill event-status-{{ strtolower($event->event_status) }}">
                            {{ ucfirst($event->event_status) }}
                        </span>
                    </div>

                    {{-- Middle Section: Content --}}
                    <div class="event-card-body">
                        <h3 class="event-title" onclick="window.location='{{ route('event.show', $event->id) }}'">
                            {{ $event->event_name }}
                        </h3>

                        {{-- Modernized Meta Container --}}
                        <div class="event-meta-container">
                            {{-- Visual Date Block --}}
                            <div class="date-block">
                                <span class="month">{{ $event->event_date->format('M') }}</span>
                                <span class="day">{{ $event->event_date->format('d') }}</span>
                            </div>

                            {{-- Time and Location Details --}}
                            <div class="meta-details">
                                <div class="detail-row">
                                    <i class="bi bi-watch"></i>
                                    <span>{{ $event->event_time->format('g:i A') }}</span>
                                </div>

                                <div class="detail-row">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span class="location-text">{{ $event->event_location }}</span>
                                </div>
                            </div>
                        </div>

                        <p class="event-description">
                            {{ Str::limit($event->description, 100) }}
                        </p>

                        @if ($event->tags)
                            <p class="event-tags">
                                @foreach (explode(',', $event->tags) as $tag)
                                    #{{ trim($tag) }}
                                @endforeach
                            </p>
                        @endif
                    </div>

                    {{-- Bottom Section: Dynamic Attendance Actions --}}
                    @php
                        $myAttendance = $event->attendees
                            ->where('employee_id', auth()->user()->employee?->employee_id)
                            ->first();
                    @endphp

                    @if (!$isPast)
                        <div class="event-card-footer">
                            @if ($myAttendance && $myAttendance->response_status === 'pending')
                                <div class="attendance-prompt">
                                    <div class="text-muted small mb-1 fw-bold">RSVP REQUIRED</div>
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('event.attendance.confirm', $myAttendance->id) }}"
                                            method="POST" class="flex-grow-1">
                                            @csrf
                                            <button class="btn btn-success w-100">Accept</button>
                                        </form>

                                        <form action="{{ route('event.attendance.decline', $myAttendance->id) }}"
                                            method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                                data-bs-target="#declineReasonModal"
                                                data-action="{{ route('event.attendance.decline', $myAttendance->id) }}"
                                                data-event-date="{{ $event->event_date->format('d M Y') }}">
                                                Decline
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            @elseif ($myAttendance && $myAttendance->response_status === 'confirmed')
                                <div class="status-confirmed d-flex align-items-center gap-2">
                                    <span style="font-size: 1.2rem;">✓</span> You accepted this invitation
                                </div>
                            @elseif ($myAttendance && $myAttendance->response_status === 'declined')
                                <div class="status-declined d-flex align-items-center gap-2">
                                    <span>✕</span> You declined this invitation
                                </div>
                            @else
                                <div class="text-muted small">
                                    Not assigned to this event
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="event-card-footer">
                            <span class="badge bg-light text-dark border w-100 py-2">Past Event</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No events found</h5>
                    <p class="text-muted">There are no events to display.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" id="declineReasonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="declineForm" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="declineReasonModalLabel">Enter Decline Reason for Event on <span
                                id="eventDateText"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="decline_reason" class="form-label">Reason</label>
                            <textarea name="decline_reason" class="form-control" rows="1"
                                placeholder="Please provide a reason for declining">{{ old('decline_reason') }}</textarea>
                            @error('decline_reason')
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('declineReasonModal');
            const form = document.getElementById('declineForm');

            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                const attendanceId = button.getAttribute('data-attendance-id');
                const eventDate = button.getAttribute('data-event-date');
                const actionTemplate = button.getAttribute('data-action');

                form.action = actionTemplate.replace(':id', attendanceId);
                document.getElementById('eventDateText').innerText = eventDate;
            });
        });
    </script>
@endsection
