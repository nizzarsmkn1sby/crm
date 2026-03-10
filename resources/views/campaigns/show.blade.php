@extends('layouts.app')
@section('title', $campaign->name)
@section('breadcrumb')
    <a href="{{ route('campaigns.index') }}" class="text-gray-400 hover:text-white text-sm">Kampanye</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">{{ Str::limit($campaign->name, 30) }}</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $campaign->name }}</h1>
            <div class="flex items-center gap-2 mt-2">
                <span class="badge {{ $campaign->type==='email'?'badge-new':'badge-won' }}">{{ strtoupper($campaign->type) }}</span>
                <span class="badge badge-{{ ['draft'=>'pending','scheduled'=>'contacted','running'=>'negotiation','completed'=>'won','failed'=>'lost'][$campaign->status]??'pending' }}">{{ ucfirst($campaign->status) }}</span>
            </div>
        </div>
        <div class="flex gap-2">
            @if(in_array($campaign->status,['draft','scheduled']))
                <form action="{{ route('campaigns.send', $campaign) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary"><i class="bi bi-send"></i> Kirim Sekarang</button>
                </form>
            @endif
            <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Hapus kampanye ini?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
        <div class="crm-card">
            <h3 class="crm-card-title mb-4"><i class="bi bi-chat-text text-indigo-400 mr-2"></i>Konten Kampanye</h3>
            @if($campaign->subject)<div class="mb-3"><div class="text-xs text-gray-500 mb-1">Subjek</div><div class="font-semibold text-white">{{ $campaign->subject }}</div></div>@endif
            <div class="bg-gray-900/50 rounded-lg p-4 text-sm text-gray-300 whitespace-pre-wrap">{{ $campaign->content }}</div>
        </div>
        <div class="flex flex-col gap-4">
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-info-circle text-blue-400 mr-2"></i>Info Kampanye</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Dibuat oleh</span><span>{{ $campaign->creator?->name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Dibuat pada</span><span>{{ $campaign->created_at->format('d M Y H:i') }}</span></div>
                    @if($campaign->scheduled_at)<div class="flex justify-between"><span class="text-gray-500">Dijadwalkan</span><span>{{ $campaign->scheduled_at->format('d M Y H:i') }}</span></div>@endif
                    <div class="flex justify-between"><span class="text-gray-500">Total Penerima</span><span class="font-bold text-white">{{ $campaign->total_recipients ?? 0 }}</span></div>
                </div>
            </div>
            @if($campaign->total_recipients > 0)
                <div class="crm-card">
                    <h3 class="crm-card-title mb-3">Statistik Pengiriman</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Terkirim</span><span class="text-green-400">{{ $campaign->emailLogs?->where('status','sent')->count() + $campaign->whatsappLogs?->where('status','sent')->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Gagal</span><span class="text-red-400">{{ $campaign->emailLogs?->where('status','failed')->count() + $campaign->whatsappLogs?->where('status','failed')->count() }}</span></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
