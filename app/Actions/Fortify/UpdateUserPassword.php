<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
        	'password' => array_merge($this->passwordRules(), ['different:current_password']),
    	], [
            'current_password.current_password' => __('The provided password does not match your current password.'),
            'password.different' => __('The new password must be different from your current password.'),
        ])->validateWithBag('updatePassword');
 
        $user->forceFill([
        	'password' => Hash::make($input['password']),
            'force_password_reset' => false,
    	])->save();
	}
}
