@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <i class="bi bi-house-door text-gray-500"></i>
    <span class="text-gray-400">Dashboard</span>
@endsection

@section('content')
<div class="fade-in">
    {{-- Welcome Banner --}}
    <div class="crm-card mb-6" style="background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.15)); border-color: rgba(99,102,241,0.3);">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Selamat datang, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-gray-400 mt-1">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }} — Ringkasan aktivitas CRM Anda hari ini.</p>
            </div>
            <div class="text-6xl opacity-20">🎯</div>
        </div>
    </div>

    {{-- KPI Stats --}}
    <div class="grid-cols-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-person-plus-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_leads']) }}</div>
                <div class="stat-label">Total Lead</div>
                <div class="stat-change up"><i class="bi bi-arrow-up-short"></i>{{ $stats['new_leads_today'] }} baru hari ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-bag-check-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['active_deals']) }}</div>
                <div class="stat-label">Deal Aktif</div>
                <div class="stat-change" style="color:#93c5fd">Rp{{ number_format($stats['pipeline_value']/1000000,1) }}M pipeline</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['won_this_month']) }}</div>
                <div class="stat-label">Won Bulan Ini</div>
                <div class="stat-change up">Rp{{ number_format($stats['won_value_month']/1000000,1) }}M revenue</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><i class="bi bi-check2-square"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['pending_tasks']) }}</div>
                <div class="stat-label">Task Pending</div>
                @if($stats['overdue_tasks'] > 0)
                    <div class="stat-change down"><i class="bi bi-exclamation-triangle"></i>{{ $stats['overdue_tasks'] }} terlambat</div>
                @else
                    <div class="stat-change up">Semua on track!</div>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow"><i class="bi bi-calendar3"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['today_meetings']) }}</div>
                <div class="stat-label">Meeting Hari Ini</div>
                <div class="stat-change" style="color:#fcd34d">Terjadwal</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total_contacts']) }}</div>
                <div class="stat-label">Total Kontak</div>
                <div class="stat-change" style="color:#93c5fd">Database</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-percent"></i></div>
            <div>
                <div class="stat-value">{{ $conversionRate }}%</div>
                <div class="stat-label">Konversi Lead</div>
                <div class="stat-change up">Lead ke won</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-graph-up-arrow"></i></div>
            <div>
                <div class="stat-value">Rp{{ number_format($stats['pipeline_value']/1000000,0) }}M</div>
                <div class="stat-label">Total Pipeline</div>
                <div class="stat-change up">Potensi revenue</div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="dashboard-row grid-cols-2 mb-6" style="grid-template-columns: 2fr 1fr; gap: 16px;">
        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-graph-up-arrow text-indigo-400 mr-2"></i>Revenue 6 Bulan Terakhir</h3>
            </div>
            <canvas id="revenueChart" height="220"></canvas>
        </div>
        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-pie-chart-fill text-purple-400 mr-2"></i>Status Lead</h3>
            </div>
            <canvas id="leadStatusChart" height="220"></canvas>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid-cols-2 mb-6">
        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-calendar-event text-blue-400 mr-2"></i>Meeting Mendatang</h3>
                <a href="{{ route('meetings.index') }}" class="btn btn-secondary btn-sm">Lihat semua <i class="bi bi-arrow-right"></i></a>
            </div>
            @forelse($upcomingMeetings as $meeting)
                <div class="flex items-start gap-3 py-3 border-b border-gray-800/50 last:border-0">
                    <div class="text-center min-w-[44px] bg-indigo-500/10 rounded-lg px-2 py-1">
                        <div class="text-xs font-semibold text-indigo-400 uppercase">{{ $meeting->start_at->format('M') }}</div>
                        <div class="text-xl font-bold text-white leading-tight">{{ $meeting->start_at->format('d') }}</div>
                        <div class="text-xs text-gray-500">{{ $meeting->start_at->format('H:i') }}</div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-sm text-white">{{ $meeting->title }}</div>
                        @if($meeting->lead)<div class="text-xs text-gray-400 mt-1"><i class="bi bi-person mr-1"></i>{{ $meeting->lead->name }}</div>@endif
                        @if($meeting->location)<div class="text-xs text-gray-500"><i class="bi bi-geo-alt mr-1"></i>{{ Str::limit($meeting->location, 30) }}</div>@endif
                    </div>
                    <span class="badge badge-pending text-xs">{{ $meeting->duration }}</span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500"><i class="bi bi-calendar-x block text-3xl mb-2 opacity-30"></i><p class="text-sm">Tidak ada meeting mendatang</p></div>
            @endforelse
        </div>

        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-exclamation-triangle-fill text-red-400 mr-2"></i>Task Terlambat</h3>
                <a href="{{ route('tasks.index', ['filter'=>'overdue']) }}" class="btn btn-secondary btn-sm">Lihat semua</a>
            </div>
            @forelse($overdueTasks as $task)
                <div class="flex items-center gap-3 py-3 border-b border-gray-800/50 last:border-0">
                    <div class="flex-1">
                        <div class="font-semibold text-sm text-white">{{ $task->title }}</div>
                        <div class="text-xs text-red-400 mt-1"><i class="bi bi-clock mr-1"></i>{{ $task->due_date->format('d M Y, H:i') }}</div>
                        @if($task->lead)<div class="text-xs text-gray-500">Lead: {{ $task->lead->name }}</div>@endif
                    </div>
                    <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500"><i class="bi bi-check-circle block text-3xl mb-2 text-green-500 opacity-50"></i><p class="text-sm">Tidak ada task terlambat 🎉</p></div>
            @endforelse
        </div>
    </div>

    <div class="grid-cols-2">
        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-activity text-green-400 mr-2"></i>Aktivitas Terbaru</h3>
                <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm">Lihat semua</a>
            </div>
            <div class="timeline" style="padding-left:8px;">
                @forelse($recentActivities as $activity)
                    <div class="timeline-item">
                        <div class="timeline-icon" style="width:32px;height:32px;font-size:13px;">
                            <i class="bi {{ $activity->type_icon }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-sm">{{ Str::limit($activity->subject, 40) }}</span>
                                <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                            @if($activity->lead)<div class="text-xs text-gray-400">{{ $activity->lead->name }}</div>@endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>

        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-star-fill text-yellow-400 mr-2"></i>Top Lead (by Value)</h3>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary btn-sm">Semua lead</a>
            </div>
            @forelse($topLeads as $i => $lead)
                <a href="{{ route('leads.show', $lead) }}" class="flex items-center gap-3 py-3 border-b border-gray-800/50 last:border-0 hover:bg-white/5 rounded-lg px-2 -mx-2 transition-all">
                    <div class="w-7 h-7 rounded-full bg-indigo-500/20 flex items-center justify-center text-xs font-bold text-indigo-400">{{ $i+1 }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-white truncate">{{ $lead->name }}</div>
                        <div class="text-xs text-gray-400">{{ Str::limit($lead->company, 25) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold" style="color:#6ee7b7">Rp{{ number_format($lead->estimated_value/1000000,1) }}M</div>
                        <span class="badge badge-{{ $lead->status }}" style="font-size:10px;">{{ $lead->status }}</span>
                    </div>
                </a>
            @empty
                <p class="text-sm text-gray-500 py-4 text-center">Belum ada lead</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.font.family = 'Plus Jakarta Sans';

// Revenue Chart
new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json(array_column($monthlyRevenue, 'month')),
        datasets: [{
            label: 'Revenue',
            data: @json(array_column($monthlyRevenue, 'value')),
            backgroundColor: 'rgba(99,102,241,0.25)',
            borderColor: '#6366f1',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { callback: v => 'Rp' + (v/1000000).toFixed(0) + 'M' } }
        }
    }
});

// Lead Status
const statusData = @json($leadsByStatus);
const statusLabels = { new:'Baru',contacted:'Dihubungi',qualified:'Qualified',proposal:'Proposal',negotiation:'Negosiasi',won:'Won',lost:'Lost' };
new Chart(document.getElementById('leadStatusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(statusData).map(k => statusLabels[k]||k),
        datasets: [{ data: Object.values(statusData), backgroundColor: ['#3b82f6','#8b5cf6','#f59e0b','#06b6d4','#f97316','#10b981','#ef4444'], borderWidth: 0 }]
    },
    options: { responsive: true, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } } } }
});
</script>
@endpush
