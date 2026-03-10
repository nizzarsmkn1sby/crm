<?php

namespace App\Http\Controllers;

use App\Models\PipelineStage;
use App\Models\Lead;
use App\Models\Deal;
use Illuminate\Http\Request;

class PipelineController extends Controller
{
    public function index()
    {
        $stages = PipelineStage::with(['deals.lead', 'deals.contact', 'deals.assignedUser'])
            ->orderBy('order')
            ->get();

        $totalValue = Deal::where('status', 'open')->sum('value');
        return view('pipeline.index', compact('stages', 'totalValue'));
    }

    public function leadsView()
    {
        $stages = PipelineStage::with(['leads.assignedUser'])
            ->orderBy('order')
            ->get();

        return view('pipeline.leads', compact('stages'));
    }

    public function reorder(Request $request)
    {
        foreach ($request->stages as $index => $id) {
            PipelineStage::where('id', $id)->update(['order' => $index]);
        }
        return response()->json(['success' => true]);
    }

    public function storeStage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
        ]);

        $maxOrder = PipelineStage::max('order') ?? 0;
        PipelineStage::create([
            'name' => $request->name,
            'color' => $request->color ?? '#6366f1',
            'order' => $maxOrder + 1,
            'is_won' => $request->boolean('is_won'),
            'is_lost' => $request->boolean('is_lost'),
        ]);

        return back()->with('success', 'Stage berhasil ditambahkan!');
    }

    public function updateStage(Request $request, PipelineStage $stage)
    {
        $request->validate(['name' => 'required|string|max:255', 'color' => 'nullable|string']);
        $stage->update($request->only('name', 'color', 'is_won', 'is_lost'));
        return back()->with('success', 'Stage berhasil diperbarui!');
    }

    public function destroyStage(PipelineStage $stage)
    {
        $stage->delete();
        return back()->with('success', 'Stage berhasil dihapus!');
    }
}
