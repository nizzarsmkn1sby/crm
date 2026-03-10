@extends('layouts.app')
@section('title', 'Deals')
@section('breadcrumb')
    <i class="bi bi-bag-check text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Deals</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div><h1 class="page-title">Manajemen Deal</h1><p class="page-subtitle">{{ $deals->total() }} deal</p></div>
        <a href="{{ route('deals.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Deal</a>
    </div>
    <form method="GET" action="{{ route('deals.index') }}">
        <div class="filter-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari deal..." class="filter-input" style="flex:1;min-width:180px;">
            <select name="status" class="filter-input">
                <option value="">Semua Status</option>
                @foreach(['open'=>'Open','won'=>'Won','lost'=>'Lost'] as $v=>$l)<option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>@endforeach
            </select>
            <select name="pipeline_stage_id" class="filter-input">
                <option value="">Semua Stage</option>
                @foreach($stages as $stage)<option value="{{ $stage->id }}" {{ request('pipeline_stage_id')==$stage->id?'selected':'' }}>{{ $stage->name }}</option>@endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('deals.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i></a>
        </div>
    </form>
    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead><tr><th>JUDUL DEAL</th><th>LEAD</th><th>STAGE</th><th>NILAI</th><th>PROBABILITAS</th><th>STATUS</th><th>CLOSING</th><th>PIC</th><th>AKSI</th></tr></thead>
            <tbody>
                @forelse($deals as $deal)
                    <tr>
                        <td><a href="{{ route('deals.show', $deal) }}" class="font-semibold text-white hover:text-indigo-400">{{ $deal->title }}</a></td>
                        <td>@if($deal->lead)<a href="{{ route('leads.show', $deal->lead) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">{{ $deal->lead->name }}</a>@else<span class="text-gray-600">—</span>@endif</td>
                        <td>@if($deal->pipelineStage)<span class="badge" style="background:{{ $deal->pipelineStage->color }}20;color:{{ $deal->pipelineStage->color }};">{{ $deal->pipelineStage->name }}</span>@else<span class="text-gray-600">—</span>@endif</td>
                        <td class="font-bold" style="color:#6ee7b7">@if($deal->value)Rp{{ number_format($deal->value/1000000,1) }}M@else<span class="text-gray-600">—</span>@endif</td>
                        <td>
                            @if($deal->probability)
                                <div class="flex items-center gap-2">
                                    <div class="progress-bar" style="width:60px;"><div class="progress-fill" style="width:{{ $deal->probability }}%"></div></div>
                                    <span class="text-xs text-gray-400">{{ $deal->probability }}%</span>
                                </div>
                            @else<span class="text-gray-600">—</span>@endif
                        </td>
                        <td><span class="badge badge-{{ $deal->status }}">{{ ucfirst($deal->status) }}</span></td>
                        <td class="text-gray-400 text-sm">{{ $deal->expected_close_date ? \Carbon\Carbon::parse($deal->expected_close_date)->format('d M Y') : '—' }}</td>
                        <td>@if($deal->assignedUser)<div class="flex items-center gap-2"><img src="{{ $deal->assignedUser->avatar_url }}" class="w-6 h-6 rounded-full" alt=""><span class="text-xs text-gray-400">{{ $deal->assignedUser->name }}</span></div>@else<span class="text-gray-600">—</span>@endif</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('deals.show', $deal) }}" class="btn btn-secondary btn-icon"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('deals.edit', $deal) }}" class="btn btn-secondary btn-icon"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('deals.destroy', $deal) }}" method="POST" onsubmit="return confirm('Hapus deal?')">@csrf @method('DELETE')<button class="btn btn-danger btn-icon"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-bag-x"></i></div><div class="empty-state-title">Belum ada deal</div><a href="{{ route('deals.create') }}" class="btn btn-primary mt-2">Tambah Deal</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $deals->links() }}</div>
</div>
@endsection
