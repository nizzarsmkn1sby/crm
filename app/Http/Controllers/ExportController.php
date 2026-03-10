<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Deal;
use App\Models\Contact;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    // ─── Leads ───────────────────────────────────────────────────────────────

    public function leadsExportCsv(Request $request)
    {
        $query = Lead::with(['assignedUser', 'pipelineStage']);
        $this->applyLeadFilters($query, $request);

        $leads    = $query->get();
        $filename = 'leads-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($leads) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Nama', 'Perusahaan', 'Email', 'Telepon', 'WhatsApp',
                'Status', 'Prioritas', 'Sumber', 'Nilai Estimasi', 'Salesperson',
                'Stage Pipeline', 'Terakhir Dihubungi', 'Dibuat']);

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
                    $lead->last_contacted_at?->format('d/m/Y H:i'),
                    $lead->created_at->format('d/m/Y'),
                ]);
            }
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function leadsExportPdf(Request $request)
    {
        $query = Lead::with(['assignedUser', 'pipelineStage']);
        $this->applyLeadFilters($query, $request);
        $leads = $query->take(500)->get(); // limit PDF

        $pdf = Pdf::loadView('exports.leads-pdf', compact('leads'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('leads-' . date('Ymd') . '.pdf');
    }

    // ─── Sales Report ─────────────────────────────────────────────────────────

    public function salesExportCsv(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $deals    = Deal::where('status', 'won')
            ->whereBetween('closed_date', [$from, $to])
            ->with(['assignedUser', 'lead'])
            ->get();
        $filename = 'laporan-penjualan-' . date('Ymd') . '.csv';

        return response()->streamDownload(function () use ($deals) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Judul Deal', 'Lead', 'Salesperson', 'Nilai', 'Tanggal Menang']);

            foreach ($deals as $deal) {
                fputcsv($file, [
                    $deal->id,
                    $deal->title,
                    $deal->lead?->name,
                    $deal->assignedUser?->name,
                    $deal->value,
                    $deal->closed_date ? \Carbon\Carbon::parse($deal->closed_date)->format('d/m/Y') : '',
                ]);
            }
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function salesExportPdf(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $deals      = Deal::where('status', 'won')
            ->whereBetween('closed_date', [$from, $to])
            ->with(['assignedUser', 'lead'])
            ->get();
        $totalValue  = $deals->sum('value');
        $totalDeals  = $deals->count();

        $pdf = Pdf::loadView('exports.sales-pdf', compact('deals', 'totalValue', 'totalDeals', 'from', 'to'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-penjualan-' . date('Ymd') . '.pdf');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function applyLeadFilters($query, Request $request): void
    {
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('priority'))   $query->where('priority', $request->priority);
        if ($request->filled('source'))     $query->where('source', $request->source);
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);
    }
}
