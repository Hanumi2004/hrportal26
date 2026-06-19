<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Employee;
use App\Models\EventAttendee;
use App\Models\Department;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $employees = Employee::orderBy('full_name')->get();

        $query = Event::with([
            'createdBy',
            'attendees.employee',
            'attendees.department',
            'category',
        ])->orderBy('created_at', 'desc');

        // Employee: only events assigned to them (via pivot)
        if (!in_array($user->role_id, [1, 2]) && $employee) {
            $query->whereHas('attendees', function ($q) use ($employee) {
                $q->where('employee_id', $employee->employee_id);
            });
        }

        if (in_array($user->role_id, [1, 2])) {
            if ($request->filled('employee_id')) {
                $query->whereHas('attendees', function ($q) use ($request) {
                    $q->where('employee_id', $request->employee_id);
                });
            }

            if ($request->filled('department_id')) {
                $query->whereHas('attendees', function ($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('event_name', 'like', '%' . $request->search . '%')
                    ->orWhere('tags', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('event_category_id')) {
            $query->where('event_category_id', $request->event_category_id);
        }

        if ($request->filled('event_status')) {
            $query->where('event_status', $request->event_status);
        }

        if ($request->filled('event_date')) {
            $query->whereDate('event_date', '=', $request->event_date);
        }

        // Finally fetch results
        $events = $query->get();

        $eventCategories = EventCategory::orderBy('name')->get();
        $eventStatuses = ['upcoming', 'ongoing', 'completed', 'cancelled']; // don’t change dynamically — they’re controlled logic states, not user input

        $view = in_array($user->role_id, [1, 2]) ? 'admin.admin-event' : 'employee.employee-event';

        return view($view, compact('events', 'employees', 'eventCategories', 'eventStatuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $employee = $user->employee;

        $role_id = $user->role_id;

        $employment   = $employee?->employment;
        $departmentId = $employment?->department_id;

        // Employees from same department
        $employees = Employee::with('employment.department')
            ->whereHas('employment', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                    ->whereHas('status', fn($qs) => $qs->where('name', 'active'));
            })
            ->get()
            ->map(fn($e) => [
                'id'         => $e->employee_id,
                'name'       => $e->full_name,
                'department' => $e->employment->department->department_name ?? 'N/A',
            ]);

        $departments = Department::orderBy('name')->get();

        $allEmployees = Employee::with('employment.department')->get()->map(fn($e) => [
            'id'         => $e->employee_id,
            'name'       => $e->full_name,
            'department' => $e->employment->department->name ?? 'N/A',
        ]);

        $eventCategoriesEnum = EventCategory::orderBy('name')->get();

        return view('event.event-create', compact('role_id', 'departments', 'allEmployees', 'employees', 'eventCategoriesEnum'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->role_id, [1, 2]) && !$user->employee) {
            abort(403, 'Employee profile not found. Please contact HR.');
        }

        $employee = $user->employee;

        // ✅ 1. Validate all important columns that are NOT nullable
        $request->validate([
            'event_name'        => 'required|string|max:100',
            'description'       => 'required|string|max:255',
            'event_date'        => 'required|date',
            'event_time'        => 'required|date_format:H:i',
            'event_location'    => 'required|string|max:255',
            'event_category_id' => ['required', Rule::exists('event_categories', 'id')],
            'image'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            'event_status'      => 'required|in:upcoming,ongoing,completed,cancelled',
            'tags'              => 'nullable|string|max:100',    // expecting an array from form
            'department_ids'  => 'array',
            'department_ids.*' => 'exists:departments,id',
            'employee_ids'    => 'array',
            'employee_ids.*'  => 'exists:employees,employee_id',

        ]);

        // ✅ 2. Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            // saves files to storage/app/public/events.
        }

        // ✅ 3. Create and save event
        $event = new Event();
        $event->created_by       = Auth::id();
        $event->event_name       = $request->event_name;
        $event->description      = $request->description;
        $event->event_date       = $request->event_date;
        $event->event_time       = $request->event_time;
        $event->event_location   = $request->event_location;
        $event->event_category_id = $request->event_category_id;
        $event->image            = $imagePath; // can be null
        $event->tags             = $request->tags;
        // event_status defaults to "upcoming" in the migration

        $event->save();

        if ($request->boolean('assign_all')) {
            $employees = Employee::all();

            foreach ($employees as $emp) {
                EventAttendee::create([
                    'event_id'    => $event->id,
                    'employee_id' => $emp->employee_id,
                    'response_status' => 'pending',
                ]);
            }
        } else {
            // Assign departments
            foreach ($request->department_ids ?? [] as $deptId) {
                $event->attendees()->create([
                    'department_id' => $deptId,
                ]);
            }

            // Assign employees
            $employeeIds = Employee::whereIn('employee_id', $request->employee_ids ?? [])
                ->pluck('employee_id');

            foreach ($employeeIds as $empId) {
                EventAttendee::firstOrCreate([
                    'event_id'    => $event->id,
                    'employee_id' => $empId,
                ]);
            }
        }
        
        $route = in_array($user->role_id, [1, 2])
            ? 'event.index.admin'
            : 'event.index.employee';

        return redirect()->route($route)->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $role_id = $user->role_id;

        $event = Event::findOrFail($id);

        $eventCategories = EventCategory::orderBy('name')->get();
        $eventStatuses = ['upcoming', 'ongoing', 'completed', 'cancelled']; // don’t change dynamically — they’re controlled logic states, not user input

        return view('event.event-show', compact('role_id', 'event', 'eventCategories', 'eventStatuses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $role_id = $user->role_id;

        // ✅ LOAD EVENT
        $event = Event::with('attendees')->findOrFail($id);

        $employment   = $employee?->employment;
        $departmentId = $employment?->department_id;

        // Employees from same department
        $employees = Employee::with('employment.department')
            ->whereHas('employment', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId)
                    ->whereHas('status', fn($qs) => $qs->where('name', 'active'));
            })
            ->get()
            ->map(fn($e) => [
                'id'         => $e->employee_id,
                'name'       => $e->full_name,
                'department' => $e->employment->department->name ?? 'N/A',
            ]);

        $departments = Department::orderBy('name')->get();

        $allEmployees = Employee::with('employment.department')->get()->map(fn($e) => [
            'id'         => $e->employee_id,
            'name'       => $e->full_name,
            'department' => $e->employment->department?->name ?? 'N/A',
        ]);

        $selectedDepartmentIds = $event->attendees()
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->toArray();

        $assignedEmployees = $event->attendees()
            ->whereNotNull('employee_id')
            ->with('employee.department')
            ->get()
            ->map(fn($a) => [
                'id' => $a->employee->employee_id,
                'name' => $a->employee->full_name,
                'department' => $a->employee->department?->name ?? 'N/A',
            ]);

        $eventCategoriesEnum = EventCategory::orderBy('name')->get();

        return view('event.event-edit', compact('event', 'role_id', 'departments', 'allEmployees', 'employees', 'eventCategoriesEnum', 'selectedDepartmentIds', 'assignedEmployees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if (!in_array($user->role_id, [1, 2]) && !$user->employee) {
            abort(403, 'Employee profile not found. Please contact HR.');
        }

        // ✅ 1. Validate all important columns that are NOT nullable
        $request->validate([
            'event_name'        => 'required|string|max:100',
            'description'       => 'required|string|max:255',
            'event_date'        => 'required|date',
            'event_time'        => 'required|date_format:H:i',
            'event_location'    => 'required|string|max:255',
            'event_category_id' => ['required', Rule::exists('event_categories', 'id')],
            'image'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            'event_status'      => 'required|in:upcoming,ongoing,completed,cancelled',
            'tags'              => 'nullable|string|max:100',    // expecting an array from form
            'department_ids'  => 'array',
            'department_ids.*' => 'exists:departments,id',
            'employee_ids'    => 'array',
            'employee_ids.*'  => 'exists:employees,employee_id',

        ]);

        // 3. Create and save event
        $event = Event::findOrFail($id);

        // If user clicked ❌ remove image
        if ($request->input('remove_image') == 1) {
            if ($event->image) {
                Storage::delete('public/' . $event->image);
            }
            $event->image = null;
        }

        // If user uploaded a new image
        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::delete('public/' . $event->image);
            }

            $event->image = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'event_name'     => $request->event_name,
            'description'    => $request->description,
            'event_date'     => $request->event_date,
            'event_time'     => $request->event_time,
            'event_location' => $request->event_location,
            'event_category_id' => $request->event_category_id,
            'event_status'   => $request->event_status,
            'tags'           => $request->tags,
        ]);

        // Reset attendees
        $event->attendees()->delete();

        if ($request->boolean('assign_all')) {
            $employees = Employee::all();

            foreach ($employees as $emp) {
                EventAttendee::create([
                    'event_id'    => $event->id,
                    'employee_id' => $emp->employee_id,
                    'response_status' => 'pending',
                ]);
            }
        } else {

            // Assign departments
            foreach ($request->department_ids ?? [] as $deptId) {
                $event->attendees()->create([
                    'department_id' => $deptId,
                ]);
            }

            // Assign employees
            $employeeIds = Employee::whereIn('employee_id', $request->employee_ids ?? [])
                ->pluck('employee_id');

            foreach ($employeeIds as $empId) {
                EventAttendee::firstOrCreate([
                    'event_id'    => $event->id,
                    'employee_id' => $empId,
                ]);
            }
        }

        $route = in_array($user->role_id, [1, 2])
            ? 'event.index.admin'
            : 'event.index.employee';

        return redirect()->route($route)->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $user = Auth::user();
        $employee = $user->employee;

        $event = Event::findOrFail($id);

        if ($event->created_by !== $user->id && !in_array($user->role_id, [1, 2])) {
            abort(403, 'You are not allowed to delete this event.');
        }

        $event->attendees()->delete();
        $event->delete();

        $route = in_array($user->role_id, [1, 2])
            ? 'event.index.admin'
            : 'event.index.employee';

        return redirect()->route($route)
            ->with('success', 'Event deleted successfully!');
    }
}
