<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user     = Auth::user();
        $employee = $user->employee; // may be null for admin with no employee profile

        // Preload supporting lists for the filters (blade)
        $projects  = Project::orderBy('project_name')->get();
        $employees = Employee::orderBy('full_name')->get();

        // Base query for projects
        $query = Project::orderBy('created_at', 'desc');

        if (in_array($user->role_id, [1, 2, 7], true)) {
        // Super Admin, Admin, President: global view (President read-only akan di-handle di UI & action)
        $query->with(['tasks', 'createdBy']);
    } else {
        // Employee/Manager/Exec Director/Staff/Others: projek yang berkaitan sahaja
        if ($employee) {
            $createdProjectIds = Project::where('created_by', $employee->employee_id)->pluck('id');

            $assignedProjectIds = Task::whereHas('assignedTo', function ($q) use ($employee) {
                    $q->where('task_assignments.employee_id', $employee->employee_id);
                })
                ->whereNotNull('project_id')
                ->pluck('project_id');

            $projectIds = $createdProjectIds->merge($assignedProjectIds)->unique();

            $query->whereIn('id', $projectIds)
                ->with(['tasks', 'createdBy']);
        } else {
            $query->with('createdBy');
        }
    }

    // “Created by me” filter untuk semua roles (kalau ada employee profile)
    if ($request->filled('created_by_me') && $employee) {
        $query->where('created_by', $employee->employee_id);
    }

    // Filter search, created_by, dates, status (kekalkan)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('project_name', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        });
    }

    if ($request->filled('created_by')) {
        $query->where('created_by', $request->created_by);
    }

    if ($request->filled('start_date')) {
        $query->whereDate('start_date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('end_date', '<=', $request->end_date);
    }

    if ($request->filled('project_status')) {
        $query->where('project_status', $request->project_status);
    }

    $projects = $query->get();

    $totalProjects      = $projects->count();
    $notStartedProjects = $projects->where('project_status', 'not-started')->count();
    $inProgressProjects = $projects->where('project_status', 'in-progress')->count();
    $onHoldProjects     = $projects->where('project_status', 'on-hold')->count();
    $completedProjects  = $projects->where('project_status', 'completed')->count();

    $view = in_array($user->role_id, [1, 2, 7], true)
        ? 'admin.admin-project'
        : 'employee.employee-project';

    return view($view, compact(
        'totalProjects',
        'notStartedProjects',
        'inProgressProjects',
        'onHoldProjects',
        'completedProjects',
        'projects',
        'employees'
    ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

    // President (7) & Others (6) tak boleh create
    if (in_array($user->role_id, [6, 7], true)) {
        return redirect()->back()->with('error', 'You are not allowed to create projects.');
    }

    return view('employee.createproject');
    }

    /**
     * Store a newly created resource in storage.
     */
    // Create new project
    public function store(Request $request)
    {
        $user     = Auth::user();
    $employee = $user->employee;

    if (!$employee) {
        return redirect()->back()->with('error', 'No employee profile found for this user.');
    }

    if (in_array($user->role_id, [6, 7], true)) {
        return redirect()->back()->with('error', 'You are not allowed to create projects.');
    }

    $request->validate([
        'project_name'   => 'required|string|max:255',
        'project_desc'   => 'nullable|string',
        'start_date'     => 'nullable|date',
        'end_date'       => 'nullable|date',
        'project_status' => 'required|in:not-started,in-progress,on-hold,completed',
    ]);

    // Fuzzy duplicate check (skip if force_create)
    if (!$request->boolean('force_create')) {
        $normalized = $this->normalizeProjectName($request->project_name);
        $existing   = Project::all()->first(fn($p) =>
            $this->normalizeProjectName($p->project_name) === $normalized
        );

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_project', [
                    'id'   => $existing->id,
                    'name' => $existing->project_name,
                ]);
        }
    }

    // Handle "Yes, update existing"
    if ($request->boolean('confirm_update') && $request->filled('duplicate_id')) {
        $project = Project::findOrFail($request->duplicate_id);
        $project->update([
            'project_name'   => $request->project_name,
            'project_desc'   => $request->project_desc,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'project_status' => $request->project_status,
        ]);
        return redirect()->route('project.index.employee')
            ->with('success', 'Project updated successfully!');
    }

    $project = new Project();
    $project->project_name   = $request->project_name;
    $project->project_desc   = $request->project_desc;
    $project->created_by     = $employee->employee_id;
    $project->start_date     = $request->start_date;
    $project->end_date       = $request->end_date;
    $project->project_status = $request->project_status;
    $project->save();

    return redirect()->route('project.index.employee')->with('success', 'Project created successfully!');
    }

    private function normalizeProjectName($name)
    {
        return preg_replace('/[\s_-]+/u', '', mb_strtolower(trim($name)));
    }

    // Mark project as completed
    public function complete($id)
    {
        $project = Project::findOrFail($id);
        $project->update(['status' => 'completed']);

        return response()->json(['message' => 'Project marked as completed']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = Auth::user();

    if (! $user->canManageProjectMeta($project)) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'project_status' => 'required|in:not-started,in-progress,on-hold,completed',
        'start_date'     => 'nullable|date',
        'end_date'       => 'nullable|date',
    ]);

    $project->project_status = $request->project_status;
    $project->start_date     = $request->start_date;
    $project->end_date       = $request->end_date;
    $project->save();

    return redirect()->back()->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      $user    = Auth::user();
    $project = Project::findOrFail($id);

    // Untuk permulaan: hanya Super Admin boleh delete project
    if ($user->role_id !== 1) {
        abort(403, 'Only Super Admin can delete projects.');
    }

    // Jika nak soft delete, pastikan Project model guna SoftDeletes & column deleted_at
    $project->delete();

    return redirect()->back()->with('success', 'Project deleted successfully.');
    }
}
