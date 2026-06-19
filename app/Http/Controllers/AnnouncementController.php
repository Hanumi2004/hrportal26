<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncement;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $query = Announcement::orderBy('created_at', 'desc');

        // Only apply filters if the inputs exist
        if ($user->role_id === 2) {
            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            if ($request->filled('created_by')) {
                $query->whereHas('createdBy', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->created_by . '%');
                });
            }

            if ($request->filled('expires_before')) {
                $query->whereDate('expires_date', '<=', $request->expires_before);
            }
        }

        // Non-admins only see active (non-expired) announcements
        else {
            $query->where(function ($q) {
                $q->whereNull('expires_date')
                    ->orWhere('expires_date', '>=', now());
            });
        }

        // Finally fetch results
        $announcements = $query->get();

        $view = $user->role_id == 2 ? 'admin.admin-announcement' : 'employee.employee-announcement';

        return view($view, compact(
            'announcements'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('announcement.announcement-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'category'      => 'required|in:general,policy,system,other',
            'priority'      => 'required|in:high,medium,low',
            'expires_date'  => 'nullable|date',
        ]);

        $announcement = Announcement::create([
            'title'         => $request->title,
            'description'   => $request->description,
            'category'      => $request->category,
            'priority'      => $request->priority,
            'expires_date'  => $request->expires_date,
            'created_by'    => Auth::id(),
        ]);

        // notify all users
        $users = User::all();
        
        foreach ($users as $user) {
            $user->notify(new NewAnnouncement($announcement));
        }

        return redirect()->route('announcement.index.admin')->with('success', 'Announcement posted!');
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'category'      => 'required|in:general,policy,system,other',
            'priority'      => 'required|in:high,medium,low',
            'expires_date'  => 'nullable|date',
        ]);

        $announcement->update($request->only([
            'title',
            'description',
            'category',
            'priority',
            'expires_date',
        ]));

        return redirect()->back()->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcement.index.admin')
            ->with('success', 'Announcement deleted successfully.');
    }
}
