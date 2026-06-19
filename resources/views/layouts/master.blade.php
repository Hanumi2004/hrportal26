<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>@yield('title', 'Al-Hidayah Group HR Portal')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" type="image/png" href="{{ asset('assets/img/ahglogonobg.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/simple-calendar/simple-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hrportal.css') }}">

    @stack('styles')

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 75px;
            --topbar-height: 70px;
            --transition-speed: 0.3s;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-navbar {
            height: var(--topbar-height);
            z-index: 1040;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            position: fixed;
            top: 0;
            width: 100%;
        }

        .layout-shell {
            display: flex;
            padding-top: var(--topbar-height);
            flex: 1 0 auto;
            min-height: 0;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background: #fff;
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            height: auto;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1030;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar-header {
            flex-shrink: 0;
        }

        .sidebar-nav {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
            min-width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-title {
            display: none;
        }

        .sidebar.collapsed .nav-link i,
        .sidebar.collapsed .nav-group-title i {
            margin-right: 0 !important;
            font-size: 1.2rem;
            min-width: 24px;
            text-align: center;
        }

        .sidebar.collapsed .nav-link,
        .sidebar.collapsed .nav-group-title {
            justify-content: center !important;
        }

        .sidebar.collapsed .nav-submenu {
            display: none !important;
        }

        .main-content {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) ease, width var(--transition-speed) ease;
            width: calc(100% - var(--sidebar-width));
            min-height: 0;
        }

        .sidebar.collapsed + .sidebar-overlay + .main-content,
        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1025;
        }

        .app-footer {
            flex-shrink: 0;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            background: #fff;
            border-top: 1px solid #e9ecef;
            transition: margin-left var(--transition-speed) ease, width var(--transition-speed) ease;
        }

        .sidebar.collapsed ~ .app-footer {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        @media (max-width: 991.98px) {
            .sidebar {
                left: -100%;
                width: 280px;
                min-width: 280px;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content,
            .app-footer {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        .punch-link {
            font-weight: 600;
            color: #212529;
            text-decoration: none;
        }

        .punch-link i {
            font-size: 1.2rem;
            margin-right: 5px;
        }

        .logout-link {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
        }

        .sidebar .nav-link {
            color: #495057;
            border-radius: 0;
            transition: background-color 0.2s ease, color 0.2s ease;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: #f8f9fa;
            color: #0d6efd;
        }

        .sidebar .nav-link.active {
            background: #eaf2ff;
            color: #0d6efd;
            font-weight: 600;
        }

        .nav-group {
            margin-bottom: 2px;
        }

        .nav-group-title {
            color: #6c757d;
            font-weight: 600;
            cursor: default;
            background: transparent;
        }

        .nav-group-title.active {
            background: #eef4ff;
            color: #0d6efd;
        }

        .nav-submenu {
            display: block;
            padding-bottom: 4px;
        }

        .submenu-link {
            padding-left: 3rem !important;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
<nav class="top-navbar d-flex align-items-center">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <button class="btn border-0 shadow-none me-2" id="sidebarToggle" type="button">
                <i class="bi bi-list fs-3"></i>
            </button>

            @auth
                <a class="navbar-brand d-flex align-items-center"
                   href="{{ Auth::user()->role_id == '2' || Auth::user()->role_id == '1' ? route('admin.dashboard') : route('employee.dashboard') }}">
                    <img src="{{ asset('assets/img/ahglogonobg.png') }}" alt="Logo"
                         style="height: 45px; width: auto; margin-right: 10px;">
                    <span class="d-none d-lg-inline fw-bold text-primary">Al-Hidayah Group HR Portal</span>
                </a>
            @endauth
        </div>

        <div class="top-nav-items d-flex align-items-center">
            <div class="nav-item dropdown me-3">
                <a class="nav-link position-relative" id="notificationBell" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-bell fs-5"></i>
                    @if (auth()->user()->unreadNotifications->count())
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              id="notificationCount">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>

                <div class="dropdown-menu dropdown-menu-end shadow border-0 py-0" style="min-width: 300px;">
                    <div class="p-2 border-bottom fw-bold text-center">Notifications</div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <a href="{{ $notification->type === 'App\\\\Notifications\\\\EventReminderNotification' ? route('event.index.admin') : route('announcement.index.admin') }}"
                               class="dropdown-item p-3 border-bottom">
                                <div class="fw-bold text-truncate">
                                    {{ $notification->data['title'] ?? $notification->data['message'] }}
                                </div>
                                <small class="text-muted d-block text-truncate">
                                    {{ $notification->data['message'] ?? $notification->data['content'] }}
                                </small>
                                <small class="text-primary" style="font-size: 0.7rem;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">No new notifications</div>
                        @endforelse
                    </div>
                </div>
            </div>

            @auth
                @if (Auth::user()->role_id !== 2)
                    <a href="#" id="punchLink" class="punch-link me-3 d-flex align-items-center"
                       data-punched="{{ isset($isPunchedIn) && $isPunchedIn ? 'true' : 'false' }}">
                        <i id="punchIcon"
                           class="bi {{ isset($isPunchedIn) && $isPunchedIn ? 'bi-person-x text-danger' : 'bi-person-check text-success' }}"></i>
                        <span id="punchText" class="d-none d-md-inline">
                            {{ isset($isPunchedIn) && $isPunchedIn ? 'Punch Out' : 'Punch In' }}
                        </span>
                    </a>
                @endif

                <a class="logout-link d-flex align-items-center" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    <span class="d-none d-md-inline">Logout</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            @endauth
        </div>
    </div>
</nav>

<div class="layout-shell">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header p-3 d-flex justify-content-between align-items-center border-bottom">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/ahglogonobg.png') }}" alt="Logo"
                     style="height: 30px; margin-right: 8px;">
                <span class="fw-bold text-primary sidebar-title">Menu</span>
            </div>
            <button class="btn btn-sm btn-light d-lg-none" id="sidebarClose" type="button">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="sidebar-nav py-3">
            @auth
                @php
                    $taskProjectOpen = request()->routeIs(
                        'task.index.*',
                        'project.index.*',
                        'task.assignment.approvals'
                    );
                @endphp

                <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.dashboard', 'employee.dashboard') ? 'active' : '' }}"
                   href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2' ? route('admin.dashboard') : route('employee.dashboard') }}">
                    <i class="bi bi-house-door me-3"></i><span class="sidebar-text">Dashboard</span>
                </a>

                <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.attendance*', 'employee.attendance*') ? 'active' : '' }}"
                   href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2' ? route('admin.attendance') : route('employee.attendance') }}">
                    <i class="bi bi-clock-history me-3"></i><span class="sidebar-text">Attendance</span>
                </a>

                <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('leave*') ? 'active' : '' }}"
                   href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2' ? route('leave.index.admin') : route('leave.index.employee') }}">
                    <i class="bi bi-airplane me-3"></i><span class="sidebar-text">Leave</span>
                </a>

                <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('calendar*') ? 'active' : '' }}"
                   href="{{ route('calendar.index') }}">
                    <i class="bi bi-calendar3 me-3"></i><span class="sidebar-text">Calendar</span>
                </a>

                <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('event*') ? 'active' : '' }}"
                   href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2' ? route('event.index.admin') : route('event.index.employee') }}">
                    <i class="bi bi-calendar-event me-3"></i><span class="sidebar-text">Event</span>
                </a>

                <div class="nav-group {{ $taskProjectOpen ? 'open' : '' }}">
                    <div class="nav-link px-3 py-2 d-flex align-items-center nav-group-title {{ $taskProjectOpen ? 'active' : '' }}">
                        <i class="bi bi-ui-checks me-3"></i>
                        <span class="sidebar-text">Task & Project</span>
                    </div>

                    <div class="nav-submenu">
                        <a class="nav-link px-3 py-2 d-flex align-items-center submenu-link {{ request()->routeIs('task.index.*') ? 'active' : '' }}"
                           href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2'
                                ? route('task.index.admin')
                                : route('task.index.employee') }}">
                            <i class="bi bi-check2-circle me-3"></i>
                            <span class="sidebar-text">Tasks</span>
                        </a>

                        <a class="nav-link px-3 py-2 d-flex align-items-center submenu-link {{ request()->routeIs('project.index.*') ? 'active' : '' }}"
                           href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2'
                                ? route('project.index.admin')
                                : route('project.index.employee') }}">
                            <i class="bi bi-kanban me-3"></i>
                            <span class="sidebar-text">Projects</span>
                        </a>

                        @if (in_array(Auth::user()->role_id, [4, 5]))
                            <a class="nav-link px-3 py-2 d-flex align-items-center submenu-link {{ request()->routeIs('task.assignment.approvals') ? 'active' : '' }}"
                               href="{{ route('task.assignment.approvals') }}">
                                <i class="bi bi-check2-square me-3"></i>
                                <span class="sidebar-text">Assignment Approvals</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '2')
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('form*') ? 'active' : '' }}"
                       href="{{ route('form.admin') }}">
                        <i class="bi bi-file-earmark-text me-3"></i><span class="sidebar-text">Form</span>
                    </a>
                @else
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('form*') ? 'active' : '' }}"
                       href="{{ route('form.myforms') }}">
                        <i class="bi bi-file-earmark-text me-3"></i><span class="sidebar-text">Form</span>
                    </a>
                @endif

                @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '2')
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.requests*') ? 'active' : '' }}"
                       href="{{ route('admin.requests') }}">
                        <i class="bi bi-clipboard me-3"></i><span class="sidebar-text">Request</span>
                    </a>
                @else
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('employee.myrequests*') ? 'active' : '' }}"
                       href="{{ route('employee.myrequests') }}">
                        <i class="bi bi-clipboard me-3"></i><span class="sidebar-text">Request</span>
                    </a>
                @endif

                @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '2')
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('admin.employee*') ? 'active' : '' }}"
                       href="{{ route('admin.employee') }}">
                        <i class="bi bi-people me-3"></i><span class="sidebar-text">Employee</span>
                    </a>
                @endif

                <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('announcement*') ? 'active' : '' }}"
                   href="{{ Auth::user()->role_id == '1' || Auth::user()->role_id == '2' ? route('announcement.index.admin') : route('announcement.index.employee') }}">
                    <i class="bi bi-megaphone me-3"></i><span class="sidebar-text">Announcement</span>
                </a>

                @if (Auth::user()->role_id == '1' || Auth::user()->role_id == '2')
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('settings*') ? 'active' : '' }}"
                       href="{{ route('settings.index') }}">
                        <i class="bi bi-gear me-3"></i><span class="sidebar-text">System Setting</span>
                    </a>
                @endif

                @if (Auth::user()->role_id >= 3)
                    <a class="nav-link px-3 py-2 d-flex align-items-center {{ request()->routeIs('profile*') ? 'active' : '' }}"
                       href="{{ route('profile.show') }}">
                        <i class="bi bi-person-circle me-3"></i><span class="sidebar-text">Profile</span>
                    </a>
                @endif
            @endauth
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content flex-grow-1">
        <div class="container-fluid pt-2 pb-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show shadow-sm border-0" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm border-0">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<footer class="app-footer text-center py-3 text-muted small">
    <div class="container">
        &copy; {{ date('Y') }} AHG HR Portal. Designed by AHG IT Team.
    </div>
</footer>

<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/feather.min.js') }}"></script>
<script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ical.js@1.5.0/build/ical.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        sidebarToggle?.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle('collapsed');
            } else {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            }
        });

        [sidebarClose, sidebarOverlay].forEach(el => {
            el?.addEventListener('click', function() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            });
        });

        const bell = document.getElementById('notificationBell');
        if (bell) {
            bell.addEventListener('show.bs.dropdown', function() {
                fetch("{{ route('notifications.readAll') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                });

                const badge = document.getElementById('notificationCount');
                if (badge) badge.remove();
            });
        }

        function sendPunch(url) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(`Success: ${data.time}\nStatus: ${data.status || data.status_time_in}`);
                        window.location.reload();
                    })
                    .catch(err => console.error(err));
                });
            } else {
                alert("Geolocation not supported.");
            }
        }

        document.getElementById('punchLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            const punched = this.dataset.punched === 'true';
            sendPunch(punched ? "{{ route('attendance.punchOut') }}" : "{{ route('attendance.punchIn') }}");
        });
    });
</script>

@stack('scripts')
</body>
</html>