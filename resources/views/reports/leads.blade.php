@extends('layouts.app')
@section('title', 'Laporan Lead')
@section('breadcrumb')
    <a href="{{ route('reports.index') }}" class="text-gray-400 hover:text-white text-sm">Laporan</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Lead</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1 class="page-title">Laporan Lead</h1>
        <form method="GET" class="flex gap-2 items-center">
            <input type="date" name="from" value="{{ $from }}" class="filter-input">
            <span class="text-gray-500">s/d</span>
            <input type="date" name="to" value="{{ $to }}" class="filter-input">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
        </form>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-bar-chart text-indigo-400 mr-2"></i>Lead per Sumber</h3>
            <canvas id="sourceChart" height="250"></canvas>
        </div>
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-pie-chart text-purple-400 mr-2"></i>Lead per Status</h3>
            <canvas id="statusChart" height="250"></canvas>
        </div>
    </div>

    <div class="crm-card">
        <h3 class="crm-card-title mb-4"><i class="bi bi-graph-up text-green-400 mr-2"></i>Trend Lead Baru per Hari</h3>
        <canvas id="newLeadsChart" height="150"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.font.family = 'Plus Jakarta Sans';
const opts = { responsive: true, plugins: { legend: { position: 'bottom' } } };

new Chart(document.getElementById('sourceChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($leadsBySource->keys()),
        datasets: [{ label: 'Lead', data: @json($leadsBySource->values()), backgroundColor: 'rgba(99,102,241,0.5)', borderColor: '#6366f1', borderWidth: 2, borderRadius: 8 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { color: 'rgba(255,255,255,0.05)' } }, y: { grid: { color: 'rgba(255,255,255,0.05)' } } } }
});

new Chart(document.getElementById('statusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: @json($leadsByStatus->keys()),
        datasets: [{ data: @json($leadsByStatus->values()), backgroundColor: ['#3b82f6','#8b5cf6','#f59e0b','#06b6d4','#f97316','#10b981','#ef4444'], borderWidth: 0 }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom' } } }
});

new Chart(document.getElementById('newLeadsChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($newLeadsPerDay->pluck('date')),
        datasets: [{ label: 'Lead Baru', data: @json($newLeadsPerDay->pluck('count')), borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.4 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { x: { grid: { color: 'rgba(255,255,255,0.05)' } }, y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { stepSize: 1 } } } }
});
</script>
@endpush
