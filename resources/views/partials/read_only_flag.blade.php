@php
    $employment = auth()->user()->employee->employment ?? null;
    $statusName = strtolower(optional($employment?->status)->name ?? '');
    $hasFinalDate =
        ! is_null($employment?->termination_date) ||
        ! is_null($employment?->resignation_date) ||
        ! is_null($employment?->last_working_day) ||
        ! is_null($employment?->suspension_end);

    $isReadOnly = in_array($statusName, ['terminated', 'resigned', 'suspended']) && $hasFinalDate;
@endphp