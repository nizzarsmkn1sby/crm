<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Deal;
use App\Models\Activity;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $wonDeals = Deal::where('status', 'won')
            ->whereBetween('closed_date', [$from, $to])
            ->with(['assignedUser', 'lead'])
            ->get();

        $totalValue = $wonDeals->sum('value');
        $totalDeals = $wonDeals->count();
        $avgDealValue = $totalDeals > 0 ? $totalValue / $totalDeals : 0;

        // By assignee
        $byUser = $wonDeals->groupBy('assigned_to')->map(function($deals, $userId) {
            return [
                'user' => $deals->first()->assignedUser?->name ?? 'Unassigned',
                'count' => $deals->count(),
                'value' => $deals->sum('value'),
            ];
        })->values();

        // Lead conversion
        $totalLeads = Lead::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->count();
        $wonLeads = Lead::where('status', 'won')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->count();
        $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;

        // Daily/Weekly trend
        $trend = Deal::where('status', 'won')
            ->whereBetween('closed_date', [$from, $to])
            ->select(DB::raw('DATE(closed_date) as date'), DB::raw('count(*) as count'), DB::raw('sum(value) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Activity summary
        $activitiesByType = Activity::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type');

        return view('reports.sales', compact(
            'wonDeals', 'totalValue', 'totalDeals', 'avgDealValue',
            'byUser', 'totalLeads', 'conversionRate', 'trend',
            'activitiesByType', 'from', 'to'
        ));
    }

    public function leads(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to = $request->get('to', now()->toDateString());

        $leadsBySource = Lead::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source');

        $leadsByStatus = Lead::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $newLeadsPerDay = Lead::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.leads', compact(
            'leadsBySource', 'leadsByStatus', 'newLeadsPerDay', 'from', 'to'
        ));
    }
}
