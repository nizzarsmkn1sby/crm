@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('breadcrumb')
    <a href="{{ route('reports.index') }}" class="text-gray-400 hover:text-white text-sm">Laporan</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Penjualan</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1 class="page-title">Laporan Penjualan</h1>
        <div class="flex gap-2 items-center flex-wrap">
            <form method="GET" action="{{ route('reports.sales') }}" class="flex gap-2 items-center">
                <input type="date" name="from" value="{{ $from }}" class="filter-input">
                <span class="text-gray-500">s/d</span>
                <input type="date" name="to" value="{{ $to }}" class="filter-input">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            </form>
            {{-- Export --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="btn btn-secondary btn-sm flex items-center gap-1">
                    <i class="bi bi-download"></i> Export <i class="bi bi-chevron-down text-[10px]"></i>
                </button>
                <div class="user-dropdown" x-show="open" @click.outside="open = false" x-transition style="width:170px;">
                    <a href="{{ route('export.sales.csv', ['from'=>$from,'to'=>$to]) }}" class="dropdown-item">
                        <i class="bi bi-filetype-csv text-green-400"></i> Export CSV
                    </a>
                    <a href="{{ route('export.sales.pdf', ['from'=>$from,'to'=>$to]) }}" class="dropdown-item">
                        <i class="bi bi-filetype-pdf text-red-400"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid-cols-4 mb-6">
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-trophy-fill"></i></div>
            <div>
                <div class="stat-value">{{ $totalDeals }}</div>
                <div class="stat-label">Deal Menang</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value">Rp{{ number_format($totalValue/1000000,1) }}M</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-calculator"></i></div>
            <div>
                <div class="stat-value">Rp{{ number_format($avgDealValue/1000000,1) }}M</div>
                <div class="stat-label">Avg Deal Value</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><i class="bi bi-percent"></i></div>
            <div>
                <div class="stat-value">{{ $conversionRate }}%</div>
                <div class="stat-label">Konversi Lead</div>
            </div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px;">
        {{-- Revenue Trend --}}
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-graph-up text-green-400 mr-2"></i>Trend Revenue Harian</h3>
            <canvas id="trendChart" height="200"></canvas>
        </div>

        {{-- By User --}}
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-people text-blue-400 mr-2"></i>Performa Salesperson</h3>
            @forelse($byUser as $perf)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-300 font-medium">{{ $perf['user'] }}</span>
                        <span class="text-emerald-400 font-bold">{{ $perf['count'] }} deal · Rp{{ number_format($perf['value']/1000000,1) }}M</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:{{ $totalValue > 0 ? round($perf['value']/$totalValue*100) : 0 }}%;"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Belum ada data</p>
            @endforelse
        </div>
    </div>

    {{-- Activity Summary --}}
    <div class="crm-card mb-4">
        <h3 class="crm-card-title mb-4"><i class="bi bi-activity text-purple-400 mr-2"></i>Ringkasan Aktivitas</h3>
        <div class="grid-cols-4">
            @foreach(['call'=>'Telepon','email'=>'Email','whatsapp'=>'WhatsApp','meeting'=>'Meeting'] as $type=>$label)
                <div class="text-center py-3 bg-gray-900/50 rounded-xl">
                    <div class="text-2xl font-bold text-white">{{ $activitiesByType[$type] ?? 0 }}</div>
                    <div class="text-sm text-gray-400 mt-1">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Won Deals Table --}}
    <div class="crm-table-wrapper">
        <div class="crm-card-header" style="padding:16px 20px;border-bottom:1px solid rgba(255,255,255,0.07);">
            <h3 class="crm-card-title"><i class="bi bi-table text-indigo-400 mr-2"></i>Daftar Deal Menang ({{ $totalDeals }})</h3>
        </div>
        <table class="crm-table">
            <thead>
                <tr>
                    <th>DEAL</th>
                    <th>LEAD</th>
                    <th>SALESPERSON</th>
                    <th>NILAI</th>
                    <th>TANGGAL MENANG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wonDeals as $deal)
                    <tr>
                        <td class="font-semibold text-white"><a href="{{ route('deals.show', $deal) }}" class="hover:text-indigo-400">{{ $deal->title }}</a></td>
                        <td class="text-gray-400">{{ $deal->lead?->name ?? '—' }}</td>
                        <td class="text-gray-400">{{ $deal->assignedUser?->name ?? '—' }}</td>
                        <td class="font-bold text-emerald-400">Rp {{ number_format($deal->value,0,',','.') }}</td>
                        <td class="text-gray-400">{{ $deal->closed_date ? \Carbon\Carbon::parse($deal->closed_date)->format('d M Y') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-trophy"></i></div><div class="empty-state-title">Belum ada deal menang dalam periode ini</div></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.font.family = 'Plus Jakarta Sans';
const trendData = @json($trend);
new Chart(document.getElementById('trendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: trendData.map(d => d.date),
        datasets: [{
            label: 'Revenue',
            data: trendData.map(d => d.total),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#10b981',
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
</script>
@endpush
