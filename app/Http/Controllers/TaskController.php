<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['assignedUser', 'creator', 'lead', 'contact', 'deal']);

        if (!Auth::user()->isManager()) {
            $query->where('assigned_to', Auth::id());
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('title', 'like', "%{$s}%");
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);

        $filter = $request->get('filter', 'all');
        if ($filter === 'today') {
            $query->whereDate('due_date', today());
        } elseif ($filter === 'overdue') {
            $query->where('due_date', '<', now())->where('status', '!=', 'completed');
        } elseif ($filter === 'upcoming') {
            $query->where('due_date', '>=', now())->where('due_date', '<=', now()->addDays(7));
        }

        $tasks = $query->orderBy('due_date')->paginate(20)->withQueryString();
        $users = User::where('is_active', true)->get();

        return view('tasks.index', compact('tasks', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'lead_id' => 'nullable|exists:leads,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'deal_id' => 'nullable|exists:deals,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'tags' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $task = Task::create($validated);

        return back()->with('success', "Task '{$task->title}' berhasil dibuat!");
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $task->update($validated);
        return back()->with('success', "Task berhasil diperbarui!");
    }

    public function complete(Task $task)
    {
        $task->update(['status' => 'completed', 'completed_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Task berhasil dihapus!');
    }
}
