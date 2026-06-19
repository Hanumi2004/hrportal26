@extends('layouts.master')

@section('content')
<div class="content container-fluid event-page">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-sub-header w-100">
                    <div class="d-flex justify-content-between align-items-center w-100 gap-3 flex-wrap">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Event</li>
                                </ol>
                            </nav>
                            <h3 class="page-title"><br>Event</h3>
                            <p class="text-muted">Manage events and schedule.</p>
                        </div>

                        <button class="btn btn-primary fw-semibold px-3 py-2 rounded-3"
                                onclick="window.location='{{ route('event.create') }}'">
                            New Event
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <form method="GET" action="{{ route('event.index.admin') }}">
            <input type="hidden" name="tab" id="activeTabInput" value="event">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-6 col-lg-3">
                            <label class="form-label">Search Events</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       class="form-control"
                                       placeholder="Name or tags...">
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label">Category</label>
                            <select name="event_category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($eventCategories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('event_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ ucfirst($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label">Status</label>
                            <select name="event_status" class="form-select">
                                <option value="">All Status</option>
                                @foreach ($eventStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request('event_status') == $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-lg-2">
                            <label class="form-label">Date</label>
                            <input type="date"
                                   name="event_date"
                                   value="{{ request('event_date') }}"
                                   class="form-control">
                        </div>

                        <div class="col-6 col-lg-1 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
                        </div>

                        <div class="col-6 col-lg-1 d-grid">
                            <a href="{{ route('event.index.admin') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="event-grid">
            @forelse($events as $event)
                @php
                    $eventImage = $event->image
                        ? Storage::url($event->image)
                        : asset('img/event-corporate.jpg');
                @endphp

                <div class="event-card">
                    <div class="event-image-wrapper">
                        <img src="{{ $eventImage }}" alt="{{ $event->event_name }}" class="event-image">

                        <div class="event-actions dropdown">
                            <button class="btn btn-sm" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('event.edit', $event->id) }}">
                                        <i class="bi bi-pencil me-2"></i>Edit Event
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger"
                                       href="#"
                                       data-bs-toggle="modal"
                                       data-bs-target="#deleteEventModal"
                                       data-event-id="{{ $event->id }}"
                                       data-event-name="{{ $event->event_name }}">
                                        <i class="bi bi-trash me-2"></i>Delete Event
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="event-category-badge">
                            {{ $event->category?->name }}
                        </div>

                        <span class="status-pill event-status-{{ strtolower($event->event_status) }}">
                            {{ ucfirst($event->event_status) }}
                        </span>
                    </div>

                    <div class="event-card-body">
                        <h3 class="event-title"
                            onclick="window.location='{{ route('event.show', $event->id) }}'">
                            {{ $event->event_name }}
                        </h3>

                        <div class="event-meta-container">
                            <div class="date-block">
                                <span class="month">{{ $event->event_date->format('M') }}</span>
                                <span class="day">{{ $event->event_date->format('d') }}</span>
                            </div>

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

                    <div class="event-card-footer">
                        <div class="d-flex flex-column gap-2">
                            <div class="small text-muted fw-bold">EVENT OVERVIEW</div>

                            <div class="d-flex justify-content-between small">
                                <span>Confirmed</span>
                                <span class="fw-bold text-success">
                                    {{ $event->attendees->where('response_status', 'confirmed')->count() }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between small">
                                <span>Declined</span>
                                <span class="fw-bold text-danger">
                                    {{ $event->attendees->where('response_status', 'declined')->count() }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between small">
                                <span>Pending</span>
                                <span class="fw-bold text-warning">
                                    {{ $event->attendees->where('response_status', 'pending')->count() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No events found</h5>
                    <p class="text-muted mb-0">There are no events to display.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteEventForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title">Delete Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Are you sure you want to delete
                        <strong id="deleteEventName"></strong>?
                    </p>
                    <p class="text-danger small mb-0">
                        This action cannot be undone.
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteEventModal');
        const form = document.getElementById('deleteEventForm');
        const nameText = document.getElementById('deleteEventName');

        modal?.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const eventId = button.getAttribute('data-event-id');
            const eventName = button.getAttribute('data-event-name');

            form.action = `/event/${eventId}`;
            nameText.textContent = eventName;
        });
    });
</script>
@endpush