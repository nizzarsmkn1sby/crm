@extends('layouts.app')
@section('title', 'Otomasi Workflow')
@section('breadcrumb')
    <i class="bi bi-robot text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Otomasi</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div><h1 class="page-title">Otomasi Workflow</h1><p class="page-subtitle">Automasi berulang dengan mudah</p></div>
        <a href="{{ route('automation.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Workflow</a>
    </div>
    @forelse($workflows as $wf)
        <div class="crm-card mb-3">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0" style="background:rgba(99,102,241,0.15);">
                    <i class="bi bi-lightning-charge text-indigo-400"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-white">{{ $wf->name }}</span>
                        <span class="badge {{ $wf->is_active ? 'badge-won' : 'badge-pending' }}">{{ $wf->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <div class="text-sm text-gray-400 mt-1">Trigger: <strong class="text-indigo-400">{{ $wf->trigger }}</strong></div>
                    @if($wf->description)<p class="text-xs text-gray-500 mt-1">{{ $wf->description }}</p>@endif
                </div>
                <div class="text-sm text-gray-500">{{ $wf->created_at->format('d M Y') }}</div>
                <div class="flex gap-2">
                    <a href="{{ route('automation.edit', $wf) }}" class="btn btn-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('automation.destroy', $wf) }}" method="POST" onsubmit="return confirm('Hapus workflow?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="crm-card">
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-robot"></i></div>
                <div class="empty-state-title">Belum ada workflow otomasi</div>
                <div class="empty-state-desc">Buat workflow untuk mengotomasi tugas berulang seperti pengiriman WA/email saat lead baru masuk.</div>
                <a href="{{ route('automation.create') }}" class="btn btn-primary mt-2">Buat Workflow Pertama</a>
            </div>
        </div>
    @endforelse
</div>
@endsection
