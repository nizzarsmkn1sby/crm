@extends('layouts.app')
@section('title', $automation->name)
@section('breadcrumb')
    <a href="{{ route('automation.index') }}" class="text-gray-400 hover:text-white text-sm">Otomasi</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">{{ $automation->name }}</span>
@endsection

@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header mb-6">
        <div>
            <h1 class="page-title">{{ $automation->name }}</h1>
            <p class="page-subtitle">{{ $automation->description }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('automation.edit', $automation) }}" class="btn btn-secondary"><i class="bi bi-pencil"></i> Edit</a>
            <form action="{{ route('automation.destroy', $automation) }}" method="POST"
                onsubmit="return confirm('Hapus workflow ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>

    <div class="crm-card mb-4">
        <h3 class="crm-card-title mb-4"><i class="bi bi-info-circle text-indigo-400 mr-2"></i>Detail Workflow</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Status</p>
                <span class="badge {{ $automation->is_active ? 'badge-won' : 'badge-pending' }} text-sm">
                    {{ $automation->is_active ? '✅ Aktif' : '⏸ Nonaktif' }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Trigger</p>
                <span class="font-semibold text-indigo-400">{{ $automation->trigger_label }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Jumlah Dijalankan</p>
                <span class="font-bold text-white text-lg">{{ $automation->run_count }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Terakhir Dijalankan</p>
                <span class="text-gray-300 text-sm">{{ $automation->last_run_at ? $automation->last_run_at->diffForHumans() : 'Belum pernah' }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Dibuat Oleh</p>
                <span class="text-gray-300 text-sm">{{ $automation->creator?->name ?? 'Sistem' }}</span>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase mb-1">Dibuat</p>
                <span class="text-gray-300 text-sm">{{ $automation->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>

    @if(!empty($automation->trigger_conditions))
    <div class="crm-card mb-4">
        <h3 class="crm-card-title mb-4"><i class="bi bi-filter text-yellow-400 mr-2"></i>Kondisi Trigger</h3>
        @foreach($automation->trigger_conditions as $cond)
        <div class="flex items-center gap-2 py-2 border-b border-gray-800/50 last:border-0">
            <span class="badge badge-pending">{{ $cond['field'] ?? '—' }}</span>
            <span class="text-gray-500 text-sm">{{ $cond['operator'] ?? '=' }}</span>
            <span class="font-semibold text-white">{{ $cond['value'] ?? '—' }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="crm-card">
        <h3 class="crm-card-title mb-4"><i class="bi bi-lightning-charge text-green-400 mr-2"></i>Aksi yang Dijalankan</h3>
        @foreach($automation->actions as $i => $action)
        <div class="flex gap-3 py-3 border-b border-gray-800/50 last:border-0">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
                style="background:rgba(99,102,241,0.15);color:#a5b4fc;">{{ $i + 1 }}</div>
            <div class="flex-1">
                <p class="font-semibold text-white text-sm capitalize">{{ str_replace('_', ' ', $action['type'] ?? '—') }}</p>
                @if(!empty($action['message']))
                    <p class="text-xs text-gray-400 mt-1">{{ $action['message'] }}</p>
                @elseif(!empty($action['subject']))
                    <p class="text-xs text-gray-400 mt-1">{{ $action['subject'] }}</p>
                @elseif(!empty($action['title']))
                    <p class="text-xs text-gray-400 mt-1">{{ $action['title'] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
