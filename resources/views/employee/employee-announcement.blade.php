@extends('layouts.master')

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Announcement</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Announcement</h3>
                                <p class="text-muted">Manage and publish important announcements.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="announcementsCard">
        <div class="row">
            @forelse ($announcements as $announcement)
                <div class="col-md-6 mb-3 announcement-item" data-status="{{ $announcement->priority }}">
                    <div class="card h-100" data-bs-toggle="modal"
                        data-bs-target="#announcementModal{{ $announcement->id }}" style="cursor:pointer;">

                        <div class="card-body">
                            <div class="row">
                                <!-- Left side: Announcement details -->
                                <div class="col-12">
                                    @switch($announcement->priority)
                                        @case('low')
                                            <span class="badge bg-primary mb-3 p-2 me-2">Low Priority</span>
                                        @break

                                        @case('medium')
                                            <span class="badge bg-warning mb-3 p-2 me-2">Medium Priority</span>
                                        @break

                                        @case('high')
                                            <span class="badge bg-danger mb-3 p-2 me-2">High Priority</span>
                                        @break
                                    @endswitch

                                    @switch($announcement->category)
                                        @case('general')
                                            <span class="badge bg-purple mb-3 p-2">
                                                <i class="bi bi-info-circle me-1"></i>General
                                            </span>
                                        @break

                                        @case('policy')
                                            <span class="badge bg-light-blue mb-3 p-2">
                                                <i class="bi bi-file-text me-1"></i>Policy
                                            </span>
                                        @break

                                        @case('system')
                                            <span class="badge bg-light-red mb-3 p-2">
                                                <i class="bi bi-gear me-1"></i>System
                                            </span>
                                        @break

                                        @case('other')
                                            <span class="badge bg-light-green mb-3 p-2">
                                                <i class="bi bi-tag me-1"></i>Other
                                            </span>
                                        @break
                                    @endswitch

                                    <h5 class="fw-bold mb-2 text-dark">{{ $announcement->title }}</h5>
                                    <p class="text-muted mb-3">{{ $announcement->description }}</p>
                                </div>

                                <div class="announcement-meta mb-1">
                                    <i class="bi bi-person-fill me-1 text-secondary"></i>
                                    Published By: {{ $announcement->created_by }}
                                </div>

                                <small class="text-muted mb-1 d-block">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Created: {{ $announcement->created_at?->format('d M Y') ?? '-' }} |
                                    Updated: {{ $announcement->updated_at?->format('d M Y') ?? '-' }}
                                </small>

                                <small class="text-muted">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Expires: {{ $announcement->expires_date?->format('d M Y') ?? '-' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-4 text-muted no-announcements-static">
                        <i class="bi bi-megaphone display-6 mb-2"></i>
                        <p class="mb-0">No announcements found</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Announcement Detail Modals --}}
        @foreach ($announcements as $announcement)
            <div class="modal fade" id="announcementModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('announcement.update', $announcement->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">Announcement Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Title</th>
                                        <td>{{ $announcement->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $announcement->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{ ucfirst($announcement->category) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Priority</th>
                                        <td>{{ ucfirst($announcement->priority) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Published by</th>
                                        <td>{{ $announcement->created_by }}</td>
                                    </tr>
                                    <tr>
                                        <th>Expires Date</th>
                                        <td>{{ $announcement->expires_date?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $announcement->created_at?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $announcement->updated_at?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <script>
            document.querySelectorAll('.filter-card').forEach(card => {
                card.addEventListener('click', function() {
                    // remove active class from all cards 
                    document.querySelectorAll('.filter-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    let status = this.dataset.status;
                    let visibleCount = 0;

                    // Hide all existing static messages first
                    document.querySelectorAll(
                            '#announcementsCard .announcement-item, #announcementsCard .no-announcements-static'
                        )
                        .forEach(item => {
                            if (item.classList.contains('no-announcements-static')) {
                                item.style.display = 'none';
                            }
                        });

                    // Show/hide announcement items based on filter
                    document.querySelectorAll('#announcementsCard .announcement-item').forEach(item => {
                        if (status === 'all' || item.dataset.status === status) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    // Handle the no announcements message
                    const existingStaticMsg = document.querySelector(
                        '#announcementsCard .no-announcements-static');
                    let noAnnouncementsMessage = document.getElementById('noAnnouncementsMessage');

                    // Remove existing dynamic message if it exists
                    if (noAnnouncementsMessage) {
                        noAnnouncementsMessage.remove();
                    }

                    // If no announcements are visible, show appropriate message
                    if (visibleCount === 0) {
                        // If there's already a static message (from Blade), show it and update text
                        if (existingStaticMsg) {
                            existingStaticMsg.style.display = '';
                            existingStaticMsg.querySelector('p').textContent =
                                `No ${getAnnouncementStatusText(status).toLowerCase()} announcements found`;
                        } else {
                            // Create new dynamic message
                            noAnnouncementsMessage = document.createElement('div');
                            noAnnouncementsMessage.id = 'noAnnouncementsMessage';
                            noAnnouncementsMessage.className =
                                'text-center py-4 text-muted no-announcements-dynamic';
                            noAnnouncementsMessage.innerHTML = `
                            <i class="bi bi-megaphone display-6 mb-2"></i>
                            <p class="mb-0">No ${getAnnouncementStatusText(status).toLowerCase()} announcements found</p>
                            `;
                            document.querySelector('#announcementsCard .row').appendChild(
                                noAnnouncementsMessage);
                        }
                    } else {
                        // Hide static message if announcements are visible
                        if (existingStaticMsg) {
                            existingStaticMsg.style.display = 'none';
                        }
                    }
                });
            });

            function getAnnouncementStatusText(status) {
                const statusMap = {
                    'all': 'all',
                    'low': 'low',
                    'medium': 'medium',
                    'high': 'high'
                };
                return statusMap[status] || status;
            }
        </script>
    @endsection
