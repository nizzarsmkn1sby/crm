@extends('layouts.app')
@section('title', 'Notifikasi')
@section('breadcrumb')
    <i class="bi bi-bell text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Notifikasi</span>
@endsection
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header">
        <h1 class="page-title">Notifikasi</h1>
        <form action="{{ route('notifications.read-all') }}" method="POST">@csrf<button class="btn btn-secondary btn-sm"><i class="bi bi-check-all"></i> Tandai Semua Dibaca</button></form>
    </div>
    <div class="crm-card">
        @forelse($notifications as $notif)
            <div class="flex gap-3 py-4 border-b border-gray-800/50 last:border-0 {{ $notif->read_at ? 'opacity-60' : '' }}">
                <div class="w-10 h-10 rounded-full bg-indigo-500/15 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-info-circle text-indigo-400"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-300">{{ $notif->data['message'] ?? 'Notifikasi baru' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
                @if(!$notif->read_at)
                    <form action="{{ route('notifications.read', $notif->id) }}" method="POST">@csrf @method('PATCH')<button class="btn btn-secondary btn-sm flex-shrink-0">Baca</button></form>
                @endif
            </div>
        @empty
            <div class="empty-state"><div class="empty-state-icon"><i class="bi bi-bell-slash"></i></div><div class="empty-state-title">Belum ada notifikasi</div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
