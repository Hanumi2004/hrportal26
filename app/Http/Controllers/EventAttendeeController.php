<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EventAttendee;

class EventAttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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

    public function confirm(EventAttendee $myAttendance)
    {
        $myAttendance->update([
            'response_status' => 'confirmed',
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Attendance confirmed.');
    }

    public function decline(Request $request, EventAttendee $myAttendance)
    {
        $request->validate([
            'decline_reason' => 'required|string|max:1000',
        ]);

        $myAttendance->update([
            'response_status' => 'declined',
            'decline_reason' => $request->input('decline_reason'),
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Attendance declined.');
    }
}