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
                                        @if ($role_id == 2)
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                            </li>
                                        @else
                                            <li class="breadcrumb-item">
                                                <a href="{{ route('employee.dashboard') }}">Dashboard</a>
                                            </li>
                                        @endif
                                        <li class="breadcrumb-item active" aria-current="page">Calendar</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Calendar</h3>
                                <p class="text-muted">View company calendar events.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-10 align-items-center">
                    <strong class="me-2">Show:</strong>

                    <label class="form-check-label">
                        <input class="form-check-input calendar-toggle" type="checkbox" checked data-type="event">
                        🎉 Events
                    </label>

                    <label class="form-check-label">
                        <input class="form-check-input calendar-toggle" type="checkbox" checked data-type="birthday">
                        🎂 Birthdays
                    </label>

                    <label class="form-check-label">
                        <input class="form-check-input calendar-toggle" type="checkbox" checked data-type="leave">
                        🌴 Leaves
                    </label>

                    <label class="form-check-label">
                        <input class="form-check-input calendar-toggle" type="checkbox" checked data-type="holiday">
                        🇲🇾 Public Holiday
                    </label>

                    @if ($role_id == 2)
                        <label class="form-check-label">
                            <input class="form-check-input calendar-toggle" type="checkbox" checked data-type="contract_end">
                            ⏳ Contract / intern / employment / probation / suspension end
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl || typeof FullCalendar === 'undefined') return;

        const fullEvents = @json($calendarEvents ?? []);

        let activeTypes = new Set([
            'event', 'birthday', 'leave', 'holiday', 'contract_end'
        ]);

        let filteredEvents = fullEvents.filter(event => activeTypes.has(event.type));

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: filteredEvents
        });

        calendar.render();

        if (typeof ICAL !== 'undefined') {
            fetch("/holidays")
                .then(response => response.text())
                .then(icsData => {
                    const jcalData = ICAL.parse(icsData);
                    const comp = new ICAL.Component(jcalData);
                    const vevents = comp.getAllSubcomponents("vevent");

                    const holidayEvents = vevents.map((evt) => {
                        const event = new ICAL.Event(evt);
                        const start = event.startDate.toJSDate();

                        return {
                            title: '🏛 ' + event.summary,
                            start: start.toISOString().split('T')[0],
                            color: '#a78bfa',
                            type: 'holiday',
                        };
                    });

                    fullEvents.push(...holidayEvents);
                    filteredEvents = fullEvents.filter(event => activeTypes.has(event.type));
                    calendar.removeAllEvents();
                    calendar.addEventSource(filteredEvents);
                })
                .catch(err => console.error("Failed to load holidays:", err));
        }

        document.querySelectorAll('.calendar-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const type = this.dataset.type;

                if (this.checked) {
                    activeTypes.add(type);
                } else {
                    activeTypes.delete(type);
                }

                filteredEvents = fullEvents.filter(event => activeTypes.has(event.type));
                calendar.removeAllEvents();
                calendar.addEventSource(filteredEvents);
            });
        });
    });
</script>
@endpush