@extends('layouts.app')
@section('title', 'Pipeline — Kanban Board')
@section('breadcrumb')
    <i class="bi bi-kanban text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Pipeline</span>
@endsection
@push('styles')
<style>
.kanban-board { align-items: flex-start; }
.kanban-card.dragging { opacity: 0.4; transform: rotate(2deg); }
.kanban-column.drag-over { border-color: rgba(99,102,241,0.5); background: rgba(99,102,241,0.05); }
</style>
@endpush
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">Pipeline Penjualan</h1>
            <p class="page-subtitle">Total pipeline: <strong style="color:#6ee7b7">Rp{{ number_format($totalValue/1000000,1) }}M</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pipeline.leads') }}" class="btn btn-secondary"><i class="bi bi-person-plus"></i> Pipeline Lead</a>
            <a href="{{ route('deals.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Deal</a>
        </div>
    </div>

    <div class="kanban-board" id="kanbanBoard">
        @foreach($stages as $stage)
            @php
                $stageDeals = $stage->deals->where('status','open');
                $stageValue = $stageDeals->sum('value');
            @endphp
            <div class="kanban-column" data-stage-id="{{ $stage->id }}" id="stage-{{ $stage->id }}">
                <div class="kanban-column-header">
                    <div class="kanban-column-dot" style="background:{{ $stage->color }}"></div>
                    <span class="kanban-column-title">{{ $stage->name }}</span>
                    <span class="kanban-column-count">{{ $stageDeals->count() }}</span>
                    @if($stageValue > 0)
                        <span class="text-xs" style="color:#6ee7b7">Rp{{ number_format($stageValue/1000000,1) }}M</span>
                    @endif
                </div>
                <div class="kanban-cards" id="cards-{{ $stage->id }}" data-stage="{{ $stage->id }}">
                    @forelse($stageDeals as $deal)
                        <div class="kanban-card" draggable="true" data-deal-id="{{ $deal->id }}">
                            <div class="kanban-card-title">
                                <a href="{{ route('deals.show', $deal) }}" class="hover:text-indigo-400 transition-colors">{{ $deal->title }}</a>
                            </div>
                            @if($deal->value)
                                <div class="kanban-card-value mb-2">Rp{{ number_format($deal->value/1000000,1) }}M</div>
                            @endif
                            <div class="kanban-card-meta flex-wrap gap-1">
                                @if($deal->lead)
                                    <span class="flex items-center gap-1"><i class="bi bi-person"></i>{{ Str::limit($deal->lead->name,20) }}</span>
                                @endif
                                @if($deal->probability)
                                    <span class="flex items-center gap-1"><i class="bi bi-percent"></i>{{ $deal->probability }}%</span>
                                @endif
                            </div>
                            @if($deal->probability)
                                <div class="progress-bar mt-2">
                                    <div class="progress-fill" style="width:{{ $deal->probability }}%;background:{{ $stage->color }}"></div>
                                </div>
                            @endif
                            @if($deal->expected_close_date)
                                <div class="text-xs mt-2 {{ $deal->expected_close_date->isPast() ? 'text-red-400' : 'text-gray-500' }}">
                                    <i class="bi bi-calendar mr-1"></i>{{ $deal->expected_close_date->format('d M Y') }}
                                </div>
                            @endif
                            @if($deal->assignedUser)
                                <div class="flex items-center gap-1 mt-2">
                                    <img src="{{ $deal->assignedUser->avatar_url }}" class="w-5 h-5 rounded-full" alt="">
                                    <span class="text-xs text-gray-500">{{ $deal->assignedUser->name }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-600 text-sm">
                            <i class="bi bi-inbox block text-2xl mb-2 opacity-50"></i>
                            Belum ada deal
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
// Drag & drop Kanban
let draggedCard = null;

document.querySelectorAll('.kanban-card').forEach(card => {
    card.addEventListener('dragstart', (e) => {
        draggedCard = card;
        setTimeout(() => card.classList.add('dragging'), 0);
    });
    card.addEventListener('dragend', () => {
        card.classList.remove('dragging');
        draggedCard = null;
    });
});

document.querySelectorAll('.kanban-cards').forEach(col => {
    col.addEventListener('dragover', (e) => {
        e.preventDefault();
        col.closest('.kanban-column').classList.add('drag-over');
    });
    col.addEventListener('dragleave', () => {
        col.closest('.kanban-column').classList.remove('drag-over');
    });
    col.addEventListener('drop', (e) => {
        e.preventDefault();
        if (!draggedCard) return;
        col.closest('.kanban-column').classList.remove('drag-over');
        col.appendChild(draggedCard);
        const dealId = draggedCard.dataset.dealId;
        const stageId = col.dataset.stage;

        fetch(`/deals/${dealId}/stage`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ pipeline_stage_id: stageId })
        }).catch(err => console.error(err));
    });
});
</script>
@endpush
