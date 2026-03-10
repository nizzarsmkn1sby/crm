<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Stats dengan caching 5 menit (invalidate on write di controller lain)
        $stats = Cache::remember('dashboard_stats_' . $user->id, 300, function () use ($user) {
            $isManager = $user->isManager();

            return [
                'total_leads'       => Lead::count(),
                'new_leads_today'   => Lead::whereDate('created_at', today())->count(),
                'active_deals'      => Deal::where('status', 'open')->count(),
                'pipeline_value'    => Deal::where('status', 'open')->sum('value'),
                'won_this_month'    => Deal::where('status', 'won')
                    ->whereMonth('closed_date', now()->month)
                    ->whereYear('closed_date', now()->year)
                    ->count(),
                'won_value_month'   => Deal::where('status', 'won')
                    ->whereMonth('closed_date', now()->month)
                    ->whereYear('closed_date', now()->year)
                    ->sum('value'),
                'pending_tasks'     => Task::where('status', 'pending')
                    ->when(!$isManager, fn($q) => $q->where('assigned_to', $user->id))
                    ->count(),
                'overdue_tasks'     => Task::where('status', 'pending')
                    ->where('due_date', '<', now())
                    ->when(!$isManager, fn($q) => $q->where('assigned_to', $user->id))
                    ->count(),
                'today_meetings'    => Meeting::whereDate('start_at', today())->count(),
                'total_contacts'    => Contact::count(),
            ];
        });

        // Lead by status (cached 5 menit)
        $leadsByStatus = Cache::remember('leads_by_status', 300, function () {
            return Lead::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        });

        // Monthly revenue — relatif ke waktu jadi cache 1 jam
        $monthlyRevenue = Cache::remember('dashboard_monthly_revenue', 3600, function () {
            $data = [];
            for ($i = 5; $i >= 0; $i--) {
                $month  = now()->subMonths($i);
                $data[] = [
                    'month' => $month->format('M Y'),
                    'value' => Deal::where('status', 'won')
                        ->whereYear('closed_date', $month->year)
                        ->whereMonth('closed_date', $month->month)
                        ->sum('value'),
                ];
            }
            return $data;
        });

        // Recent activities — tidak di-cache karena real-time
        $recentActivities = Activity::with(['user', 'lead', 'contact'])
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // Upcoming meetings
        $upcomingMeetings = Meeting::with(['lead', 'contact', 'creator'])
            ->where('start_at', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('start_at')
            ->take(5)
            ->get();

        // Overdue tasks (role-aware)
        $overdueTasks = Task::with(['assignedUser', 'lead'])
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->when(!$user->isManager(), fn($q) => $q->where('assigned_to', $user->id))
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // Top leads
        $topLeads = Cache::remember('top_leads_by_value', 300, function () {
            return Lead::with(['assignedUser', 'pipelineStage'])
                ->orderByDesc('estimated_value')
                ->take(5)
                ->get();
        });

        // Conversion rate
        $conversionRate = Cache::remember('lead_conversion_rate', 600, function () {
            $total = Lead::count();
            $won   = Lead::where('status', 'won')->count();
            return $total > 0 ? round(($won / $total) * 100, 1) : 0;
        });

        return view('dashboard', compact(
            'stats', 'leadsByStatus', 'monthlyRevenue',
            'recentActivities', 'upcomingMeetings', 'overdueTasks',
            'topLeads', 'conversionRate'
        ));
    }
}
