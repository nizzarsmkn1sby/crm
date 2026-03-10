@extends('layouts.app')
@section('title', $contact->name)
@section('content')
<div class="fade-in" style="max-width:900px;margin:0 auto;">
    <div class="page-header">
        <div class="flex items-center gap-4">
            <img src="{{ $contact->avatar ? asset('storage/'.$contact->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($contact->name).'&background=8b5cf6&color=fff&size=80' }}" class="w-16 h-16 rounded-2xl flex-shrink-0" alt="">
            <div>
                <h1 class="page-title">{{ $contact->name }}</h1>
                @if($contact->position || $contact->company?->name)
                    <p class="text-gray-400 text-sm mt-1">{{ $contact->position }} @if($contact->position && $contact->company?->name)—@endif {{ $contact->company?->name }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Hapus kontak?')">@csrf @method('DELETE')<button class="btn btn-danger"><i class="bi bi-trash"></i></button></form>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">
        <div>
            <div class="crm-card mb-4">
                <h3 class="crm-card-title mb-4"><i class="bi bi-person-lines-fill text-indigo-400 mr-2"></i>Info Kontak</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    @if($contact->email)<div><div class="text-xs text-gray-500">Email</div><a href="mailto:{{ $contact->email }}" class="text-sm text-blue-400">{{ $contact->email }}</a></div>@endif
                    @if($contact->phone)<div><div class="text-xs text-gray-500">Telepon</div><span class="text-sm text-gray-300">{{ $contact->phone }}</span></div>@endif
                    @if($contact->whatsapp)<div><div class="text-xs text-gray-500">WhatsApp</div><a href="https://wa.me/{{ preg_replace('/[^0-9]/','',$contact->whatsapp) }}" target="_blank" class="text-sm text-green-400">{{ $contact->whatsapp }}</a></div>@endif
                </div>
                @if($contact->notes)<div class="mt-4 pt-4 border-t border-gray-800"><div class="text-xs text-gray-500 mb-1">Catatan</div><p class="text-sm text-gray-300">{{ $contact->notes }}</p></div>@endif
            </div>
        </div>
        <div>
            <div class="crm-card">
                <h3 class="crm-card-title mb-3"><i class="bi bi-person-plus text-purple-400 mr-2"></i>Lead Terkait</h3>
                @forelse($contact->leads ?? [] as $lead)
                    <a href="{{ route('leads.show', $lead) }}" class="flex items-center gap-2 py-2 border-b border-gray-800/50 last:border-0 hover:bg-white/5 rounded -mx-2 px-2">
                        <div class="flex-1"><div class="text-sm font-semibold text-white">{{ $lead->name }}</div></div>
                        <span class="badge badge-{{ $lead->status }}" style="font-size:10px;">{{ $lead->status }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">Belum ada lead terkait</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
