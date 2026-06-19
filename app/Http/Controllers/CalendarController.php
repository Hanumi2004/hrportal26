<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Employment;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Display the unified calendar.
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $role_id = $user->role_id;

        $calendarEvents = collect();

        // 1. EVENTS; CAN SEE ONLY EVENT THEY ARE INVITED TO OR ALL IF ADMIN
        $eventColour = '#f472b6'; // pink

        $query = Event::query();

        if ($user->role_id !== 2 && $employee) {
            $query->whereHas('attendees', function ($q) use ($employee) {
                $q->where('employee_id', $employee->employee_id);
            });
        }

        $events = $query->get();

        foreach ($events as $event) {
            $calendarEvents->push([
                'title' => $event->event_name,
                'start' => Carbon::parse($event->event_date)->toDateString(),
                'color' => $eventColour,
                'url'   => route('event.show', $event->id),
                'type'  => 'event',
            ]);
        }

        // 2. BIRTHDAYS; CAN SEE EVERYONE (repeat yearly)
        $birthdayColour = '#60a5fa'; // blue

        $employees = Employee::whereNotNull('birthday')->get();

        foreach ($employees as $emp) {
            $birthdayThisYear = Carbon::parse($emp->birthday)
                ->year(now()->year)
                ->toDateString();

            $calendarEvents->push([
                'title' => '🎂 ' . $emp->full_name,
                'start' => $birthdayThisYear,
                'color' => $birthdayColour,
                'type'  => 'birthday',
            ]);
        }

        // 3. LEAVES; CAN SEE EVERYONE
        $leaveColour = '#34d399'; // green

        // Approved Leaves only
        $leaveQuery = Leave::with('employee')->where('leave_status', 'approved');

        $leaves = $leaveQuery->get();

        foreach ($leaves as $leave) {
            $calendarEvents->push([
                'title' => '🏖 ' . $leave->employee->full_name,
                'start' => Carbon::parse($leave->start_date)->toDateString(),
                // FullCalendar needs end date +1 for inclusive ranges
                'end'   => Carbon::parse($leave->end_date)->addDay()->toDateString(),
                'color' => $leaveColour,
                'type'  => 'leave',
                // 'url'   => route('leave.show', $leave->id), 
            ]);
        }

        // 4. Contract / Internship Ending
        $contractColour = '#fbbf24'; // amber
        $internColour   = '#fb7185'; // rose

        $employmentQuery = Employment::with(['employee', 'type', 'status'])
            ->whereHas('status', fn($q) => $q->where('name', 'active'))
            ->whereNotNull('contract_end');

        // Employee: only their own
        if ($user->role_id !== 2 && $employee) {
            $employmentQuery->where('employee_id', $employee->employee_id);
        }

        $employments = $employmentQuery->get();

        foreach ($employments as $emp) {

            $isIntern = strtolower($emp->type?->name ?? '') === 'intern';

            $calendarEvents->push([
                'title' => ($isIntern ? '🎓 Internship Ends: ' : '⏳ Contract Ends: ')
                    . $emp->employee->full_name,
                'start' => Carbon::parse($emp->contract_end)->toDateString(),
                'color' => $isIntern ? $internColour : $contractColour,
                'type'  => 'contract_end',
                // 'url'   => route('employment.show', $emp->id),
            ]);
        }

        // 5. PUBLIC HOLIDAYS (Malaysia)
        $holidayColour = '#a78bfa'; // purple

        $currentYear = now()->year;
        $icsUrl = "https://calendar.google.com/calendar/ical/en.malaysia%23holiday%40group.v.calendar.google.com/public/basic.ics";
        $icsData = Http::get($icsUrl)->body();

        // Simple ICS parser (no external library needed)
        $lines = explode("\n", $icsData);
        $events = [];
        $currentEvent = null;

        // It parses the ICS manually (basic regex-based parsing for SUMMARY and DTSTART)
        // This works for simple Google Calendar ICS feeds but may need refinement for complex ones
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'BEGIN:VEVENT') === 0) {
                $currentEvent = [];
            } elseif (strpos($line, 'END:VEVENT') === 0) {
                if ($currentEvent) {
                    $events[] = $currentEvent;
                }
                $currentEvent = null;
            } elseif ($currentEvent !== null) {
                if (strpos($line, 'SUMMARY:') === 0) {
                    $currentEvent['summary'] = substr($line, 8);
                } elseif (strpos($line, 'DTSTART;VALUE=DATE:') === 0) {
                    $currentEvent['start'] = substr($line, 20);
                }
            }
        }

        foreach ($events as $holiday) {
            if (isset($holiday['start']) && isset($holiday['summary'])) {
                $startDate = Carbon::createFromFormat('Ymd', $holiday['start']);
                if ($startDate->year == $currentYear) {
                    $calendarEvents->push([
                        'title' => '🏛 ' . $holiday['summary'],
                        'start' => $startDate->toDateString(),
                        'color' => $holidayColour,
                        'type' => 'holiday',  // Matches the checkbox data-type
                    ]);
                }
            }
        }

        return view('calendar', [
            'calendarEvents' => $calendarEvents, 'role_id' => $role_id
        ]);
    }
}
