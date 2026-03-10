@extends('layouts.app')
@section('title', $deal->title)
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $deal->title }}</h1>
            <div class="flex items-center gap-2 mt-2">
                @if($deal->pipelineStage)<span class="badge" style="background:{{ $deal->pipelineStage->color }}20;color:{{ $deal->pipelineStage->color }};">{{ $deal->pipelineStage->name }}</span>@endif
                <span class="badge badge-{{ $deal->status }}">{{ ucfirst($deal->status) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            @if($deal->status === 'open')
                <form action="{{ route('deals.won', $deal) }}" method="POST">@csrf<button class="btn btn-success"><i class="bi bi-trophy"></i> Menang</button></form>
                <form action="{{ route('deals.lost', $deal) }}" method="POST">@csrf<button class="btn btn-danger"><i class="bi bi-x-circle"></i> Kalah</button></form>
            @endif
            <a href="{{ route('deals.edit', $deal) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-info-circle text-indigo-400 mr-2"></i>Detail Deal</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div><div class="text-xs text-gray-500">Lead</div>@if($deal->lead)<a href="{{ route('leads.show',$deal->lead) }}" class="text-indigo-400 text-sm">{{ $deal->lead->name }}</a>@else<span class="text-gray-600">—</span>@endif</div>
                <div><div class="text-xs text-gray-500">Nilai</div><span class="font-bold text-emerald-400">Rp{{ number_format($deal->value ?? 0,0,',','.') }}</span></div>
                <div><div class="text-xs text-gray-500">Probabilitas</div><span class="text-gray-300">{{ $deal->probability ?? 0 }}%</span></div>
                <div><div class="text-xs text-gray-500">Target Closing</div><span class="text-gray-300">{{ $deal->expected_close_date ? \Carbon\Carbon::parse($deal->expected_close_date)->format('d M Y') : '—' }}</span></div>
                <div><div class="text-xs text-gray-500">PIC</div><span class="text-gray-300">{{ $deal->assignedUser?->name ?? '—' }}</span></div>
                <div><div class="text-xs text-gray-500">Dibuat</div><span class="text-gray-300">{{ $deal->created_at->format('d M Y') }}</span></div>
            </div>
            @if($deal->description)<div class="mt-4 pt-4 border-t border-gray-800"><div class="text-xs text-gray-500 mb-1">Catatan</div><p class="text-sm text-gray-300">{{ $deal->description }}</p></div>@endif
        </div>
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-kanban text-purple-400 mr-2"></i>Ubah Stage</h3>
            @foreach($stages as $stage)
                <button onclick="changeStage({{ $deal->id }}, {{ $stage->id }})"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-all hover:bg-white/5 w-full text-left mb-1 {{ $deal->pipeline_stage_id==$stage->id ? 'bg-white/10 font-semibold text-white' : 'text-gray-400' }}">
                    <span class="w-3 h-3 rounded-full" style="background:{{ $stage->color }}"></span>
                    {{ $stage->name }}
                    @if($deal->pipeline_stage_id==$stage->id)<i class="bi bi-check ml-auto text-green-400"></i>@endif
                </button>
            @endforeach
        </div>
    </div>
</div>
@push('scripts')
<script>
function changeStage(dealId, stageId) {
    fetch(`/deals/${dealId}/stage`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ pipeline_stage_id: stageId })
    }).then(r => r.json()).then(d => { if(d.success) location.reload() });
}
</script>
@endpush
@endsection
