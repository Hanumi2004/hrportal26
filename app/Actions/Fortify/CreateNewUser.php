<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Employee;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\AccCreationMail;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user. (admin only)
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            // 'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '', // admins don’t accept terms when creating employees.
            'employee_id' => ['required', 'string', 'max:20', 'unique:employees,employee_id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ])->validate();

        $tempPassword = Str::random(12);

        // Create the user record
        $user = User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($tempPassword),
            'role_id'  => $input['role_id'],
            'force_password_reset' => true,
        ]);

        // Create the associated employee record
        Employee::create([
            'employee_id' => $input['employee_id'],
            'full_name'   => $input['name'],
            'email'      => $input['email'],
            'user_id'     => $user->id,
        ]);

        // Send account creation email with temporary password
        Mail::to($user->email)->send(
            new AccCreationMail($user, $tempPassword)
        );

        return $user;
    }
}
