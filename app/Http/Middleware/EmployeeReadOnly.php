<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EmployeeReadOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // Admin / Super Admin tak pernah read-only
        if (in_array($user->role_id, [1, 2])) {
            return $next($request);
        }

        $employee   = $user->employee ?? null;
        $employment = $employee?->employment ?? null;

        if (! $employment) {
            return $next($request);
        }

        $statusName = strtolower(optional($employment->status)->name ?? '');

        $hasFinalDate =
            ! is_null($employment->termination_date) ||
            ! is_null($employment->resignation_date) ||
            ! is_null($employment->last_working_day) ||
            ! is_null($employment->suspension_end);

        $isReadOnlyStatus = in_array($statusName, ['terminated', 'resigned', 'suspended'])
            && $hasFinalDate;

        if (! $isReadOnlyStatus) {
            return $next($request);
        }

        if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->isMethod('OPTIONS')) {
            return $next($request);
        }

        return redirect()
            ->route('employee.dashboard')
            ->with('error', 'Your account is view-only. Please contact HR.');
    }
}