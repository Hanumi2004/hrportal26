<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Department;
use App\Models\TaskAssignment;
use App\Models\TaskProgressLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user     = Auth::user();
        $employee = $user->employee;

        $projects  = Project::orderBy('project_name')->get();
        $employees = Employee::orderBy('full_name')->get();

        $baseQuery = Task::with([
            'project',
            'createdBy',
            'assignedTo',
            'assignedTo.employment.department',
            'progressLogs',
        ])->orderByDesc('created_at');

        // Super Admin (1) & Admin HR (2) nampak semua
        if (!in_array($user->role_id, [1, 2], true) && $employee) {
            $baseQuery->where(function ($q) use ($employee, $user) {
                // Assigned task
                $q->whereHas('assignedTo', function ($qq) use ($employee, $user) {
                    $qq->where('task_assignments.employee_id', $employee->employee_id);

                    // Staff biasa hanya nampak assignment approved
                    if (!in_array($user->role_id, [4, 5], true)) {
                        $qq->where('task_assignments.approval_status', 'approved');
                    }
                })
                // Task yang dia create sendiri
                ->orWhere('created_by', $employee->employee_id);

                // Manager / Exec Director nampak semua task department sendiri
                if (in_array($user->role_id, [4, 5], true)) {
                    $deptId = $employee->employment?->department_id;

                    if ($deptId) {
                        $q->orWhereHas('assignedTo.employment', function ($qqq) use ($deptId) {
                            $qqq->where('department_id', $deptId);
                        });
                    }
                }
            });
        }

        if ($request->boolean('created_by_me') && $employee) {
            $baseQuery->where('created_by', $employee->employee_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $baseQuery->where(function ($q) use ($search) {
                $q->where('task_name', 'like', '%' . $search . '%')
                  ->orWhere('id', $search);
            });
        }

        if ($request->filled('project_id')) {
            if ($request->project_id === 'independent') {
                $baseQuery->whereNull('project_id');
            } else {
                $baseQuery->where('project_id', $request->project_id);
            }
        }

        if (in_array($user->role_id, [1, 2], true)) {
            if ($request->filled('employee_id')) {
                $baseQuery->whereHas('assignedTo', function ($q) use ($request) {
                    $q->where('task_assignments.employee_id', $request->employee_id);
                });
            }

            if ($request->filled('department_id')) {
                $baseQuery->whereHas('assignedTo', function ($q) use ($request) {
                    $q->where('task_assignments.department_id', $request->department_id);
                });
            }

            if ($request->filled('created_by')) {
                $baseQuery->where('created_by', $request->created_by);
            }

            if ($request->filled('task_status')) {
                $baseQuery->where('task_status', $request->task_status);
            }

            if ($request->filled('due_date')) {
                $baseQuery->whereDate('due_date', $request->due_date);
            }
        }

        $tasks            = $baseQuery->get();
        $projectTasks     = $tasks->whereNotNull('project_id')->values();
        $independentTasks = $tasks->whereNull('project_id')->values();

        $totalTasks      = $tasks->count();
        $toDoTasks       = $tasks->where('task_status', 'to-do')->count();
        $inProgressTasks = $tasks->where('task_status', 'in-progress')->count();
        $inReviewTasks   = $tasks->where('task_status', 'in-review')->count();
        $toReviewTasks   = $tasks->where('task_status', 'to-review')->count();
        $completedTasks  = $tasks->where('task_status', 'completed')->count();

        $view = in_array($user->role_id, [1, 2], true)
            ? 'admin.admin-task'
            : 'employee.employee-task';

        $myTotalTasks      = 0;
        $myCompletedTasks  = 0;
        $myInProgressTasks = 0;
        $myProgress        = 0;
        $myTaskStatuses    = [];

        if ($employee && $user->role_id >= 3) {
            $myAssignments = TaskAssignment::where('employee_id', $employee->employee_id)
                ->where('approval_status', 'approved')
                ->get();

            foreach ($myAssignments as $assignment) {
                $myTaskStatuses[$assignment->task_id] = [
                    'status'     => $assignment->employee_status ?? 'pending',
                    'progress'   => match ($assignment->employee_status) {
                        'completed'   => 100,
                        'in-progress' => 50,
                        default       => 0,
                    },
                    'remarks'    => $assignment->employee_remarks,
                    'updated_at' => $assignment->progress_updated_at,
                ];
            }

            $myTotalTasks      = $myAssignments->count();
            $myCompletedTasks  = $myAssignments->where('employee_status', 'completed')->count();
            $myInProgressTasks = $myAssignments->where('employee_status', 'in-progress')->count();
            $myProgress        = $myTotalTasks > 0
                ? round(($myCompletedTasks / $myTotalTasks) * 100)
                : 0;
        }

        return view($view, compact(
            'tasks',
            'projectTasks',
            'independentTasks',
            'projects',
            'employees',
            'totalTasks',
            'toDoTasks',
            'inProgressTasks',
            'inReviewTasks',
            'toReviewTasks',
            'completedTasks',
            'myTotalTasks',
            'myCompletedTasks',
            'myInProgressTasks',
            'myProgress',
            'myTaskStatuses',
            'user'
        ));
    }

    public function create()
    {
        $user     = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile found.');
        }

        $currentHierarchyLevel = $user->role?->hierarchy_level;
        $departmentId          = $employee->employment?->department_id;

        if (!$currentHierarchyLevel) {
            return back()->with('error', 'Your role hierarchy is not configured.');
        }

        $projects = Project::orderBy('project_name')->get();

        $assignableEmployees = Employee::with(['employment.department', 'user.role'])
            ->where('employee_id', '!=', $employee->employee_id)
            ->whereHas('user.role', function ($q) use ($currentHierarchyLevel) {
                $q->where('hierarchy_level', '>=', $currentHierarchyLevel);
            })
            ->whereHas('employment', function ($q) {
                $q->whereHas('status', function ($qs) {
                    $qs->where('name', 'active');
                });
            })
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->whereHas('employment', function ($qq) use ($departmentId) {
                    $qq->where('department_id', $departmentId);
                });
            })
            ->orderBy('full_name')
            ->get();

        return view('employee.createtask', compact(
            'projects',
            'assignableEmployees',
            'currentHierarchyLevel'
        ));
    }

    public function store(Request $request)
    {
        $user     = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile found.');
        }

        $currentHierarchyLevel = $user->role?->hierarchy_level;
        $departmentId          = $employee->employment?->department_id;

        if (!$currentHierarchyLevel) {
            return back()->with('error', 'Your role hierarchy is not configured.');
        }

        $rules = [
            'project_id'     => 'nullable|string',
            'task_name'      => 'required|string|max:255',
            'task_desc'      => 'required|string',
            'task_status'    => 'required|in:to-do,in-progress,in-review,to-review,completed',
            'due_date'       => 'required|date',
            'notes'          => 'required|string',
            'employee_ids'   => 'required|array|min:1',
            'employee_ids.*' => 'required|exists:employees,employee_id',
        ];

        if ($request->project_id === '__create__') {
            $rules['new_project_name']       = 'required|string|max:255';
            $rules['new_project_desc']       = 'nullable|string';
            $rules['new_project_start_date'] = 'nullable|date';
            $rules['new_project_end_date']   = 'nullable|date';
            $rules['new_project_status']     = 'required|in:not-started,in-progress,on-hold,completed';
        } else {
            $rules['project_id'] = 'nullable|exists:projects,id';
        }

        $request->validate($rules, [
            'task_name.required'    => 'Task name is required.',
            'task_desc.required'    => 'Task description is required.',
            'task_status.required'  => 'Task status is required.',
            'due_date.required'     => 'Due date is required.',
            'notes.required'        => 'Notes field is required.',
            'employee_ids.required' => 'Please assign at least one employee.',
            'employee_ids.min'      => 'Please assign at least one employee.',
        ]);

        $duplicateExists = Task::where('task_name', $request->task_name)
            ->whereDate('due_date', $request->due_date)
            ->where('created_by', $employee->employee_id)
            ->exists();

        if ($duplicateExists) {
            return back()
                ->withErrors([
                    'task_name' => 'A task with the same name and due date created by you already exists.'
                ])
                ->withInput();
        }

        $submittedEmployeeIds = collect($request->employee_ids)
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values();

        if ($submittedEmployeeIds->isEmpty()) {
            return back()
                ->withErrors(['employee_ids' => 'Please assign at least one valid employee.'])
                ->withInput();
        }

        $allowedEmployeeIds = Employee::with(['employment', 'user.role'])
            ->whereIn('employee_id', $submittedEmployeeIds->all())
            ->where('employee_id', '!=', $employee->employee_id)
            ->whereHas('user.role', function ($q) use ($currentHierarchyLevel) {
                $q->where('hierarchy_level', '>=', $currentHierarchyLevel);
            })
            ->when($departmentId, function ($q) use ($departmentId, $user) {
                if (!in_array($user->role_id, [1, 2], true)) {
                    $q->whereHas('employment', function ($qq) use ($departmentId) {
                        $qq->where('department_id', $departmentId);
                    });
                }
            })
            ->pluck('employee_id')
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();

        if (count($allowedEmployeeIds) !== $submittedEmployeeIds->count()) {
            return back()
                ->withErrors([
                    'employee_ids' => 'You may only assign valid employees within your allowed hierarchy and department.'
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $employee, $user, $allowedEmployeeIds) {
            $projectId = null;

            if ($request->project_id === '__create__') {
                $project = Project::create([
                    'project_name'   => $request->new_project_name,
                    'project_desc'   => $request->new_project_desc,
                    'created_by'     => $employee->employee_id,
                    'start_date'     => $request->new_project_start_date,
                    'end_date'       => $request->new_project_end_date,
                    'project_status' => $request->new_project_status,
                ]);
                $projectId = $project->id;
            } elseif ($request->filled('project_id')) {
                $projectId = $request->project_id;
            }

            $task = Task::create([
                'project_id'  => $projectId,
                'task_name'   => $request->task_name,
                'task_desc'   => $request->task_desc,
                'task_status' => $request->task_status,
                'due_date'    => $request->due_date,
                'notes'       => $request->notes,
                'created_by'  => $employee->employee_id,
            ]);

            $assignees = Employee::with('employment')
                ->whereIn('employee_id', $allowedEmployeeIds)
                ->get()
                ->keyBy('employee_id');

            foreach ($allowedEmployeeIds as $empId) {
                $assignee = $assignees->get($empId);

                $approvalStatus = in_array($user->role_id, [4, 5], true)
                    ? 'approved'
                    : 'pending';

                TaskAssignment::create([
                    'task_id'         => $task->id,
                    'department_id'   => $assignee?->employment?->department_id,
                    'employee_id'     => $empId,
                    'assigned_by'     => $employee->employee_id,
                    'employee_status' => 'pending',
                    'approval_status' => $approvalStatus,
                ]);
            }
        });

        return redirect()->route('task.index.employee')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $user     = Auth::user();
        $employee = $user->employee;
        $role_id  = $user->role_id;

        if (!($task->created_by === ($employee->employee_id ?? null) || in_array($role_id, [1, 2]))) {
            abort(403, 'Unauthorized.');
        }

        $task->load(['project', 'createdBy', 'assignedTo.employment.department']);

        $projects    = Project::orderBy('project_name')->get();
        $departments = Department::orderBy('name')->get();

        $allEmployees = Employee::with('employment.department')
            ->get()
            ->map(fn ($e) => [
                'id'         => $e->employee_id,
                'name'       => $e->full_name,
                'department' => $e->employment->department->name ?? 'N/A',
            ]);

        $assignedEmployees = $task->assignedTo
            ->map(fn ($e) => [
                'employee_id'         => $e->employee_id,
                'full_name'           => $e->full_name,
                'department'          => $e->employment->department->name ?? '-',
                'status'              => $e->pivot->employee_status ?? 'pending',
                'remarks'             => $e->pivot->employee_remarks,
                'progress_updated_at' => $e->pivot->progress_updated_at,
            ]);

        $assignedDepartments = $task->assignedTo
            ->pluck('pivot.department_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return view('edit-task', compact(
            'task', 'role_id', 'employee',
            'projects', 'departments', 'allEmployees',
            'assignedEmployees', 'assignedDepartments'
        ));
    }

    public function update(Request $request, Task $task)
    {
        $user     = Auth::user();
        $employee = $user->employee;

        $isCreator = $task->created_by === ($employee->employee_id ?? null);
        $isAdmin   = in_array($user->role_id, [1, 2], true);

        if (!$isCreator && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'task_name'         => 'required|string|max:255',
            'project_id'        => 'nullable|exists:projects,id',
            'task_status'       => 'required|in:to-do,in-progress,in-review,to-review,completed',
            'due_date'          => 'nullable|date',
            'task_desc'         => 'nullable|string',
            'notes'             => 'nullable|string',
            'employee_ids'      => 'nullable|array',
            'employee_ids.*'    => 'exists:employees,employee_id',
            'new_due_date'      => 'nullable|date|after:due_date',
            'creator_remarks'   => 'nullable|string|max:500',
            'task_attachments'  => 'nullable|array',
            'task_attachments.*' => 'nullable|file|max:10240',
        ]);

        DB::transaction(function () use ($validated, $request, $task, $employee, $user) {
            $updateData = [
                'task_name'   => $validated['task_name'],
                'project_id'  => $validated['project_id'] ?? $task->project_id,
                'task_status' => $validated['task_status'],
                'due_date'    => $validated['due_date'] ?? $task->due_date,
                'task_desc'   => $validated['task_desc'] ?? $task->task_desc,
            ];

            if (!empty($validated['new_due_date'])) {
                $oldDueDate = $task->due_date ? Carbon::parse($task->due_date)->format('d M Y') : 'No due date';
                $updateData['due_date'] = $validated['new_due_date'];

                TaskProgressLog::create([
                    'task_id'             => $task->id,
                    'employee_id'         => $employee->employee_id ?? $task->created_by,
                    'employee_status'     => $task->task_status,
                    'employee_remarks'    => 'Deadline extended from ' . $oldDueDate . ' to '
                        . Carbon::parse($validated['new_due_date'])->format('d M Y')
                        . (!empty($validated['creator_remarks']) ? ' - ' . $validated['creator_remarks'] : ''),
                    'progress_updated_at' => now(),
                ]);
            }

            if (!empty($validated['creator_remarks'])) {
                $currentNotes = $task->notes ?? '';
                $timestamp    = now()->format('d M Y, h:i A');
                $newRemark    = "[{$timestamp}] Creator Update: " . $validated['creator_remarks'];

                $updateData['notes'] = $currentNotes
                    ? $currentNotes . "\n\n" . $newRemark
                    : $newRemark;
            } else {
                $updateData['notes'] = $validated['notes'] ?? $task->notes;
            }

            $task->update($updateData);

            if (array_key_exists('employee_ids', $validated)) {
                $newEmployeeIds = collect($validated['employee_ids'] ?? [])
                    ->map(fn ($id) => (string) $id)
                    ->unique()
                    ->values();

                $currentAssignments = TaskAssignment::where('task_id', $task->id)->get();

                foreach ($currentAssignments as $assignment) {
                    if (!$newEmployeeIds->contains((string) $assignment->employee_id)) {
                        $assignment->delete();
                    }
                }

                $employees = Employee::with('employment')
                    ->whereIn('employee_id', $newEmployeeIds->all())
                    ->get()
                    ->keyBy('employee_id');

                foreach ($newEmployeeIds as $empId) {
                    $exists = TaskAssignment::where('task_id', $task->id)
                        ->where('employee_id', $empId)
                        ->exists();

                    if (!$exists) {
                        $approvalStatus = in_array($user->role_id, [4, 5], true)
                            ? 'approved'
                            : 'pending';

                        TaskAssignment::create([
                            'task_id'         => $task->id,
                            'employee_id'     => $empId,
                            'assigned_by'     => $employee->employee_id ?? $task->created_by,
                            'department_id'   => $employees->get($empId)?->employment?->department_id,
                            'employee_status' => 'pending',
                            'approval_status' => $approvalStatus,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    }
                }
            }

            if ($request->hasFile('task_attachments')) {
                $paths = [];
                foreach ($request->file('task_attachments') as $file) {
                    if ($file && $file->isValid()) {
                        $paths[] = $file->store('task_attachments', 'public');
                    }
                }
                if (!empty($paths)) {
                    $existing = $task->attachments ?? [];
                    $task->update(['attachments' => array_merge($existing, $paths)]);
                }
            }
        });

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function updateProgress(Request $request, Task $task)
    {
        $user     = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found.');
        }

        $request->validate([
            'employee_status'  => 'required|in:pending,in-progress,completed',
            'employee_remarks' => 'required|string|max:2000',
            'attachments'      => 'nullable|array',
            'attachments.*'    => 'nullable|file|max:10240',
        ], [
            'employee_status.required'  => 'Please select task status.',
            'employee_status.in'        => 'Invalid task status selected.',
            'employee_remarks.required' => 'Please enter remarks / notes before submitting.',
            'employee_remarks.max'      => 'Remarks may not be greater than 2000 characters.',
        ]);

        $assignment = TaskAssignment::with('task')
            ->where('task_id', $task->id)
            ->where('employee_id', $employee->employee_id)
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'You are not assigned to this task.');
        }

        if ($assignment->approval_status !== 'approved') {
            return redirect()->back()->with('error', 'This assignment is not approved yet.');
        }

        if ($assignment->employee_status === 'completed') {
            return redirect()->back()->with('error', 'This task has already been completed and is locked. Please contact admin if task needs to be reopened.');
        }

        $status        = $request->employee_status;
        $remarks       = trim($request->employee_remarks);
        $now           = now();
        $progressValue = $status === 'completed' ? 100 : 0;

        $filePaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $filePaths[] = $file->store('task_attachments', 'public');
                }
            }
        }

        $assignment->update([
            'employee_status'     => $status,
            'employee_remarks'    => $remarks,
            'progress'            => $progressValue,
            'progress_updated_at' => $now,
            'updated_at'          => $now,
        ]);

        TaskProgressLog::create([
            'task_id'             => $task->id,
            'employee_id'         => $employee->employee_id,
            'employee_status'     => $status,
            'progress'            => $progressValue,
            'employee_remarks'    => $remarks,
            'attachment_path'     => !empty($filePaths) ? $filePaths : null,
            'progress_updated_at' => $now,
        ]);

        $task->load(['assignments' => function ($q) {
            $q->where('approval_status', 'approved');
        }]);

        $allCompleted = $task->assignments->count() > 0
            && $task->assignments->every(fn ($item) => $item->employee_status === 'completed');

        $anyStarted = $task->assignments->contains(fn ($item) =>
            in_array($item->employee_status, ['in-progress', 'completed'], true)
        );

        if ($allCompleted) {
            $task->update(['task_status' => 'completed']);
        } elseif ($anyStarted) {
            $task->update(['task_status' => 'in-progress']);
        } else {
            $task->update(['task_status' => 'to-do']);
        }

        return redirect()->back()->with('success', 'Task progress updated successfully.');
    }

    public function detail(Task $task)
    {
        $user     = Auth::user();
        $employee = $user->employee;
        $role_id  = $user->role_id;

        $task->load([
            'assignedTo.employment.department',
            'project',
            'createdBy',
            'progressLogs.employee',
            'assignments' => function ($q) {
                $q->where('approval_status', 'approved');
            },
        ]);

        $myAssignment = $employee
            ? $task->assignments->where('employee_id', $employee->employee_id)->first()
            : null;

        $isCreator  = $task->created_by === ($employee->employee_id ?? null);
        $isLocked   = $myAssignment?->employee_status === 'completed';

        return view('task.task-detail', compact(
            'task',
            'employee',
            'role_id',
            'myAssignment',
            'isCreator',
            'isLocked'
        ));
    }

    public function reopenAssignment(Request $request, Task $task, $employeeId)
    {
        $user = Auth::user();

        if (!in_array($user->role_id, [1, 2], true)) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'employee_status'  => 'required|in:pending,in-progress',
            'employee_remarks' => 'required|string|max:2000',
        ], [
            'employee_status.required'  => 'Please select new status.',
            'employee_remarks.required' => 'Please provide reason for reopening this task.',
        ]);

        $assignment = TaskAssignment::where('task_id', $task->id)
            ->where('employee_id', $employeeId)
            ->first();

        if (!$assignment) {
            return redirect()->back()->with('error', 'Assignment not found.');
        }

        $assignment->update([
            'employee_status'     => $request->employee_status,
            'employee_remarks'    => $request->employee_remarks,
            'progress'            => $request->employee_status === 'completed' ? 100 : 0,
            'progress_updated_at' => now(),
        ]);

        TaskProgressLog::create([
            'task_id'             => $task->id,
            'employee_id'         => $employeeId,
            'employee_status'     => $request->employee_status,
            'employee_remarks'    => 'Admin reopened task: ' . $request->employee_remarks,
            'progress_updated_at' => now(),
        ]);

        if ($task->task_status === 'completed') {
            $task->update([
                'task_status' => 'in-progress',
            ]);
        }

        return redirect()->back()->with('success', 'Task assignment reopened successfully.');
    }

    public function assignmentApprovals()
    {
        $user     = Auth::user();
        $employee = $user->employee;

        if (!$employee || !in_array($user->role_id, [4, 5], true)) {
            abort(403, 'Unauthorized.');
        }

        $deptId = $employee->employment?->department_id;

        if (!$deptId) {
            return back()->with('error', 'Your department is not configured.');
        }

        $pendingAssignments = TaskAssignment::with([
                'task.project',
                'task.createdBy',
                'task.assignedTo.employment.department',
                'employee.user',
                'employee.employment.department',
                'assignedByEmployee'
            ])
            ->where('approval_status', 'pending')
            ->whereHas('employee.employment', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('manager.assignment-approvals', compact('pendingAssignments', 'user'));
    }

    public function approveAssignment(TaskAssignment $assignment)
    {
        $user     = Auth::user();
        $employee = $user->employee;

        if (!$employee || !in_array($user->role_id, [4, 5], true)) {
            abort(403, 'Unauthorized.');
        }

        $assignment->loadMissing('employee.employment', 'task');

        $deptId       = $employee->employment?->department_id;
        $assigneeDept = $assignment->employee->employment?->department_id;

        if (!$deptId || $assigneeDept !== $deptId) {
            abort(403, 'You can only approve assignments for your department.');
        }

        if ($assignment->approval_status !== 'pending') {
            return back()->with('error', 'This assignment has already been processed.');
        }

        $assignment->update([
            'approval_status'     => 'approved',
            'progress_updated_at' => now(),
        ]);

        TaskProgressLog::create([
            'task_id'             => $assignment->task_id,
            'employee_id'         => $employee->employee_id,
            'employee_status'     => $assignment->employee_status,
            'employee_remarks'    => 'Manager approved assignment for ' . $assignment->employee->full_name,
            'progress_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Assignment approved successfully.');
    }

    public function rejectAssignment(TaskAssignment $assignment)
    {
        $user     = Auth::user();
        $employee = $user->employee;

        if (!$employee || !in_array($user->role_id, [4, 5], true)) {
            abort(403, 'Unauthorized.');
        }

        $assignment->loadMissing('employee.employment', 'task');

        $deptId       = $employee->employment?->department_id;
        $assigneeDept = $assignment->employee->employment?->department_id;

        if (!$deptId || $assigneeDept !== $deptId) {
            abort(403, 'You can only reject assignments for your department.');
        }

        if ($assignment->approval_status !== 'pending') {
            return back()->with('error', 'This assignment has already been processed.');
        }

        $assignment->update([
            'approval_status'     => 'rejected',
            'progress_updated_at' => now(),
        ]);

        TaskProgressLog::create([
            'task_id'             => $assignment->task_id,
            'employee_id'         => $employee->employee_id,
            'employee_status'     => $assignment->employee_status,
            'employee_remarks'    => 'Manager rejected assignment for ' . $assignment->employee->full_name,
            'progress_updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Assignment rejected successfully.');
    }

    public function destroy(string $id)
    {
        //
    }
}