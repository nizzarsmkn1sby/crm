<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Company;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $query = Deal::with(['lead', 'contact', 'pipelineStage', 'assignedUser']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhereHas('lead', fn($q2) => $q2->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('contact', fn($q2) => $q2->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('pipeline_stage_id')) $query->where('pipeline_stage_id', $request->pipeline_stage_id);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);

        $deals = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $stages = PipelineStage::orderBy('order')->get();
        $users = User::where('is_active', true)->get();

        return view('deals.index', compact('deals', 'stages', 'users'));
    }

    public function create()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $leads = Lead::orderBy('name')->get();
        $contacts = Contact::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $users = User::where('is_active', true)->get();
        return view('deals.create', compact('stages', 'leads', 'contacts', 'companies', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lead_id' => 'nullable|exists:leads,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'assigned_to' => 'nullable|exists:users,id',
            'value' => 'nullable|numeric|min:0',
            'probability' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'tags' => 'nullable|string',
        ]);

        $deal = Deal::create($validated);

        Activity::create([
            'type' => 'note',
            'subject' => 'Deal baru dibuat',
            'description' => "Deal '{$deal->title}' senilai Rp " . number_format($deal->value, 0, ',', '.'),
            'deal_id' => $deal->id,
            'lead_id' => $deal->lead_id,
            'user_id' => Auth::id(),
            'activity_at' => now(),
            'status' => 'completed',
        ]);

        return redirect()->route('deals.show', $deal)
            ->with('success', "Deal '{$deal->title}' berhasil dibuat!");
    }

    public function show(Deal $deal)
    {
        $deal->load(['lead', 'contact', 'company', 'pipelineStage', 'assignedUser',
            'activities.user', 'tasks.assignedUser', 'documents.uploader']);
        $stages = PipelineStage::orderBy('order')->get();
        return view('deals.show', compact('deal', 'stages'));
    }

    public function edit(Deal $deal)
    {
        $stages = PipelineStage::orderBy('order')->get();
        $leads = Lead::orderBy('name')->get();
        $contacts = Contact::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $users = User::where('is_active', true)->get();
        return view('deals.edit', compact('deal', 'stages', 'leads', 'contacts', 'companies', 'users'));
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'lead_id' => 'nullable|exists:leads,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'pipeline_stage_id' => 'required|exists:pipeline_stages,id',
            'assigned_to' => 'nullable|exists:users,id',
            'value' => 'nullable|numeric|min:0',
            'probability' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:open,won,lost',
            'description' => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'closed_date' => 'nullable|date',
            'lost_reason' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $oldStage = $deal->pipeline_stage_id;
        if ($validated['status'] !== 'open' && !isset($validated['closed_date'])) {
            $validated['closed_date'] = now()->toDateString();
        }

        $deal->update($validated);

        if ($oldStage !== $deal->pipeline_stage_id) {
            Activity::create([
                'type' => 'note',
                'subject' => 'Stage deal diubah',
                'description' => "Deal dipindah ke stage baru",
                'deal_id' => $deal->id,
                'user_id' => Auth::id(),
                'activity_at' => now(),
                'status' => 'completed',
            ]);
        }

        return redirect()->route('deals.show', $deal)
            ->with('success', "Deal '{$deal->title}' berhasil diperbarui!");
    }

    public function destroy(Deal $deal)
    {
        $title = $deal->title;
        $deal->delete();
        return redirect()->route('deals.index')
            ->with('success', "Deal '{$title}' berhasil dihapus!");
    }

    public function updateStage(Request $request, Deal $deal)
    {
        $request->validate(['pipeline_stage_id' => 'required|exists:pipeline_stages,id']);
        $stage = PipelineStage::find($request->pipeline_stage_id);

        $data = ['pipeline_stage_id' => $request->pipeline_stage_id];
        if ($stage->is_won) $data['status'] = 'won';
        if ($stage->is_lost) $data['status'] = 'lost';

        $deal->update($data);
        return response()->json(['success' => true, 'deal' => $deal->fresh()]);
    }

    public function markWon(Deal $deal)
    {
        $deal->update(['status' => 'won', 'closed_date' => now()->toDateString()]);
        Activity::create([
            'type' => 'note',
            'subject' => 'Deal berhasil MENANG!',
            'description' => "Deal '{$deal->title}' senilai Rp " . number_format($deal->value, 0, ',', '.') . " berhasil ditutup.",
            'deal_id' => $deal->id,
            'lead_id' => $deal->lead_id,
            'user_id' => Auth::id(),
            'activity_at' => now(),
            'status' => 'completed',
        ]);
        return redirect()->route('deals.show', $deal)->with('success', "🏆 Deal '{$deal->title}' berhasil ditandai MENANG!");
    }

    public function markLost(Deal $deal)
    {
        $deal->update(['status' => 'lost', 'closed_date' => now()->toDateString()]);
        Activity::create([
            'type' => 'note',
            'subject' => 'Deal ditandai Kalah',
            'description' => "Deal '{$deal->title}' ditutup dengan status kalah.",
            'deal_id' => $deal->id,
            'lead_id' => $deal->lead_id,
            'user_id' => Auth::id(),
            'activity_at' => now(),
            'status' => 'completed',
        ]);
        return redirect()->route('deals.show', $deal)->with('error', "Deal '{$deal->title}' ditandai KALAH.");
    }
}
