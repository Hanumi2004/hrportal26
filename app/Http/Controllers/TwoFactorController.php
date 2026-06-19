<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //Display the 2FA code input form
    public function index()
    {
        //User logged out and directed to 
        return view('auth.two-factor-code-challenge');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validates 2FA code
        $request->validate([
            'two_factor_code' => ['required', 'digits:6'],
        ]);

        //Identify user from session (only accepts user stored in session)
        //User must already be partially verified
        $userId = $request->session()->get('login.id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Your session has expired. Please login again.']);
        }

        $user = User::find($userId);

        if ($user->two_factor_code !== $request->two_factor_code) {
            return back()->withErrors(['two_factor_code' => 'The code is incorrect.']);
        }

        if (Carbon::now()->greaterThan($user->two_factor_expires_at)) {
            return back()->withErrors(['two_factor_code' => 'The code has expired.']);
        }

        //If valid; 

        //Clear used 2FA code in database
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        //Logs user in
        Auth::login($user);

        //Cleans login.id after successful login
        $request->session()->forget('login.id');
        $request->session()->regenerate();

        // 7) Finally, send them where they belong
        if (in_array(Auth::user()->role_id, [1, 2, 7])) {
			return redirect()->route('admin.dashboard');
		} else {
			return redirect()->route('employee.dashboard');
		}

        // (any non-admin)
        return redirect()->route('employee.dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
