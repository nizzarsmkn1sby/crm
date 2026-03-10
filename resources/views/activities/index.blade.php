@extends('layouts.app')
@section('title', 'Aktivitas')
@section('breadcrumb')
    <i class="bi bi-activity text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Aktivitas</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <h1 class="page-title">Log Aktivitas</h1>
    </div>
    <form method="GET" class="filter-bar mb-4">
        <select name="type" class="filter-input">
            <option value="">Semua Tipe</option>
            @foreach(['call'=>'Telepon','email'=>'Email','whatsapp'=>'WhatsApp','meeting'=>'Meeting','note'=>'Catatan'] as $v=>$l)
                <option value="{{ $v }}" {{ request('type')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
        <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x"></i></a>
    </form>
    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead><tr><th>TIPE</th><th>SUBJEK</th><th>LEAD</th><th>OLEH</th><th>WAKTU</th><th>AKSI</th></tr></thead>
            <tbody>
                @forelse($activities as $act)
                    <tr>
                        <td>
                            <span class="flex items-center gap-2 text-sm">
                                <i class="bi {{ $act->type_icon }} text-indigo-400"></i>
                                <span class="text-gray-400 capitalize">{{ $act->type }}</span>
                            </span>
                        </td>
                        <td>
                            <div class="font-semibold text-sm text-white">{{ $act->subject }}</div>
                            @if($act->outcome)<div class="text-xs text-green-400 mt-0.5">✓ {{ $act->outcome }}</div>@endif
                        </td>
                        <td>
                            @if($act->lead)<a href="{{ route('leads.show', $act->lead) }}" class="text-indigo-400 hover:text-indigo-300 text-sm">{{ $act->lead->name }}</a>@else<span class="text-gray-600">—</span>@endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <img src="{{ $act->user->avatar_url }}" class="w-6 h-6 rounded-full" alt="">
                                <span class="text-xs text-gray-400">{{ $act->user->name }}</span>
                            </div>
                        </td>
                        <td class="text-gray-400 text-sm">{{ $act->created_at->diffForHumans() }}</td>
                        <td>
                            <form action="{{ route('activities.destroy', $act) }}" method="POST" onsubmit="return confirm('Hapus aktivitas ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-icon btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-activity"></i></div><div class="empty-state-title">Belum ada aktivitas</div></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $activities->links() }}</div>
</div>
@endsection
