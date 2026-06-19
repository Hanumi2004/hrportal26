// Dalam KpiController.php
public function index()
{
    $user = Auth::user();
    $employee = $user->employee;

    // ONLY President (role_id=7) can view all
    if ($user->role_id == 7) {
        $kpis = Kpi::with('employee')->get();
    } else {
        // Superadmin (1), Admin (2), Manager (4), etc. - hanya KPI sendiri
        $kpis = Kpi::where('employee_id', $employee->employee_id)->get();
    }

    return view('kpi.index', compact('kpis'));
}