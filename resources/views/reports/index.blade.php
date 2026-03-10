@extends('layouts.app')
@section('title', 'Laporan CRM')
@section('breadcrumb')
    <i class="bi bi-bar-chart text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Laporan</span>
@endsection
@section('content')
<div class="fade-in">
    <h1 class="page-title mb-6">Laporan & Analitik</h1>
    <div class="grid-cols-3">
        <a href="{{ route('reports.sales') }}" class="crm-card hover:border-indigo-500/50 transition-all cursor-pointer" style="text-decoration:none;">
            <div class="text-5xl mb-4">💰</div>
            <h3 class="text-lg font-bold text-white mb-2">Laporan Penjualan</h3>
            <p class="text-sm text-gray-400">Revenue, deal won/lost, rata-rata deal value, performa per salesperson</p>
            <div class="mt-4"><span class="btn btn-primary btn-sm">Lihat Laporan →</span></div>
        </a>
        <a href="{{ route('reports.leads') }}" class="crm-card hover:border-indigo-500/50 transition-all cursor-pointer" style="text-decoration:none;">
            <div class="text-5xl mb-4">📊</div>
            <h3 class="text-lg font-bold text-white mb-2">Laporan Lead</h3>
            <p class="text-sm text-gray-400">Sumber lead, tingkat konversi, trend masuk, lead per stage</p>
            <div class="mt-4"><span class="btn btn-primary btn-sm">Lihat Laporan →</span></div>
        </a>
        <div class="crm-card" style="opacity:0.6;">
            <div class="text-5xl mb-4">📈</div>
            <h3 class="text-lg font-bold text-white mb-2">Laporan Aktivitas</h3>
            <p class="text-sm text-gray-400">Log panggilan, email, WhatsApp, dan meeting per tim</p>
            <div class="mt-4"><span class="badge badge-pending">Segera Hadir</span></div>
        </div>
    </div>
</div>
@endsection
