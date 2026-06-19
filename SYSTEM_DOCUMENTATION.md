# HR Portal 26 — Complete System Documentation

---

## 🔰 1. INTRODUCTION

HR Portal 26 is a Laravel-based internal HR management system.  
Tech Stack: **Laravel 11**, **MySQL**, **Laravel Jetstream** (with Fortify), **Bootstrap 5**, **jQuery**, **Vite**.

---

## 🔐 2. AUTHENTICATION & ACCESS CONTROL

### 2.1 Login Flow

| Step | Detail |
|------|--------|
| 1 | User visits `/login` (Fortify default) |
| 2 | `AuthenticateUser` (`app/Actions/Fortify/AuthenticateUser.php`) checks email + password against `users` table |
| 3 | If matched → redirect to `/dashboard` |
| 4 | `/dashboard` checks `role_id`: **1,2,7** → `/admin-dashboard` | **3,4,5,6** → `/employee-dashboard` |
| 5 | If `force_password_reset = true`, user is redirected to profile settings to change password before accessing any other page |

### 2.2 Two-Factor Authentication (2FA)

- Fortify 2FA with `TwoFactorController` override at `/two-factor-challenge`
- Admin can disable 2FA for any user via `routes/web.php:179`

### 2.3 Middleware Layers

| Middleware | File | Role Protected | Behaviour |
|-----------|------|----------------|-----------|
| `auth` | built-in | All logged-out users | Redirect to login |
| `force.password.reset` | `app/Http/Middleware/ForcePasswordReset.php` | All users with `force_password_reset = true` | Redirect to profile settings page until password is changed |
| `employee.readonly` | `app/Http/Middleware/EmployeeReadOnly.php` | Employees with status Terminated/Resigned/Suspended + has final date | GET requests pass through; POST/PUT/DELETE blocked with error "Your account is view-only" |
| `admin.only` | `app/Http/Middleware/AdminOnly.php` | Only role_id **1** (Super Admin) & **2** (Admin) | All others get 403 |

### 2.4 Role System

**Table: `roles`**

| Column | Type | Notes |
|--------|------|-------|
| id | unsigned int | Primary |
| role_name | string | e.g. "Super Admin", "Admin", "Staff", "Manager", etc. |
| hierarchy_level | unsigned int | Added by migration `2026_04_27_123327` — used for assignment permissions |

**Role Definitions & Hierarchy:**

| id | role_name | hierarchy_level | Description |
|----|-----------|-----------------|-------------|
| 1 | Super Admin | (lowest) | Full system access |
| 2 | Admin | (lowest) | Full system access (except delete projects) |
| 3 | Staff | 3 | Can create tasks, needs manager approval for assignments |
| 4 | Manager | 4 | Can approve assignments, cross-dept |
| 5 | Exec Director | 5 | Can approve assignments, division-based |
| 6 | Others | 6 | Limited access |
| 7 | President | 7 | Executive Observer (read-only) |

**`hierarchy_level` logic:** Lower number = higher authority. A user can only assign tasks to employees with `hierarchy_level >=` their own.

---

## 🗄️ 3. DATABASE SCHEMA COMPLETE

### 3.1 `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | Auto-increment |
| name | string(255) | |
| email | string(255) | Unique |
| email_verified_at | timestamp | Nullable |
| password | string(255) | Bcrypt hashed |
| remember_token | string(100) | |
| current_team_id | bigint unsigned | Nullable (Jetstream) |
| profile_photo_path | string(2048) | Nullable |
| force_password_reset | boolean | Default true (new users must reset) |
| two_factor_secret | text | Nullable (Fortify) |
| two_factor_recovery_codes | text | Nullable (Fortify) |
| two_factor_confirmed_at | timestamp | Nullable (Fortify) |
| role_id | bigint unsigned FK→roles.id | Nullable, set null on delete |
| created_at | timestamp | |
| updated_at | timestamp | |

### 3.2 `roles`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| role_name | string(255) | |
| hierarchy_level | unsigned int | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 3.3 `employees`
| Column | Type | Notes |
|--------|------|-------|
| employee_id | string PK | Manually assigned (e.g. matric/staff ID) |
| user_id | bigint unsigned FK→users.id | Cascade on delete |
| full_name | string(255) | |
| email | string(255) | Unique |
| phone_number | string(255) | Nullable, unique |
| address | string(255) | Nullable |
| ic_number | string(255) | Nullable, unique (IC/MyKad) |
| marital_status | string(255) | Nullable |
| gender | string(255) | Nullable |
| birthday | date | Nullable |
| nationality | string(255) | Nullable |
| emergency_contact_name | string(255) | Nullable |
| emergency_contact_number | string(255) | Nullable |
| emergency_contact_relationship | string(255) | Nullable |
| highest_education_level | string(255) | Nullable |
| highest_education_institution | string(255) | Nullable |
| graduation_year | year | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 3.4 `employments`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| employee_id | string FK→employees.employee_id | Cascade on delete |
| department_id | bigint unsigned FK→departments.id | Null on delete |
| employment_type_id | bigint unsigned FK→employment_types.id | Null on delete |
| employment_status_id | bigint unsigned FK→employment_statuses.id | Null on delete |
| company_branch_id | bigint unsigned FK→company_branches.id | Null on delete |
| report_to | string FK→employees.employee_id | Null on delete |
| position | string(255) | Nullable |
| date_of_employment | date | Nullable |
| contract_start | date | Nullable |
| contract_end | date | Nullable |
| probation_start | date | Nullable |
| probation_end | date | Nullable |
| suspension_start | date | Nullable |
| suspension_end | date | Nullable |
| resignation_date | date | Nullable |
| last_working_day | date | Nullable |
| termination_date | date | Nullable |
| work_start_time | time | Nullable |
| work_end_time | time | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### 3.5 `departments`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| name | string(255) unique |
| created_at / updated_at | timestamp |

### 3.6 `company_branches`
Same structure as departments.

### 3.7 `employment_types`
Same structure as departments.

### 3.8 `employment_statuses`
Same structure as departments. Values: active, terminated, resigned, suspended, etc.

### 3.9 `projects`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| project_name | string(255) | |
| project_desc | text | Nullable |
| created_by | string FK→employees.employee_id | Null on delete |
| start_date | date | Nullable |
| end_date | date | Nullable |
| project_status | enum | 'not-started', 'in-progress', 'on-hold', 'completed' (default: not-started) |
| created_at / updated_at | timestamp | |

### 3.10 `tasks`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| project_id | bigint unsigned FK→projects.id | Null on delete, nullable (independent tasks) |
| created_by | bigint unsigned FK→users.id | Cascade on delete |
| task_name | string(255) | |
| task_desc | text | Nullable |
| task_status | enum | 'to-do', 'in-progress', 'in-review', 'to-review', 'completed' (default: to-do) |
| notes | text | Nullable |
| attachments | text | Nullable, JSON array of file paths |
| due_date | date | Nullable |
| created_at / updated_at | timestamp | |

### 3.11 `task_assignments`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| task_id | bigint unsigned FK→tasks.id | Cascade on delete |
| department_id | bigint unsigned FK→departments.id | Cascade on delete, nullable |
| employee_id | string FK→employees.employee_id | Cascade on delete, nullable |
| assigned_by | string FK→employees.employee_id | Nullable (who assigned) |
| employee_status | string(255) | Default 'pending' (values: pending, in-progress, completed) |
| approval_status | string(255) | Default 'approved' (values: pending, approved, rejected) |
| employee_remarks | text | Nullable |
| progress_updated_at | timestamp | Nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Unique constraint:** `(task_id, department_id, employee_id)`

### 3.12 `task_progress_logs`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| task_id | bigint unsigned FK→tasks.id | Cascade on delete |
| employee_id | string FK→employees.employee_id | Cascade on delete |
| employee_status | string(255) | Default 'pending' |
| employee_remarks | text | Nullable |
| attachment_path | text | Nullable, JSON array of file paths |
| progress_updated_at | timestamp | Nullable |
| created_at / updated_at | timestamp | |

### 3.13 `attendances`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| employee_id | string FK→employees.employee_id | Cascade on delete |
| date | date | |
| time_in | time | Nullable |
| time_in_lat | decimal(10,7) | GPS latitude |
| time_in_lng | decimal(10,7) | GPS longitude |
| location_in | string(255) | |
| time_out | time | Nullable |
| time_out_lat | decimal(10,7) | |
| time_out_lng | decimal(10,7) | |
| location_out | string(255) | |
| status_time_in | string(255) | Nullable ('Late', 'On Time') |
| status_time_out | string(255) | Nullable |
| late_reason | string(255) | Nullable |
| early_leave_reason | string(255) | Nullable |
| time_slip_start | time | Nullable |
| time_slip_end | time | Nullable |
| time_slip_reason | string(255) | Nullable |
| time_slip_status | enum | 'pending', 'approved', 'rejected', nullable |
| created_at / updated_at | timestamp | |

### 3.14 `leaves`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| employee_id | string FK→employees.employee_id | Cascade on delete |
| leave_entitlement_id | bigint unsigned FK→leave_entitlements.id | Null on delete |
| leave_length | enum | 'full_day', 'AM', 'PM' |
| leave_reason | text | Nullable |
| start_date | date | |
| end_date | date | |
| days | integer | |
| attachment | string(255) | Nullable, file path |
| approved_by | string FK→employees.employee_id | Null on delete |
| approval_level | unsigned tinyint | Default 0 |
| approved_at | timestamp | Nullable |
| leave_status | enum | 'pending', 'approved', 'rejected' (default: pending) |
| reject_reason | text | Nullable |
| created_at / updated_at | timestamp | |

### 3.15 `leave_entitlements`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| name | string(255) unique (e.g. "Annual Leave", "Sick Leave") |
| full_entitlement | decimal(5,2) (days per year) |
| created_at / updated_at | timestamp |

### 3.16 `events`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| created_by | bigint unsigned FK→users.id | Cascade on delete |
| event_name | string(255) | |
| description | text | |
| event_date | date | |
| event_time | time | |
| event_location | string(255) | |
| event_category_id | bigint unsigned FK→event_categories.id | Null on delete |
| image | string(255) | Nullable |
| event_status | enum | 'upcoming', 'ongoing', 'completed', 'cancelled' |
| tags | string(255) | Nullable |
| created_at / updated_at | timestamp | |

### 3.17 `event_attendees`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| event_id | bigint unsigned FK→events.id | Cascade on delete |
| department_id | bigint unsigned FK→departments.id | Cascade on delete, nullable |
| employee_id | string FK→employees.employee_id | Cascade on delete, nullable |
| response_status | enum | 'pending', 'confirmed', 'declined' (default: pending) |
| decline_reason | text | Nullable |
| responded_at | timestamp | Nullable |
| attendance_status | enum | 'attended', 'absent', 'excused', nullable |
| notes | text | Nullable |
| created_at / updated_at | timestamp | |

**Unique constraint:** `(event_id, department_id, employee_id)`

### 3.18 `event_categories`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| name | string(255) unique |
| created_at / updated_at | timestamp |

### 3.19 `event_departments`
Pivot: event_id FK→events.id + department_id FK→departments.id

### 3.20 `event_employees`
Pivot: event_id FK→events.id + employee_id FK→employees.employee_id

### 3.21 `announcements`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| title | string(255) |
| description | text, nullable |
| category | enum: 'general', 'policy', 'system', 'other' (default: general) |
| priority | enum: 'high', 'medium', 'low' (default: low) |
| expires_date | date, nullable |
| created_by | bigint unsigned FK→users.id (null on delete) |
| created_at / updated_at | timestamp |

### 3.22 `forms`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| form_type | string(255) | 'work_handover' etc. |
| employee_id | string FK→employees.employee_id | Cascade on delete |
| form_description | text | Nullable |
| approved_by | string FK→employees.employee_id | Null on delete |
| approval_level | unsigned tinyint | Default 0 |
| approval_at | timestamp | Nullable |
| form_status | enum | 'pending', 'approved', 'rejected' (default: pending) |
| reject_reason | text | Nullable |
| created_at / updated_at | timestamp | |

### 3.23 `work_handovers`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| form_id | bigint unsigned FK→forms.id | Cascade on delete |
| last_working_day | date | |
| handover_to | string FK→users.id | Cascade on delete, nullable |
| handover_reason | string(255) | |
| handover_notes | text | Nullable |
| tasks | json | Nullable |
| documents | json | Nullable |
| electronic_files | json | Nullable |
| passwords | json | Nullable |
| financial_commitments | json | Nullable |
| inventory | json | Nullable |
| created_at / updated_at | timestamp | |

### 3.24 `request_approvers`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| employee_id | string FK→employees.employee_id |
| approver_id | string FK→employees.employee_id |
| level | unsigned tinyint (approval order) |
| created_at / updated_at | timestamp |

### 3.25 `form_approvers`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| form_id | bigint unsigned FK→forms.id |
| employee_id | string FK→employees.employee_id |
| approver_id | string FK→employees.employee_id |
| level | unsigned tinyint |
| created_at / updated_at | timestamp |

### 3.26 `settings`
| Column | Type |
|--------|------|
| id | bigint unsigned PK |
| key | string(255) unique |
| value | json |
| created_at / updated_at | timestamp |

### 3.27 `notifications`
Standard Laravel notifications table (UUID primary key, morphs).

### 3.28 `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`, `personal_access_tokens`
Standard Laravel tables.

---

## 🧩 4. MODEL RELATIONSHIPS (Complete ER Map)

### 4.1 `User` → Relationships
```
User (users)
├── role() → belongsTo(Role::class, 'role_id')
├── employee() → hasOne(Employee::class, 'user_id', 'id')
├── isAdmin() → role_name in ['Super Admin', 'Admin']
├── isSystemAdmin() → role_id in [1, 2]
├── isCreatorOfTask(Task) → created_by == employee.employee_id
├── isCreatorOfProject(Project) → created_by == employee.employee_id
├── canAssignTo(Employee) → hierarchy & role-based logic
└── canManageProjectMeta(Project) → owner/role-based logic
```

### 4.2 `Employee` → Relationships
```
Employee (employees)
├── user() → belongsTo(User::class, 'user_id')
├── attendances() → hasMany(Attendance::class)
├── leaves() → hasMany(Leave::class)
├── employment() → hasOne(Employment::class)
├── approvers() → belongsToMany(Employee, 'request_approvers', ...) with level pivot
├── formApprovers() → belongsToMany(Employee, 'form_approvers', ...) with level pivot
├── taskAssignments() → hasMany(TaskAssignment::class)
├── tasks() → hasManyThrough(Task::class, TaskAssignment::class)
├── eventAttendees() → hasMany(EventAttendee::class)
├── events() → hasManyThrough(Event::class, EventAttendee::class)
└── department() → belongsTo(Department::class) [deprecated: use employment]
```

### 4.3 `Employment` → Relationships
```
Employment (employments)
├── employee() → belongsTo(Employee::class)
├── reportToEmployee() → belongsTo(Employee::class, 'report_to')
├── department() → belongsTo(Department::class)
├── branch() → belongsTo(CompanyBranch::class)
├── type() → belongsTo(EmploymentType::class)
└── status() → belongsTo(EmploymentStatus::class)
```

### 4.4 `Task` → Relationships
```
Task (tasks)
├── project() → belongsTo(Project::class)
├── createdBy() → belongsTo(Employee::class, 'created_by')
├── assignedTo() → belongsToMany(Employee, 'task_assignments') with pivots:
│   ├── department_id
│   ├── assigned_by
│   ├── employee_status
│   ├── employee_remarks
│   └── progress_updated_at
├── assignments() → hasMany(TaskAssignment::class)
├── progressLogs() → hasMany(TaskProgressLog::class) ordered by progress_updated_at desc
└── getCompletionPercentageAttribute() → computed from assignments
```

### 4.5 `TaskAssignment` → Relationships
```
TaskAssignment (task_assignments)
├── task() → belongsTo(Task::class)
├── department() → belongsTo(Department::class)
├── employee() → belongsTo(Employee::class)
├── assignedByEmployee() → belongsTo(Employee::class, 'assigned_by')
└── getKpiSubmissionStatusAttribute() → computed (no_due_date, completed, within_time, late_submission, not_submitted, pending)
```

### 4.6 `TaskProgressLog` → Relationships
```
TaskProgressLog (task_progress_logs)
├── task() → belongsTo(Task::class)
└── employee() → belongsTo(Employee::class)
```

### 4.7 `Project` → Relationships
```
Project (projects)
├── tasks() → hasMany(Task::class)
└── createdBy() → belongsTo(Employee::class, 'created_by')
```

### 4.8 `Leave` → Relationships
```
Leave (leaves)
├── employee() → belongsTo(Employee::class)
├── approvedBy() → belongsTo(User::class)
├── entitlement() → belongsTo(LeaveEntitlement::class)
└── approvers() → via employee->approvers()
```

### 4.9 `Attendance` → Relationships
```
Attendance (attendances)
└── employee() → belongsTo(Employee::class)
```

### 4.10 `Event` → Relationships
```
Event (events)
├── createdBy() → belongsTo(User::class, 'created_by')
├── attendees() → hasMany(EventAttendee::class)
├── employees() → belongsToMany(Employee, 'event_attendees')
└── category() → belongsTo(EventCategory::class)
```

### 4.11 Other Models
- `Form` → hasOne(WorkHandover), hasMany(FormApprover), belongsTo(Employee)
- `WorkHandover` → belongsTo(Form), belongsTo(Employee 'handover_to')
- `Role` → hasMany(User)
- `Department` → hasMany(Employment)
- `CompanyBranch` → hasMany(Employment)
- `EmploymentType` → hasMany(Employment)
- `EmploymentStatus` → hasMany(Employment)
- `EventCategory` → hasMany(Event)
- `Announcement` → belongsTo(User 'created_by')
- `Setting` → simple key-value store
- `LeaveEntitlement` → hasMany(Leave)

---

## 🛣️ 5. ROUTES & CONTROLLER MAP

### 5.1 Public Routes (no middleware)
| URI | Controller@Method | Name |
|-----|-------------------|------|
| `/` | → redirect to login | - |
| `/holidays` | → ICS feed from Google Calendar | - |
| `/login` | Fortify built-in | login |
| `/two-factor-challenge` GET/POST | TwoFactorController@index/store | two-factor.login/store |

### 5.2 Authenticated Routes `middleware: ['auth']`
| URI | Controller@Method | Name |
|-----|-------------------|------|
| `/dashboard` | → redirect based on role | dashboard |
| `/notifications/read-all` POST | closure | notifications.readAll |
| Events CRUD | EventController | event.* |
| `/employee/profile/edit/personal/{employee}` | EmployeeController@editPersonal | profile.editPersonal |
| `/employee/profile/update/personal/{employee}` | EmployeeController@updatePersonal | profile.updatePersonal |
| `/employee/profile/settings` | EmployeeController@settings | profile.settings.employee |
| `/admin/profile/edit/employment/{employee}` | EmployeeController@editEmployment | profile.editEmployment |
| `/admin/profile/update/employment/{employee}` | EmployeeController@updateEmployment | profile.updateEmployment |
| `/admin/profile/settings` | EmployeeController@settings | profile.settings.admin |
| `/profile/show/{employee?}` | EmployeeController@show | profile.show |
| `/profile/{id}/print` | EmployeeController@downloadProfile | profile.print |
| `/admin/employee/{employee}/photo` PUT | EmployeeController@updateProfilePhoto | admin.employee.updatePhoto |
| `/calendar` | CalendarController@index | calendar.index |
| `/form/work-handover/view/{form}` | FormController@show | form.show |

### 5.3 Employee Routes `middleware: ['auth', 'force.password.reset', 'employee.readonly']`
| URI | Controller@Method | Name |
|-----|-------------------|------|
| `/employee-dashboard` | EmployeeController@showDashboardForLoggedInUser | employee.dashboard |
| `/announcement` | AnnouncementController@index | announcement.index.employee |
| `/attendance` GET | AttendanceController@index | employee.attendance |
| `/attendance/punch-in` POST | AttendanceController@punchIn | attendance.punchIn |
| `/attendance/punch-out` POST | AttendanceController@punchOut | attendance.punchOut |
| `/attendance/{attendance}` PUT | AttendanceController@update | attendance.update |
| `/attendance/report` GET | AttendanceController@export | attendance.export |
| `/attendance/time-slip` POST | AttendanceController@requestTimeSlip | attendance.time-slip |
| `/employee/timeslip/{attendance}` DELETE | AttendanceController@destroyTimeSlip | timeslip.destroy |
| `/leave` GET | LeaveController@index | leave.index.employee |
| `/leave` POST | LeaveController@store | leave.store |
| `/leave/apply` GET | LeaveController@create | leave.create |
| `/leave/report` GET | LeaveController@export | leave.export |
| `/employee/leave/{leave}` DELETE | LeaveController@cancel | leave.cancel.employee |
| `/tasks` GET | TaskController@index | task.index.employee |
| `/task` POST | TaskController@store | task.store |
| `/task/create` GET | TaskController@create | task.create |
| `/task/{task}/edit` GET | TaskController@edit | task.edit |
| `/task/{task}` PUT | TaskController@update | task.update |
| `/task/{task}/detail` GET | TaskController@detail | task.detail |
| `/task/{task}/progress` POST | TaskController@updateProgress | task.progress |
| `/task/{task}/reopen/{employeeId}` POST | TaskController@reopenAssignment | task.reopen.assignment |
| `/tasks/approvals` GET | TaskController@assignmentApprovals | task.assignment.approvals |
| `/tasks/approvals/{assignment}/approve` POST | TaskController@approveAssignment | task.assignment.approve |
| `/tasks/approvals/{assignment}/reject` POST | TaskController@rejectAssignment | task.assignment.reject |
| `/projects` GET | ProjectController@index | project.index.employee |
| `/project` POST | ProjectController@store | project.store |
| `/project/create` GET | ProjectController@create | project.create |
| `/project/{project}` PUT | ProjectController@update | project.update |
| `/event` GET | EventController@index | event.index.employee |
| `/event/{myAttendance}/attendance/confirm` POST | EventAttendeeController@confirm | event.attendance.confirm |
| `/event/{myAttendance}/attendance/decline` POST | EventAttendeeController@decline | event.attendance.decline |
| `/requests` GET | RequestController@requests | employee.requests |
| `/myrequests` GET | RequestController@myRequests | employee.myrequests |
| `/forms` GET | FormController@forms | form.employee |
| `/myforms` GET | FormController@myForms | form.myforms |
| `/form/work-handover/store` POST | WorkHandoverController@store | form.work-handover.store |
| `/form/work-handover/create` GET | WorkHandoverController@create | form.work-handover.create |

### 5.4 Admin Routes `middleware: ['auth', 'admin.only']` (role 1,2 only)
| URI | Controller@Method | Name |
|-----|-------------------|------|
| `/admin-dashboard` | AdminController@showDashboardForLoggedInAdmin | admin.dashboard |
| `/admin/announcement` | AnnouncementController@index | announcement.index.admin |
| `/announcement/create` | AnnouncementController@create | announcement.create |
| `/announcement` POST | AnnouncementController@store | announcement.store |
| `/announcement/{announcement}` PUT | AnnouncementController@update | announcement.update |
| `/announcement/{announcement}` DELETE | AnnouncementController@destroy | announcement.destroy |
| `/admin/employee` GET | AdminController@employee | admin.employee |
| `/admin/employee/create` GET | AdminController@createUser | admin.employee.create |
| `/admin/employee` POST | AdminController@storeUser | admin.employee.store |
| `/employees/all` | EmployeeController@getAllEmployees | employees.all |
| `/admin/employee/{user}/reset-password` POST | AdminController@resetEmployeePassword | admin.employee.resetPassword |
| `/admin/employee/{user}/disable-2fa` POST | AdminController@disableEmployee2FA | admin.employee.disable2fa |
| `/admin/employee/{user}/force-password-reset` POST | AdminController@forcePasswordReset | admin.employee.forcePasswordReset |
| `/admin/attendance` GET | AttendanceController@index | admin.attendance |
| `/admin/leave` GET | LeaveController@index | leave.index.admin |
| `/admin/leave/{leave}` DELETE | LeaveController@destroy | leave.destroy.admin |
| `/admin/tasks` GET | TaskController@index | task.index.admin |
| `/admin/events` GET | EventController@index | event.index.admin |
| `/admin/event/{id}/attendees` GET | EventController@attendees | event.attendees |
| `/admin/projects` GET | ProjectController@index | project.index.admin |
| `/admin/requests` GET | RequestController@adminRequests | admin.requests |
| `/admin/timeslip/{attendance}/update-status` POST | RequestController@approveTimeSlips | timeslip.updateStatus |
| `/admin/leave/{leave}/update-status` POST | RequestController@approveLeaves | leave.updateStatus |
| `/admin/{employee}/requestapprovers` POST | RequestApproverController@store | request.approvers.store |
| `/admin/forms` GET | FormController@adminForms | form.admin |
| `/admin/{form}/update-status` POST | FormController@approveForms | form.updateStatus |
| `/admin/{employee}/formapprovers` POST | FormApproverController@store | form.approvers.store |
| `/admin/settings` GET | SettingController@index | settings.index |
| `/admin/settings/general` POST | SettingController@updateGeneral | admin.settings.general |
| `/admin/settings/leave-entitlements` POST | SettingController@updateLeaveEntitlements | admin.settings.leave |
| `/admin/settings/master-data` POST | SettingController@updateMasterData | admin.settings.master |

---

## 📁 6. VIEW STRUCTURE (Blade Files)

### 6.1 Layouts
- `layouts/master.blade.php` — Main layout (sidebar navbar, common JS/CSS)
- `layouts/app.blade.php` — Jetstream app layout
- `layouts/guest.blade.php` — Login/guest layout

### 6.2 Authentication Views
| File | Purpose |
|------|---------|
| `auth/login.blade.php` | Login page (email + password) |
| `auth/two-factor-challenge.blade.php` | 2FA code entry (authenticator app) |
| `auth/two-factor-code-challenge.blade.php` | 2FA backup code entry |
| `auth/forgot-password.blade.php` | Forgot password |
| `auth/reset-password.blade.php` | Reset password form |
| `auth/register.blade.php` | Registration (disabled in config) |
| `auth/verify-email.blade.php` | Email verification |
| `auth/confirm-password.blade.php` | Confirm password |

### 6.3 Employee Dashboard & Profile
| File | Purpose |
|------|---------|
| `employee/employee-dashboard.blade.php` | Employee dashboard (Quick Actions, Attendance, Announcements, Requests, Activities) |
| `profile/show.blade.php` | Profile view page |
| `profile/editprofile.blade.php` | Edit personal profile |
| `profile/settings.blade.php` | Account settings (password, 2FA, etc.) |
| `profile/editemployment.blade.php` | Edit employment (admin only) |

### 6.4 Admin Pages
| File | Purpose |
|------|---------|
| `admin/admin-dashboard.blade.php` | Admin dashboard (attendance overview, announcements, requests) |
| `admin/admin-employee.blade.php` | Employee list management |
| `admin/admin-createemployee.blade.php` | Create new employee |
| `admin/admin-attendance.blade.php` | Admin attendance view |
| `admin/admin-leave.blade.php` | Admin leave management |
| `admin/admin-request.blade.php` | Admin leave + time slip requests |
| `admin/admin-task.blade.php` | Admin task view (same view engine as employee, different filters) |
| `admin/admin-project.blade.php` | Admin project view |
| `admin/admin-event.blade.php` | Admin events management |
| `admin/admin-announcement.blade.php` | Admin announcements |
| `admin/createannouncement.blade.php` | Create announcement |
| `admin/admin-setting.blade.php` | System settings (general, leave entitlements, master data) |
| `admin/assignment-approvals.blade.php` | **OBSOLETE** — replaced by manager/assignment-approvals.blade.php |

### 6.5 Tasks & Projects
| File | Purpose |
|------|---------|
| `employee/employee-task.blade.php` | Main task page (task cards, #taskModal edit, #taskProgressModal) |
| `employee/createtask.blade.php` | Create task form (with inline project creation, multi-select employees) |
| `employee/createproject.blade.php` | Create project form (with fuzzy duplicate detection) |
| `employee/employee-project.blade.php` | Project listing page |
| `task/task-detail.blade.php` | Dedicated task detail page (progress history, files, assignments) |
| `edit-task.blade.php` | Full task edit page |
| `manager/assignment-approvals.blade.php` | Manager approval page for pending assignments |

### 6.6 Leave, Attendance, Events
| File | Purpose |
|------|---------|
| `employee/employee-attendance.blade.php` | Employee attendance (punch in/out, history, time slip) |
| `employee/employee-leave.blade.php` | Employee leave list |
| `employee/applyleave.blade.php` | Apply leave form |
| `employee/employee-event.blade.php` | Employee events view |
| `event/event-create.blade.php` | Create event |
| `event/event-edit.blade.php` | Edit event |
| `event/event-show.blade.php` | Event detail |

### 6.7 Forms & Work Handover
| File | Purpose |
|------|---------|
| `form/form-dashboard.blade.php` | Forms dashboard (employee + admin) |
| `form/work-handover-form.blade.php` | Work handover form |
| `form/work-handover-show.blade.php` | View work handover detail |

### 6.8 Other
| File | Purpose |
|------|---------|
| `calendar.blade.php` | Full calendar view |
| `navigation-menu.blade.php` | Jetstream navigation component |
| `partials/read_only_flag.blade.php` | Read-only banner for terminated/resigned/suspended users |
| `exports/leave_report.blade.php` | Leave export PDF template |
| `pdf/employee-profile.blade.php` | Employee profile PDF |

---

## 🔄 7. COMPLETE APPLICATION FLOWS

### 7.1 AUTHENTICATION FLOW
```
User → /login → POST email+password
  → AuthenticateUser (Fortify custom) → checks users table
  → If fails: return null (Fortify handles error)
  → If success & 2FA enabled: redirect to /two-factor-challenge
  → If success & force_password_reset=true: redirect to profile settings
  → Else: redirect to /dashboard

/dashboard → checks role_id:
  → 1,2,7 → admin-dashboard
  → 3,4,5,6 → employee-dashboard
```

### 7.2 READ-ONLY CHECK (every page load)
```
AppServiceProvider@boot → View::composer('*') → checks:
  - Is employee status 'terminated'/'resigned'/'suspended'?
  - AND has final date (termination_date, resignation_date, etc.)?
  - If YES → $isReadOnly = true → Quick Action links disabled
  - EmployeeReadOnly middleware blocks POST/PUT/DELETE
```

### 7.3 ATTENDANCE FLOW
```
Employee Dashboard → Punch In button
  → AttendanceController@punchIn
  → Creates attendance record with date, time_in, GPS location, status_time_in
  → Updates isPunchedIn = true for UI

Punch Out button
  → AttendanceController@punchOut
  → Updates same record with time_out, GPS, status_time_out

Time Slip Request (forgot to punch in/out)
  → AttendanceController@requestTimeSlip
  → Creates attendance with time_slip_start/end, reason, time_slip_status=pending
  → Admin approves/rejects via RequestController@approveTimeSlips

Admin Dashboard:
  → AdminController@showDashboard: gathers today's attendance stats
  → Shows present/absent/late counts
  → Filterable by status
```

### 7.4 LEAVE FLOW
```
Employee → Apply Leave
  → LeaveController@create → shows form with leave entitlements
  → POST → LeaveController@store
  → Validates: start_date, end_date, leave_entitlement_id, leave_length, reason
  → Creates leave record with leave_status = 'pending'
  → Approval chain: based on request_approvers table (multi-level)
  
Approval:
  → Admin sees pending leaves via RequestController@approveLeaves
  → Multiple approvers (levels) checked via approval_level field
  → Each approval increments approval_level
  → When last level approves → leave_status = 'approved'
```

### 7.5 TASK & ASSIGNMENT FLOW
```
=== CREATE TASK ===
User → /task/create
  TaskController@create():
    - Gets current user's employee profile, hierarchy_level, department_id
    - Loads projects
    - Loads assignableEmployees: same department + active status +
      hierarchy_level >= current user + not self
  → View: createtask.blade.php
  → POST → TaskController@store():
    - Validates all fields including employee_ids array
    - Checks for duplicate task name + due date
    - Verifies each selected employee is actually assignable
    - If project_id == '__create__': also creates new project
    - Creates Task record
    - Creates TaskAssignment for each selected employee
    - If Creator is Manager/Exec Director (role 4,5): approval_status = 'approved'
    - If Creator is Staff (role 3): approval_status = 'pending'

=== APPROVAL FLOW ===
Manager/Exec Director → /tasks/approvals
  TaskController@assignmentApprovals():
    - Only for roles 4,5
    - Shows pending assignments where assignee's department = manager's department
  
  Approve button → TaskController@approveAssignment():
    - Sets approval_status = 'approved'
    - Creates TaskProgressLog entry
    
  Reject button → TaskController@rejectAssignment():
    - Sets approval_status = 'rejected'
    - Creates TaskProgressLog entry

=== VIEW TASKS ===
TaskController@index():
  - Admin (1,2): sees ALL tasks (can filter by employee, department, status, etc.)
  - Manager/Exec Director (4,5): sees assigned tasks + tasks in their department
  - Staff (3): sees tasks they created + their approved assignments only
  - View: employee-task.blade.php or admin.admin-task.blade.php
  - myTaskStatuses computed: shows Update Progress button only for tasks
    where current user is an assignee with approved assignment

=== UPDATE PROGRESS ===
Staff B → Update Progress button → #taskProgressModal
  POST → TaskController@updateProgress():
    - Validates employee_status, remarks, attachments (multiple files)
    - Checks assignment is approved and not completed/locked
    - Updates TaskAssignment (employee_status, remarks, progress_updated_at)
    - Creates TaskProgressLog (with attachment_path as JSON array)
    - Auto-updates task_status:
      - All assignments completed → task_status = 'completed'
      - Any started → task_status = 'in-progress'
      - None started → task_status = 'to-do'

=== EDIT TASK ===
Creator/Admin → Edit button → #taskModal
  Loads with current task data, assigned employees, attachments
  TaskController@update():
    - Only Creator or Admin (1,2) can edit
    - Can change: task_name, project, status, due_date, desc, notes
    - Can extend deadline (new_due_date) → creates progress log
    - Can add creator_remarks → appended to notes
    - Can manage assignees: add/remove employees
    - New assignee approval_status logic same as create
    - Can upload multiple task_attachments → merged with existing attachments

=== TASK DETAIL PAGE ===
/task/{task}/detail → TaskController@detail()
  Dedicated page showing:
  - Full task info
  - Assignment details with status per employee
  - Progress history logs with file attachments
  - Task-level attachments
  - Conditional: Edit/Update Progress buttons

=== REOPEN ASSIGNMENT ===
Admin → Task detail page → Reopen (for completed tasks)
  POST → TaskController@reopenAssignment():
    - Sets employee_status back to pending/in-progress
    - Creates progress log with reason
    - If task was 'completed', sets to 'in-progress'
```

### 7.6 PROJECT FLOW
```
=== CREATE PROJECT ===
User → /project/create → ProjectController@create()
  - Restricted: Others(6) and President(7) cannot create
  - Shows createproject.blade.php form
  
POST → ProjectController@store():
  - Validates project_name, desc, dates, status
  - Fuzzy duplicate name check (ignores spaces, underscores, hyphens, case)
  - If duplicate found → returns with 'duplicate_project' flash data
  - User sees modal: "Project X already exists. Update or create new?"
  - If confirm_update + duplicate_id → updates existing project
  - Else → creates new project

=== INLINE PROJECT CREATION IN TASK FORM ===
In createtask.blade.php:
  - Project dropdown has option '+ Create New Project' (value='__create__')
  - When selected → reveals inline fields: new_project_name, desc, start/end date, status
  - TaskController@store handles __create__ project_id

=== VIEW PROJECTS ===
ProjectController@index():
  - Admin (1,2,7): sees ALL projects
  - Others: sees projects they created + projects their tasks belong to
  - Filters: search, created_by, dates, status

=== UPDATE PROJECT ===
ProjectController@update():
  - Only owner, admin, or same-department Manager/Exec Director can edit
  - Updates: project_status, start_date, end_date
```

### 7.7 EVENT FLOW
```
=== CREATE EVENT ===
User → /event/create → EventController@create()
  - Form: event_name, description, date, time, location, category, image, attendees
  - Can invite by department or individual employees

=== EVENT ATTENDANCE ===
Employees see event → Confirm/Decline
  EventAttendeeController@confirm/decline:
    - Updates response_status on event_attendees
    - Admin can mark attendance_status (attended/absent/excused)

=== ADMIN VIEW ===
EventController@index (admin):
  - Shows all events with RSVP stats
  - Can manage attendee list
```

### 7.8 ANNOUNCEMENT FLOW
```
Admin → /announcement/create → AnnouncementController@store()
  - Creates announcement with title, description, category, priority, expires_date

Employee → /announcement → views all announcements
  - Dashboard shows latest 5
  - Priority badges: High (red), Medium (yellow), Low (blue)
```

### 7.9 WORK HANDOVER FORM FLOW
```
Employee → /form/work-handover/create → WorkHandoverController@create()
  - Fills in: last working day, handover to, reason, notes
  - Multiple items: tasks, documents, electronic files, passwords, 
    financial commitments, inventory (all JSON arrays)
  → POST → WorkHandoverController@store():
    - Creates Form record (form_type = 'work_handover')
    - Creates WorkHandover record with detailed JSON data
    - Status = pending → requires admin approval

Admin → /admin/forms → FormController@approveForms()
  - Can approve/reject work handover submissions
```

### 7.10 SETTINGS FLOW
```
Admin → /admin/settings → SettingController@index()
  - General settings (stored in settings table as JSON)
  - Leave entitlements CRUD
  - Master data: departments, branches, employment types, statuses, event categories
```

---

## 🧠 8. KEY BUSINESS LOGIC

### 8.1 Task Assignment Permission (`User::canAssignTo`)
```
Super Admin (1) = can assign anyone
President (3) = cannot assign (read-only)
Others (7) = can only assign self
General rule: cannot assign to higher hierarchy (lower number)
Exec Director (5) = can assign within same division
Manager (4) = can assign cross-functionally
Staff (3) / Others (6) = can assign lower hierarchy only (higher number)
```

### 8.2 Project Management Permission (`User::canManageProjectMeta`)
```
Owner = always can edit
Super Admin, Admin = can edit any project
Exec Director, Manager = can edit projects in same department
Others = read-only
```

### 8.3 Read-Only Detection
```
Employment status in ['terminated', 'resigned', 'suspended']
AND has at least one final date field filled
→ User becomes read-only (cannot POST/PUT/DELETE)
```

### 8.4 Fuzzy Project Name Matching (`ProjectController::normalizeProjectName`)
```
- Removes spaces, underscores, hyphens
- Lowercases all characters
- Compares normalized names
```

### 8.5 Task Progress Auto-Status
```
All assignments completed → task_status = 'completed'
Any assignment in-progress or completed → task_status = 'in-progress'
All assignments pending → task_status stays = 'to-do'
```

### 8.6 KPI Computation (`TaskAssignment::getKpiSubmissionStatusAttribute`)
```
Based on: employee_status + due_date + progress_updated_at
Possible values: no_due_date, completed, within_time, late_submission, not_submitted, pending
```

---

## 📦 9. FILE STORAGE

| Storage Disk | Base Path | Usage |
|-------------|-----------|-------|
| `public` | `storage/app/public/` | Profile photos, task attachments, leave attachments |
| Symlink | `public/storage` → `storage/app/public` | Public access via `/storage/...` URL |

### Storage Subdirectories:
- `task_attachments/` — Task progress file uploads + task-level attachments

### Storage Symlink Fix History:
- Originally symlink pointed to production: `/var/www/vhosts/alhidayahgroup.com.my/httpdocs/storage/app/public`
- Was deleted and recreated locally as Windows junction

---

## ⚙️ 10. KEY CONFIGURATION

### 10.1 `composer.json` Key Packages
- `laravel/framework` ^11.0
- `laravel/jetstream` ^5.0
- `laravel/fortify` ^1.0
- `laravel/sanctum` ^4.0
- `barryvdh/laravel-dompdf` (PDF generation)
- `maatwebsite/laravel-excel` (Excel exports)

### 10.2 Auth Configuration
- Guard: `web`
- Fortify username: `email`
- Rate limiting: 5 attempts/minute for login and 2FA
- Features: password reset, email verification, profile update, password update, 2FA (with confirmation)

### 10.3 Timezone
Set in `AppServiceProvider@boot`: `date_default_timezone_set(config('app.timezone'))`

---

## 🧪 11. TESTING COMMANDS

```bash
# Storage link (create symlink)
php artisan storage:link

# Clear cache
php artisan optimize:clear

# Run migrations
php artisan migrate

# Development server
php artisan serve
```

---

## 📝 12. SUMMARY OF ALL CONTROLLERS

| Controller | File | Key Methods |
|-----------|------|-------------|
| `AdminController` | `AdminController.php` | dashboard, employee CRUD, resetPassword, disable2FA, forcePasswordReset |
| `EmployeeController` | `EmployeeController.php` | dashboard, profile CRUD, settings, show, downloadProfile, getAllEmployees |
| `AttendanceController` | `AttendanceController.php` | index, punchIn, punchOut, update, export, requestTimeSlip, destroyTimeSlip |
| `LeaveController` | `LeaveController.php` | index, create, store, cancel, destroy, export |
| `TaskController` | `TaskController.php` | index, create, store, edit, update, detail, updateProgress, reopenAssignment, assignmentApprovals, approveAssignment, rejectAssignment |
| `ProjectController` | `ProjectController.php` | index, create, store, update, destroy, complete |
| `EventController` | `EventController.php` | index, create, store, show, edit, update, destroy, attendees |
| `EventAttendeeController` | `EventAttendeeController.php` | confirm, decline |
| `AnnouncementController` | `AnnouncementController.php` | index, create, store, update, destroy |
| `RequestController` | `RequestController.php` | requests, myRequests, adminRequests, approveTimeSlips, approveLeaves |
| `RequestApproverController` | `RequestApproverController.php` | store |
| `FormController` | `FormController.php` | forms, myForms, adminForms, approveForms, show |
| `FormApproverController` | `FormApproverController.php` | store |
| `WorkHandoverController` | `WorkHandoverController.php` | create, store |
| `SettingController` | `SettingController.php` | index, updateGeneral, updateLeaveEntitlements, updateMasterData |
| `TwoFactorController` | `TwoFactorController.php` | index, store (custom 2FA challenge) |
| `CalendarController` | `CalendarController.php` | index (renders calendar.blade.php) |
| `KpiController` | `KpiController.php` | (empty/minimal) |

---

## 📐 13. MIGRATION EXECUTION ORDER

```
1. 0001_01_01_000000_create_users_table (users, password_reset_tokens, sessions)
2. 2025_08_28_071920 (2FA columns on users)
3. 2025_09_04_021513 (roles)
4. 2025_09_04_021539 (employees)
5. 2025_09_04_022322 (role_id on users)
6. 2025_09_04_040139 (attendances)
7. 2025_09_05_122733 (departments)
8. 2025_10_01_104127 (leave_entitlements)
9. 2025_10_02_035714 (leaves)
10. 2025_11_05_144903 (projects)
11. 2025_11_05_145000 (tasks)
12. 2025_11_07_163402 (announcements)
13. 2025_12_16_152232 (notifications)
14. 2025_12_23_230253 (request_approvers)
15. 2025_12_29_112932 (settings)
16. 2026_01_07_161903 (task_assignments)
17. 2026_01_27_150930 (forms)
18. 2026_01_27_151330 (work_handovers)
19. 2026_01_28_104136 (form_approvers)
20. 2026_02_01_231912 (event_categories)
21. 2026_02_01_232106 (company_branches)
22. 2026_02_01_232157 (employment_types)
23. 2026_02_01_232245 (employment_statuses)
24. 2026_02_02_151827 (employments)
25. 2026_02_03_021305 (events)
26. 2026_02_04_110926 (event_attendees)
27. 2026_02_05_105126 (event_departments)
28. 2026_02_06_105142 (event_employees)
29. 2026_04_14_000000 (progress tracking on task_assignments)
30. 2026_04_14_163000 (assigned_by on task_assignments)
31. 2026_04_20_122840 (task_progress_logs)
32. 2026_04_21_164132 (employee_progress on task_assignments)
33. 2026_04_21_171706 (drop employee_progress)
34. 2026_04_22_082452 (drop employee_progress again)
35. 2026_04_23_153857 (attachment_path on task_progress_logs)
36. 2026_04_27_123327 (hierarchy_level on roles)
37. 2026_05_13_144356 (approval_status on task_assignments)
38. 2026_05_20_add_attachments_to_tasks_table (attachments on tasks)
39. 2026_05_20_update_attachment_path_to_json (JSON conversion)
```

---

*End of Document — Generated from full codebase analysis on 2026-05-27*
