<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $meetings = Meeting::with(['lead', 'contact', 'creator'])
            ->orderBy('start_at')
            ->get();

        // Format for FullCalendar
        $events = $meetings->map(function($meeting) {
            return [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'start' => $meeting->start_at->toIso8601String(),
                'end' => $meeting->end_at->toIso8601String(),
                'color' => match($meeting->status) {
                    'completed' => '#10b981',
                    'cancelled' => '#ef4444',
                    default => '#6366f1',
                },
                'extendedProps' => [
                    'status' => $meeting->status,
                    'location' => $meeting->location,
                    'description' => $meeting->description,
                ]
            ];
        });

        $users = User::where('is_active', true)->get();
        $leads = Lead::orderBy('name')->get();
        $contacts = Contact::orderBy('name')->get();

        return view('meetings.index', compact('events', 'users', 'leads', 'contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'lead_id' => 'nullable|exists:leads,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'deal_id' => 'nullable|exists:deals,id',
            'attendees' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $meeting = Meeting::create($validated);

        return response()->json(['success' => true, 'meeting' => $meeting]);
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['lead', 'contact', 'deal', 'creator']);
        return response()->json($meeting);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|url',
            'status' => 'required|in:scheduled,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $meeting->update($validated);
        return response()->json(['success' => true]);
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return response()->json(['success' => true]);
    }
}
