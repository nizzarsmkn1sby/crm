<?php

namespace App\Http\Controllers;

use App\Models\AutomationWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomationWorkflowController extends Controller
{
    public function index()
    {
        $workflows = AutomationWorkflow::with('creator')->orderByDesc('created_at')->get();
        return view('automation.index', compact('workflows'));
    }

    public function create()
    {
        return view('automation.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger' => 'required|string',
            'trigger_conditions' => 'nullable|array',
            'actions' => 'required|array',
            'is_active' => 'boolean',
        ]);
        $validated['created_by'] = Auth::id();
        $workflow = AutomationWorkflow::create($validated);
        return redirect()->route('automation.index')
            ->with('success', "Workflow '{$workflow->name}' berhasil dibuat!");
    }

    public function show(AutomationWorkflow $automation)
    {
        return view('automation.show', compact('automation'));
    }

    public function edit(AutomationWorkflow $automation)
    {
        return view('automation.edit', compact('automation'));
    }

    public function update(Request $request, AutomationWorkflow $automation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger' => 'required|string',
            'actions' => 'required|array',
            'is_active' => 'boolean',
        ]);
        $automation->update($validated);
        return redirect()->route('automation.index')
            ->with('success', "Workflow berhasil diperbarui!");
    }

    public function destroy(AutomationWorkflow $automation)
    {
        $automation->delete();
        return redirect()->route('automation.index')
            ->with('success', 'Workflow berhasil dihapus!');
    }
}
