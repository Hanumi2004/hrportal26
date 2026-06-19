<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RequestApproverController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\EventAttendeeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FormApproverController;
use App\Http\Controllers\WorkHandoverController;

// Home page
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/holidays', function () {
    $icsUrl = "https://calendar.google.com/calendar/ical/en.malaysia%23holiday%40group.v.calendar.google.com/public/basic.ics";
    $icsData = Http::get($icsUrl)->body();
    return response($icsData)->header('Content-Type', 'text/calendar');
});

Route::get('/dashboard', function () {
    if (Auth::check()) {
        // Role 1,2,7 → admin dashboard | Role 3,4,5,6 → employee dashboard
	if (in_array(Auth::user()->role_id, [1, 2, 7])) {
		return redirect()->route('admin.dashboard');
	} else {
		return redirect()->route('employee.dashboard');
	}
    }
    return redirect()->route('login');
})->middleware('auth', 'force.password.reset')->name('dashboard');

// 2FA challenge routes (Fortify challenge override)
Route::get('/two-factor-challenge', [TwoFactorController::class, 'index'])->name('two-factor.login');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'store'])->name('two-factor.store');

// Notifications
Route::post('/notifications/read-all', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return response()->json(['status' => 'ok']);
})->middleware('auth')->name('notifications.readAll');

// Shared routes for both employees and admins (authenticated users)
Route::middleware(['auth'])->group(function () {

    // Events CRUD (shared)
    Route::post('/event', [EventController::class, 'store'])->name('event.store');
    Route::get('/event/create', [EventController::class, 'create'])->name('event.create');
    Route::get('/event/{id}', [EventController::class, 'show'])->name('event.show');
    Route::get('/event/{id}/edit', [EventController::class, 'edit'])->name('event.edit');
    Route::put('/event/{id}', [EventController::class, 'update'])->name('event.update');
    Route::delete('/event/{id}', [EventController::class, 'destroy'])->name('event.destroy');

    // Profile (employee only routes)
    Route::get('/employee/profile/edit/personal/{employee}', [EmployeeController::class, 'editPersonal'])->name('profile.editPersonal');
    Route::put('/employee/profile/update/personal/{employee}', [EmployeeController::class, 'updatePersonal'])->name('profile.updatePersonal');
    Route::get('/employee/profile/settings', [EmployeeController::class, 'settings'])->name('profile.settings.employee');

    // Profile (admin only routes) — tapi kita akan protect lagi bawah admin group
    Route::get('/admin/profile/edit/employment/{employee}', [EmployeeController::class, 'editEmployment'])->name('profile.editEmployment');
    Route::put('/admin/profile/update/employment/{employee}', [EmployeeController::class, 'updateEmployment'])->name('profile.updateEmployment');
    Route::get('/admin/profile/settings', [EmployeeController::class, 'settings'])->name('profile.settings.admin');

    // For both admin and employee
    Route::get('/profile/show/{employee?}', [EmployeeController::class, 'show'])->name('profile.show');
    Route::get('/profile/{id}/print', [EmployeeController::class, 'downloadProfile'])->name('profile.print');

    Route::put('/admin/employee/{employee}/photo', [EmployeeController::class, 'updateProfilePhoto'])->name('admin.employee.updatePhoto');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/form/work-handover/view/{form}', [FormController::class, 'show'])->name('form.show');
});

// Employee routes (auth + force password reset + employee.readonly)
Route::middleware(['auth', 'force.password.reset', 'employee.readonly'])->group(function () { // employee
    // employee dashboard
    Route::get('employee-dashboard', [EmployeeController::class, 'showDashboardForLoggedInUser'])->name('employee.dashboard');

    // Announcements (employee view)
    Route::get('/announcement', [AnnouncementController::class, 'index'])->name('announcement.index.employee');

    // Attendance (employee)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('employee.attendance');
    Route::post('/attendance/punch-in', [AttendanceController::class, 'punchIn'])->name('attendance.punchIn');
    Route::post('/attendance/punch-out', [AttendanceController::class, 'punchOut'])->name('attendance.punchOut');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/report', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::post('/attendance/time-slip', [AttendanceController::class, 'requestTimeSlip'])->name('attendance.time-slip');
    Route::delete('/employee/timeslip/{attendance}', [AttendanceController::class, 'destroyTimeSlip'])->name('timeslip.destroy');

    // Leave (employee)
    Route::get('/leave', [LeaveController::class, 'index'])->name('leave.index.employee');
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/apply', [LeaveController::class, 'create'])->name('leave.create');
    Route::get('/leave/report', [LeaveController::class, 'export'])->name('leave.export');
    Route::delete('/employee/leave/{leave}', [LeaveController::class, 'cancel'])->name('leave.cancel.employee');

    // Tasks (employee)
    Route::get('/tasks', [TaskController::class, 'index'])->name('task.index.employee');
    Route::post('/task', [TaskController::class, 'store'])->name('task.store');
    Route::get('/task/create', [TaskController::class, 'create'])->name('task.create');
    Route::get('/task/{task}/edit', [TaskController::class, 'edit'])->name('task.edit');
    Route::put('/task/{task}', [TaskController::class, 'update'])->name('task.update');
    Route::get('/task/{task}/detail', [TaskController::class, 'detail'])->name('task.detail');
    Route::post('/task/{task}/progress', [TaskController::class, 'updateProgress'])->name('task.progress');
    Route::post('/task/{task}/reopen/{employeeId}', [TaskController::class, 'reopenAssignment'])->name('task.reopen.assignment');

	// Assignment approvals (Manager / Exec Director only)
Route::get('tasks/approvals', [TaskController::class, 'assignmentApprovals'])
    ->name('task.assignment.approvals');
Route::post('tasks/approvals/{assignment}/approve', [TaskController::class, 'approveAssignment'])
    ->name('task.assignment.approve');
Route::post('tasks/approvals/{assignment}/reject', [TaskController::class, 'rejectAssignment'])
    ->name('task.assignment.reject');
	
	
    // Projects (employee)
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index.employee');
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::put('/project/{project}', [ProjectController::class, 'update'])->name('project.update');

    // Events (employee)
    Route::get('/event', [EventController::class, 'index'])->name('event.index.employee');
    Route::post('/event/{myAttendance}/attendance/confirm', [EventAttendeeController::class, 'confirm'])->name('event.attendance.confirm');
    Route::post('/event/{myAttendance}/attendance/decline', [EventAttendeeController::class, 'decline'])->name('event.attendance.decline');

    // Requests (employee)
    Route::get('/requests', [RequestController::class, 'requests'])->name('employee.requests');
    Route::get('/myrequests', [RequestController::class, 'myRequests'])->name('employee.myrequests');

    // Forms (employee)
    Route::get('/forms', [FormController::class, 'forms'])->name('form.employee');
    Route::get('/myforms', [FormController::class, 'myForms'])->name('form.myforms');

    // Work handover (employee)
    Route::post('/form/work-handover/store', [WorkHandoverController::class, 'store'])->name('form.work-handover.store');
    Route::get('/form/work-handover/create', [WorkHandoverController::class, 'create'])->name('form.work-handover.create');
});

// Admin routes (auth + admin.only) — employee TIDAK boleh access
Route::middleware(['auth', 'admin.only'])->group(function () { // admin
    // admin dashboard
    Route::get('admin-dashboard', [AdminController::class, 'showDashboardForLoggedInAdmin'])->name('admin.dashboard');

    // Announcements (admin manage)
    Route::get('/admin/announcement', [AnnouncementController::class, 'index'])->name('announcement.index.admin');
    Route::get('/announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::post('/announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::put('/announcement/{announcement}', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::delete('/announcement/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');

    // Employee management (admin)
    Route::get('/admin/employee', [AdminController::class, 'employee'])->name('admin.employee');
    Route::get('/admin/employee/create', [AdminController::class, 'createUser'])->name('admin.employee.create');
    Route::post('/admin/employee', [AdminController::class, 'storeUser'])->name('admin.employee.store');
    Route::get('/employees/all', [EmployeeController::class, 'getAllEmployees'])->name('employees.all');

    // Admin force reset password and disable 2FA
    Route::post('/admin/employee/{user}/reset-password', [AdminController::class, 'resetEmployeePassword'])
        ->name('admin.employee.resetPassword');

    Route::post('/admin/employee/{user}/disable-2fa', [AdminController::class, 'disableEmployee2FA'])
        ->name('admin.employee.disable2fa');

    Route::post('/admin/employee/{user}/force-password-reset', [AdminController::class, 'forcePasswordReset'])
        ->name('admin.employee.forcePasswordReset');

    // Attendance (admin view)
    Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance');

    // Leave (admin)
    Route::get('/admin/leave', [LeaveController::class, 'index'])->name('leave.index.admin');
    Route::delete('/admin/leave/{leave}', [LeaveController::class, 'destroy'])->name('leave.destroy.admin');

    // Tasks (admin)
    Route::get('/admin/tasks', [TaskController::class, 'index'])->name('task.index.admin');

    // Events (admin)
    Route::get('/admin/events', [EventController::class, 'index'])->name('event.index.admin');
    Route::get('/admin/event/{id}/attendees', [EventController::class, 'attendees'])->name('event.attendees');

    // Projects (admin)
    Route::get('/admin/projects', [ProjectController::class, 'index'])->name('project.index.admin');

    // Requests (admin)
    Route::get('/admin/requests', [RequestController::class, 'adminRequests'])->name('admin.requests');
    Route::post('/admin/timeslip/{attendance}/update-status', [RequestController::class, 'approveTimeSlips'])->name('timeslip.updateStatus');
    Route::post('/admin/leave/{leave}/update-status', [RequestController::class, 'approveLeaves'])->name('leave.updateStatus');
    Route::post('/admin/{employee}/requestapprovers', [RequestApproverController::class, 'store'])->name('request.approvers.store');

    // Forms (admin)
    Route::get('/admin/forms', [FormController::class, 'adminForms'])->name('form.admin');
    Route::post('/admin/{form}/update-status', [FormController::class, 'approveForms'])->name('form.updateStatus');
    Route::post('/admin/{employee}/formapprovers', [FormApproverController::class, 'store'])->name('form.approvers.store');

    // Settings (admin)
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/admin/settings/general', [SettingController::class, 'updateGeneral'])->name('admin.settings.general');
    Route::post('/admin/settings/leave-entitlements', [SettingController::class, 'updateLeaveEntitlements'])->name('admin.settings.leave');
    Route::post('/admin/settings/master-data', [SettingController::class, 'updateMasterData'])->name('admin.settings.master');

    // Admin profile settings routes (extra protection)
    Route::get('/admin/profile/edit/employment/{employee}', [EmployeeController::class, 'editEmployment'])->name('profile.editEmployment');
    Route::put('/admin/profile/update/employment/{employee}', [EmployeeController::class, 'updateEmployment'])->name('profile.updateEmployment');
    Route::get('/admin/profile/settings', [EmployeeController::class, 'settings'])->name('profile.settings.admin');
});