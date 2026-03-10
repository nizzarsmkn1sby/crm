@extends('layouts.app')
@section('title', 'Pipeline Lead')
@section('breadcrumb')
    <a href="{{ route('pipeline.index') }}" class="text-gray-400 text-sm">Pipeline</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm">Lead View</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div><h1 class="page-title">Pipeline Lead</h1></div>
        <div class="flex gap-2">
            <a href="{{ route('pipeline.index') }}" class="btn btn-secondary"><i class="bi bi-bag-check"></i> Pipeline Deal</a>
            <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Lead</a>
        </div>
    </div>
    <div class="kanban-board">
        @foreach($stages as $stage)
            @php $stageLeads = $stage->leads; @endphp
            <div class="kanban-column">
                <div class="kanban-column-header">
                    <div class="kanban-column-dot" style="background:{{ $stage->color }}"></div>
                    <span class="kanban-column-title">{{ $stage->name }}</span>
                    <span class="kanban-column-count">{{ $stageLeads->count() }}</span>
                </div>
                <div class="kanban-cards">
                    @forelse($stageLeads as $lead)
                        <div class="kanban-card">
                            <div class="kanban-card-title">
                                <a href="{{ route('leads.show', $lead) }}" class="hover:text-indigo-400">{{ $lead->name }}</a>
                            </div>
                            @if($lead->company)<div class="text-xs text-gray-500 mb-2">{{ $lead->company }}</div>@endif
                            @if($lead->estimated_value)<div class="kanban-card-value mb-2">Rp{{ number_format($lead->estimated_value/1000000,1) }}M</div>@endif
                            <div class="kanban-card-meta">
                                <span class="badge badge-{{ $lead->priority }}" style="font-size:10px;">{{ $lead->priority }}</span>
                                @if($lead->assignedUser)
                                    <img src="{{ $lead->assignedUser->avatar_url }}" class="w-4 h-4 rounded-full ml-auto" alt="" title="{{ $lead->assignedUser->name }}">
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-600 text-xs">Belum ada lead</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
