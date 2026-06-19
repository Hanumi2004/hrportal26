<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_photo_path',
    ];

    protected $hidden = [
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::updated(function ($user) {
            if ($user->employee) {
                $user->employee->update([
                    'full_name' => $user->name,
                    'email' => $user->email,
                ]);
            }
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function employee()
	{
    return $this->hasOne(Employee::class, 'user_id', 'id');
	}

    public function isAdmin(): bool
    {
        return in_array(optional($this->role)->role_name, ['Super Admin', 'Admin']);
    }

    public function isSystemAdmin(): bool
    {
        return in_array($this->role_id, [1, 2]);
    }
	
	public function isCreatorOfTask(Task $task): bool
	{
		return $task->created_by === ($this->employee->employee_id ?? null);
	}

	public function isCreatorOfProject(Project $project): bool
	{
		return $project->created_by === ($this->employee->employee_id ?? null);
	}

	/**
	 * Boleh assign seseorang ke task (guna untuk TaskAssignment & future Project team).
	 */
	
	public function canAssignTo(Employee $assignee): bool
{
    // Andaian: User ada relation `role` dan `employee`
    // Role ada column `hierarchy_level`
    $myRoleId      = $this->role_id;
    $myLevel       = $this->role?->hierarchy_level;
    $assigneeLevel = $assignee->user->role?->hierarchy_level;

    // 1. Super Admin (id = 1): boleh assign sesiapa sahaja
    if ($myRoleId === 1) {
        return true;
    }

    // 2. President (id = 3): Executive Observer, read-only dalam module task
    if ($myRoleId === 3) {
        return false;
    }

    // 3. Others (id = 7): Limited/Guest – hanya boleh assign/update diri sendiri
    if ($myRoleId === 7) {
        return $this->employee?->employee_id === $assignee->employee_id;
    }

    // 4. Kalau tak ada hierarchy level, fallback: jangan benarkan (fail safe)
    if ($myLevel === null || $assigneeLevel === null) {
        return false;
    }

    // 5. General rule: tak boleh assign orang yang LEBIH tinggi (nombor lebih kecil)
    if ($assigneeLevel < $myLevel) {
        return false;
    }

    // 6. Exec Director (id = 5 dalam table anda): Division Head
    //    Boleh assign Manager ke bawah dalam division sama
    if ($myRoleId === 5) { // Exec Director = 5
        $myDivision       = $this->employee?->employment?->division_id;
        $assigneeDivision = $assignee->employment?->division_id;

        if ($myDivision === null || $assigneeDivision === null) {
            return false;
        }

        return $myDivision === $assigneeDivision;
    }

    // 7. Manager (id = 4): Cross-Functional
    //    Boleh assign bawah hierarchy & cross-dept → general rule di atas sudah cukup
    if ($myRoleId === 4) {
        return true;
    }

    // 8. Staff/Exec (id = 3 dalam table asal, tapi di table anda Staff = 3, Manager = 4, Exec Dir = 5)
    //    Untuk simple: boleh assign bawah level (lebih besar) sahaja
    if ($myRoleId === 3 || $myRoleId === 6) { // Staff = 3, Staff/Exec = 6
        return $assigneeLevel >= $myLevel;
    }

    // Default: kalau role lain yang belum di-mapping, jangan benarkan
    return false;
}
	/**
	 * Boleh manage meta Project (edit status, tarikh, desc).
	 * Owner sentiasa boleh edit, selain itu ikut role.
	 */
	public function canManageProjectMeta(Project $project): bool
	{
		// Owner boleh edit
		if ($this->isCreatorOfProject($project)) {
			return true;
		}

		// Super Admin & Admin boleh manage semua projek
		if (in_array($this->role_id, [1, 2], true)) {
			return true;
		}

		// Exec Director & Manager boleh manage projek dalam division/department sendiri (optional)
		if (in_array($this->role_id, [4, 5], true)) {
			$myDept       = $this->employee?->employment?->department_id;
			$projectOwner = $project->createdBy;
			$ownerDept    = $projectOwner?->employment?->department_id;

			if ($myDept && $ownerDept && $myDept === $ownerDept) {
				return true;
			}
		}

		// President read-only, Staff & Others tak manage kecuali owner (dah cover atas)
		return false;
	}
	
}