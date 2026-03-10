<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use App\Models\Activity;
use App\Http\Requests\LeadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['assignedUser', 'pipelineStage']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('status'))           $query->where('status', $request->status);
        if ($request->filled('priority'))         $query->where('priority', $request->priority);
        if ($request->filled('source'))           $query->where('source', $request->source);
        if ($request->filled('assigned_to'))      $query->where('assigned_to', $request->assigned_to);
        if ($request->filled('pipeline_stage_id')) $query->where('pipeline_stage_id', $request->pipeline_stage_id);
        if ($request->filled('date_from'))        $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))          $query->whereDate('created_at', '<=', $request->date_to);

        // Sort
        $sortBy  = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['name', 'created_at', 'estimated_value', 'status', 'priority', 'last_contacted_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'created_at';
        $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');

        $leads  = $query->paginate(20)->withQueryString();
        $stages = PipelineStage::orderBy('order')->get();
        $users  = User::where('is_active', true)->get();

        // Status count (cached 5 menit untuk performa)
        $statusCounts = Cache::remember('lead_status_counts', 300, function () {
            return Lead::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
        });

        return view('leads.index', compact('leads', 'stages', 'users', 'statusCounts'));
    }

    public function create()
    {
        $stages = PipelineStage::orderBy('order')->get();
        $users  = User::where('is_active', true)->get();
        return view('leads.create', compact('stages', 'users'));
    }

    public function store(LeadRequest $request)
    {
        $lead = Lead::create($request->validated());

        Activity::create([
            'type'        => 'note',
            'subject'     => 'Lead baru dibuat',
            'description' => "Lead {$lead->name} berhasil ditambahkan ke sistem",
            'lead_id'     => $lead->id,
            'user_id'     => Auth::id(),
            'activity_at' => now(),
            'status'      => 'completed',
        ]);

        event(new \App\Events\LeadCreated($lead));
        Cache::forget('lead_status_counts');

        return redirect()->route('leads.show', $lead)
            ->with('success', "Lead {$lead->name} berhasil ditambahkan!");
    }

    public function show(Lead $lead)
    {
        $lead->load([
            'assignedUser', 'pipelineStage', 'contacts', 'deals.pipelineStage',
            'activities.user', 'tasks.assignedUser', 'meetings', 'documents.uploader',
        ]);
        $stages = PipelineStage::orderBy('order')->get();
        $users  = User::where('is_active', true)->get();

        return view('leads.show', compact('lead', 'stages', 'users'));
    }

    public function edit(Lead $lead)
    {
        $stages = PipelineStage::orderBy('order')->get();
        $users  = User::where('is_active', true)->get();
        return view('leads.edit', compact('lead', 'stages', 'users'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        $oldStatus = $lead->status;
        $lead->update($request->validated());

        if ($oldStatus !== $lead->status) {
            Activity::create([
                'type'        => 'note',
                'subject'     => 'Status lead diubah',
                'description' => "Status berubah dari {$oldStatus} ke {$lead->status}",
                'lead_id'     => $lead->id,
                'user_id'     => Auth::id(),
                'activity_at' => now(),
                'status'      => 'completed',
            ]);

            event(new \App\Events\LeadStatusChanged($lead, $oldStatus));
        }

        Cache::forget('lead_status_counts');

        return redirect()->route('leads.show', $lead)
            ->with('success', "Lead {$lead->name} berhasil diperbarui!");
    }

    public function destroy(Lead $lead)
    {
        $name = $lead->name;
        $lead->delete();
        Cache::forget('lead_status_counts');
        return redirect()->route('leads.index')
            ->with('success', "Lead {$name} berhasil dihapus!");
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $request->validate(['pipeline_stage_id' => 'required|exists:pipeline_stages,id']);
        $lead->update(['pipeline_stage_id' => $request->pipeline_stage_id]);
        return response()->json(['success' => true]);
    }

    /**
     * Bulk actions: delete, assign, status change, export
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'action'  => 'required|string|in:delete,assign,status,export',
            'ids'     => 'required|array|min:1',
            'ids.*'   => 'exists:leads,id',
            'assign_to' => 'nullable|exists:users,id',
            'status'    => 'nullable|string',
        ]);

        $ids    = $request->ids;
        $action = $request->action;

        switch ($action) {
            case 'delete':
                Lead::whereIn('id', $ids)->delete();
                Cache::forget('lead_status_counts');
                return back()->with('success', count($ids) . ' lead berhasil dihapus!');

            case 'assign':
                $request->validate(['assign_to' => 'required|exists:users,id']);
                Lead::whereIn('id', $ids)->update(['assigned_to' => $request->assign_to]);
                return back()->with('success', count($ids) . ' lead berhasil di-assign!');

            case 'status':
                $request->validate(['status' => 'required|string']);
                Lead::whereIn('id', $ids)->update(['status' => $request->status]);
                Cache::forget('lead_status_counts');
                return back()->with('success', 'Status ' . count($ids) . ' lead berhasil diubah!');

            case 'export':
                return $this->exportSelected($ids);
        }
    }

    private function exportSelected(array $ids)
    {
        $leads = Lead::with(['assignedUser', 'pipelineStage'])->whereIn('id', $ids)->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8'];
        $filename = 'leads-export-' . date('Ymd-His') . '.csv';

        $callback = function () use ($leads) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, ['ID', 'Nama', 'Perusahaan', 'Email', 'Telepon', 'WhatsApp', 'Status', 'Prioritas', 'Sumber', 'Nilai Est.', 'Salesperson', 'Stage', 'Dibuat']);

            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->name,
                    $lead->company,
                    $lead->email,
                    $lead->phone,
                    $lead->whatsapp,
                    $lead->status,
                    $lead->priority,
                    $lead->source,
                    $lead->estimated_value,
                    $lead->assignedUser?->name,
                    $lead->pipelineStage?->name,
                    $lead->created_at->format('d/m/Y'),
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function sendWhatsapp(Request $request, Lead $lead)
    {
        $request->validate(['message' => 'required|string']);

        $whatsappService = app(\App\Services\WhatsappService::class);
        $result = $whatsappService->send($lead->whatsapp ?? $lead->phone, $request->message, $lead->id);

        if ($result['success']) {
            Activity::create([
                'type'        => 'whatsapp',
                'subject'     => 'Pesan WhatsApp dikirim',
                'description' => $request->message,
                'lead_id'     => $lead->id,
                'user_id'     => Auth::id(),
                'activity_at' => now(),
                'status'      => 'completed',
            ]);
            $lead->update(['last_contacted_at' => now()]);
            return back()->with('success', 'Pesan WhatsApp berhasil dikirim!');
        }

        return back()->with('error', 'Gagal mengirim WhatsApp: ' . $result['message']);
    }

    public function sendEmail(Request $request, Lead $lead)
    {
        $request->validate([
            'subject' => 'required|string',
            'body'    => 'required|string',
        ]);

        $emailService = app(\App\Services\EmailService::class);
        $result = $emailService->send($lead->email, $request->subject, $request->body, $lead->id);

        if ($result['success']) {
            Activity::create([
                'type'        => 'email',
                'subject'     => $request->subject,
                'description' => $request->body,
                'lead_id'     => $lead->id,
                'user_id'     => Auth::id(),
                'activity_at' => now(),
                'status'      => 'completed',
            ]);
            $lead->update(['last_contacted_at' => now()]);
            return back()->with('success', 'Email berhasil dikirim!');
        }

        return back()->with('error', 'Gagal mengirim email: ' . $result['message']);
    }
}
