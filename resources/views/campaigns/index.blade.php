@extends('layouts.app')
@section('title', 'Campaign Marketing')
@section('breadcrumb')
    <i class="bi bi-megaphone text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Kampanye</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div><h1 class="page-title">Kampanye Marketing</h1><p class="page-subtitle">{{ $campaigns->total() }} kampanye</p></div>
        <a href="{{ route('campaigns.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Kampanye</a>
    </div>
    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead><tr><th>NAMA</th><th>TIPE</th><th>STATUS</th><th>PENERIMA</th><th>DIBUAT</th><th>AKSI</th></tr></thead>
            <tbody>
                @forelse($campaigns as $c)
                    <tr>
                        <td><a href="{{ route('campaigns.show', $c) }}" class="font-semibold text-white hover:text-indigo-400">{{ $c->name }}</a></td>
                        <td><span class="badge {{ $c->type==='email'?'badge-new':'badge-won' }}"><i class="bi bi-{{ $c->type==='email'?'envelope':'whatsapp' }}"></i> {{ strtoupper($c->type) }}</span></td>
                        <td><span class="badge badge-{{ ['draft'=>'pending','scheduled'=>'contacted','running'=>'negotiation','completed'=>'won','failed'=>'lost'][$c->status]??'pending' }}">{{ ucfirst($c->status) }}</span></td>
                        <td>{{ $c->total_recipients ?? '—' }}</td>
                        <td class="text-gray-400 text-sm">{{ $c->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('campaigns.show', $c) }}" class="btn btn-secondary btn-icon"><i class="bi bi-eye"></i></a>
                                @if(in_array($c->status,['draft','scheduled']))
                                    <form action="{{ route('campaigns.send', $c) }}" method="POST">@csrf<button class="btn btn-success btn-icon" title="Kirim Sekarang"><i class="bi bi-send"></i></button></form>
                                @endif
                                <form action="{{ route('campaigns.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus kampanye ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-icon"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-megaphone"></i></div><div class="empty-state-title">Belum ada kampanye</div><a href="{{ route('campaigns.create') }}" class="btn btn-primary mt-2">Buat Kampanye Pertama</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $campaigns->links() }}</div>
</div>
@endsection
