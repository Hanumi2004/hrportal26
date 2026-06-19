<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
     public function __invoke($request)
    {
        $user = User::with('employee.employment.status')
            ->where('email', $request->email)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return null;
        }

        return $user;
    }

}