<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['user', 'lead', 'contact', 'deal']);

        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('lead_id')) $query->where('lead_id', $request->lead_id);

        $activities = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        return view('activities.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:call,email,whatsapp,meeting,note,task',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lead_id' => 'nullable|exists:leads,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'deal_id' => 'nullable|exists:deals,id',
            'activity_at' => 'nullable|date',
            'outcome' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['activity_at'] = $validated['activity_at'] ?? now();
        $validated['status'] = 'completed';

        $activity = Activity::create($validated);

        // Update last_contacted_at on lead
        if ($activity->lead_id) {
            $activity->lead->update(['last_contacted_at' => now()]);
        }

        return back()->with('success', 'Aktivitas berhasil dicatat!');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return back()->with('success', 'Aktivitas berhasil dihapus!');
    }
}
