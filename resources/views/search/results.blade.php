@extends('layouts.app')
@section('title', 'Hasil Pencarian: ' . $q)
@section('breadcrumb')
    <i class="bi bi-search text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Pencarian: "{{ $q }}"</span>
@endsection

@section('content')
<div class="fade-in">
    <div class="page-header mb-6">
        <div>
            <h1 class="page-title">Hasil Pencarian</h1>
            <p class="page-subtitle">
                @if($q)
                    <span class="text-indigo-400 font-semibold">{{ $total }}</span> hasil untuk "<strong class="text-white">{{ $q }}</strong>"
                @else
                    Masukkan kata kunci untuk mencari
                @endif
            </p>
        </div>
        <form method="GET" action="{{ route('search') }}" class="flex gap-2 items-center">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari lead, kontak, deal, perusahaan..."
                class="filter-input" style="min-width:300px;" autofocus>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Cari</button>
        </form>
    </div>

    @if($q && $total === 0)
        <div class="crm-card text-center py-12">
            <i class="bi bi-search text-5xl text-gray-600 block mb-4"></i>
            <p class="text-lg text-gray-400">Tidak ada hasil untuk "<strong class="text-white">{{ $q }}</strong>"</p>
            <p class="text-sm text-gray-500 mt-2">Coba kata kunci lain seperti nama, email, atau telepon</p>
        </div>
    @endif

    @if($leads->isNotEmpty())
    <div class="crm-card mb-4">
        <div class="crm-card-header">
            <h3 class="crm-card-title"><i class="bi bi-person-plus-fill text-indigo-400 mr-2"></i>Lead ({{ $leads->count() }})</h3>
            <a href="{{ route('leads.index', ['search' => $q]) }}" class="btn btn-secondary btn-sm">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-800/50">
            @foreach($leads as $lead)
            <a href="{{ route('leads.show', $lead) }}" class="flex items-center gap-4 py-3 px-2 hover:bg-white/5 rounded-lg transition-all">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" style="background:rgba(99,102,241,0.15);color:#a5b4fc;">
                    {{ strtoupper(substr($lead->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm truncate">{{ $lead->name }}</p>
                    <p class="text-xs text-gray-400">{{ $lead->company }} {{ $lead->email ? '· ' . $lead->email : '' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge badge-{{ $lead->status }} text-xs">{{ ucfirst($lead->status) }}</span>
                    @if($lead->estimated_value)
                        <span class="text-sm font-bold" style="color:#6ee7b7">Rp{{ number_format($lead->estimated_value/1000000,1) }}M</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($contacts->isNotEmpty())
    <div class="crm-card mb-4">
        <div class="crm-card-header">
            <h3 class="crm-card-title"><i class="bi bi-people-fill text-blue-400 mr-2"></i>Kontak ({{ $contacts->count() }})</h3>
            <a href="{{ route('contacts.index', ['search' => $q]) }}" class="btn btn-secondary btn-sm">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-800/50">
            @foreach($contacts as $contact)
            <a href="{{ route('contacts.show', $contact) }}" class="flex items-center gap-4 py-3 px-2 hover:bg-white/5 rounded-lg transition-all">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" style="background:rgba(59,130,246,0.15);color:#93c5fd;">
                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm truncate">{{ $contact->name }}</p>
                    <p class="text-xs text-gray-400">{{ $contact->email }} {{ $contact->phone ? '· ' . $contact->phone : '' }}</p>
                </div>
                @if($contact->lead)
                    <span class="text-xs text-gray-500">Lead: {{ $contact->lead->name }}</span>
                @endif
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($deals->isNotEmpty())
    <div class="crm-card mb-4">
        <div class="crm-card-header">
            <h3 class="crm-card-title"><i class="bi bi-bag-check-fill text-green-400 mr-2"></i>Deal ({{ $deals->count() }})</h3>
        </div>
        <div class="divide-y divide-gray-800/50">
            @foreach($deals as $deal)
            <a href="{{ route('deals.show', $deal) }}" class="flex items-center gap-4 py-3 px-2 hover:bg-white/5 rounded-lg transition-all">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" style="background:rgba(16,185,129,0.15);color:#6ee7b7;">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm truncate">{{ $deal->title }}</p>
                    <p class="text-xs text-gray-400">{{ $deal->lead?->name }}</p>
                </div>
                <div class="text-right">
                    <span class="font-bold text-emerald-400 text-sm">Rp{{ number_format($deal->value/1000000,1) }}M</span>
                    <span class="badge badge-{{ $deal->status }} text-xs block mt-1">{{ ucfirst($deal->status) }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($companies->isNotEmpty())
    <div class="crm-card mb-4">
        <div class="crm-card-header">
            <h3 class="crm-card-title"><i class="bi bi-building text-purple-400 mr-2"></i>Perusahaan ({{ $companies->count() }})</h3>
        </div>
        <div class="divide-y divide-gray-800/50">
            @foreach($companies as $company)
            <a href="{{ route('companies.show', $company) }}" class="flex items-center gap-4 py-3 px-2 hover:bg-white/5 rounded-lg transition-all">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0" style="background:rgba(139,92,246,0.15);color:#c4b5fd;">
                    <i class="bi bi-building"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-white text-sm truncate">{{ $company->name }}</p>
                    <p class="text-xs text-gray-400">{{ $company->industry }} {{ $company->website ? '· ' . $company->website : '' }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
