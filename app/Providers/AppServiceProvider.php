<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        date_default_timezone_set(config('app.timezone'));  //added to set timezone globally
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $isPunchedIn = false;
			$isReadOnly = false;

            $user = Auth::user();
			
            if ($user && $user->employee) {
                $attendance = Attendance::where('employee_id', $user->employee->employee_id)
                    ->whereDate('date', Carbon::today())
                    ->orderBy('id', 'desc')
                    ->first();

                if ($attendance && $attendance->time_in && !$attendance->time_out) {
                    $isPunchedIn = true;
                }
				
				$employment = $user->employee->employment ?? null;
                $statusName = strtolower(optional($employment?->status)->name ?? '');

                $hasFinalDate =
                    ! is_null($employment?->termination_date) ||
                    ! is_null($employment?->resignation_date) ||
                    ! is_null($employment?->last_working_day) ||
                    ! is_null($employment?->suspension_end);

                $isReadOnly = in_array($statusName, ['terminated', 'resigned', 'suspended']) && $hasFinalDate;
            }
				
            

            $view->with([
                'isPunchedIn' => $isPunchedIn,
                'isReadOnly' => $isReadOnly,
            ]);
        });
    }
}
