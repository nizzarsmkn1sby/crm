<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Lead;
use App\Models\Contact;
use App\Services\WhatsappService;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::with('creator')->orderByDesc('created_at')->paginate(15);
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:email,whatsapp',
            'description' => 'nullable|string',
            'subject' => 'required_if:type,email|nullable|string',
            'content' => 'required|string',
            'scheduled_at' => 'nullable|date|after:now',
            'target_filters' => 'nullable|array',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $request->filled('scheduled_at') ? 'scheduled' : 'draft';

        $campaign = Campaign::create($validated);
        return redirect()->route('campaigns.show', $campaign)
            ->with('success', "Campaign '{$campaign->name}' berhasil dibuat!");
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['creator', 'emailLogs', 'whatsappLogs']);
        return view('campaigns.show', compact('campaign'));
    }

    public function send(Campaign $campaign)
    {
        if ($campaign->status === 'running') {
            return back()->with('error', 'Campaign sedang berjalan!');
        }

        $campaign->update(['status' => 'running']);

        // Get target recipients
        $query = Lead::query();
        if ($campaign->target_filters) {
            $filters = $campaign->target_filters;
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['source'])) {
                $query->where('source', $filters['source']);
            }
        }

        $leads = $query->get();
        $campaign->update(['total_recipients' => $leads->count()]);

        // Dispatch jobs
        foreach ($leads as $lead) {
            if ($campaign->type === 'whatsapp' && $lead->whatsapp) {
                dispatch(new \App\Jobs\SendWhatsappJob($lead, $campaign));
            } elseif ($campaign->type === 'email' && $lead->email) {
                dispatch(new \App\Jobs\SendEmailJob($lead, $campaign));
            }
        }

        return back()->with('success', "Campaign dimulai! Mengirim ke {$leads->count()} lead.");
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')
            ->with('success', 'Campaign berhasil dihapus!');
    }
}
