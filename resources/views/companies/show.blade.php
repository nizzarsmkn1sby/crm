@extends('layouts.app')
@section('title', $company->name)
@section('breadcrumb')
    <a href="{{ route('companies.index') }}" class="text-gray-400 hover:text-white text-sm">Perusahaan</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">{{ $company->name }}</span>
@endsection

@section('content')
<div class="fade-in">
    {{-- Header --}}
    <div class="crm-card mb-6" style="background:linear-gradient(135deg,rgba(14,165,233,0.12),rgba(99,102,241,0.08));border-color:rgba(14,165,233,0.25);">
        <div class="flex items-center gap-5">
            <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                class="w-20 h-20 rounded-2xl object-cover flex-shrink-0 border border-white/10">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-white">{{ $company->name }}</h1>
                @if($company->industry)
                    <span class="badge badge-info mt-1">{{ $company->industry }}</span>
                @endif
                <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-400">
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" class="flex items-center gap-1 hover:text-blue-400">
                            <i class="bi bi-globe text-blue-400"></i> {{ $company->website }}
                        </a>
                    @endif
                    @if($company->phone)
                        <span class="flex items-center gap-1"><i class="bi bi-telephone text-green-400"></i> {{ $company->phone }}</span>
                    @endif
                    @if($company->email)
                        <a href="mailto:{{ $company->email }}" class="flex items-center gap-1 hover:text-blue-400">
                            <i class="bi bi-envelope text-blue-400"></i> {{ $company->email }}
                        </a>
                    @endif
                    @if($company->city)
                        <span class="flex items-center gap-1"><i class="bi bi-geo-alt text-orange-400"></i> {{ $company->city }}{{ $company->country ? ', ' . $company->country : '' }}</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('companies.edit', $company) }}" class="btn btn-secondary"><i class="bi bi-pencil"></i> Edit</a>
                <form action="{{ route('companies.destroy', $company) }}" method="POST"
                    onsubmit="return confirm('Yakin hapus perusahaan {{ addslashes($company->name) }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="stat-value">{{ $company->contacts->count() }}</div>
                <div class="stat-label">Kontak</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="bi bi-bag-check-fill"></i></div>
            <div>
                <div class="stat-value">{{ $company->deals->count() }}</div>
                <div class="stat-label">Deal</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-purple"><i class="bi bi-person-workspace"></i></div>
            <div>
                <div class="stat-value">{{ $company->employees ? number_format($company->employees) : '—' }}</div>
                <div class="stat-label">Karyawan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-value">{{ $company->annual_revenue ? 'Rp' . number_format($company->annual_revenue/1000000,0) . 'M' : '—' }}</div>
                <div class="stat-label">Annual Revenue</div>
            </div>
        </div>
    </div>

    <div class="grid-cols-2">
        {{-- Contacts --}}
        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-people-fill text-blue-400 mr-2"></i>Kontak ({{ $company->contacts->count() }})</h3>
                <a href="{{ route('contacts.index', ['company_id' => $company->id]) }}" class="btn btn-secondary btn-sm">Lihat semua</a>
            </div>
            @forelse($company->contacts->take(5) as $contact)
                <div class="flex items-center gap-3 py-3 border-b border-gray-800/50 last:border-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                        style="background:rgba(59,130,246,0.15);color:#93c5fd;">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('contacts.show', $contact) }}" class="text-sm font-semibold text-white hover:text-indigo-400">
                            {{ $contact->name }}
                        </a>
                        @if($contact->position)
                            <div class="text-xs text-gray-400">{{ $contact->position }}</div>
                        @endif
                    </div>
                    @if($contact->email)
                        <a href="mailto:{{ $contact->email }}" class="text-xs text-gray-500 hover:text-blue-400">
                            <i class="bi bi-envelope"></i>
                        </a>
                    @endif
                </div>
            @empty
                <div class="text-center py-6 text-gray-500"><i class="bi bi-people block text-3xl mb-2 opacity-30"></i><p class="text-sm">Belum ada kontak</p></div>
            @endforelse
        </div>

        {{-- Deals --}}
        <div class="crm-card">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-bag-check-fill text-green-400 mr-2"></i>Deal ({{ $company->deals->count() }})</h3>
            </div>
            @forelse($company->deals->take(5) as $deal)
                <div class="flex items-center gap-3 py-3 border-b border-gray-800/50 last:border-0">
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('deals.show', $deal) }}" class="text-sm font-semibold text-white hover:text-indigo-400">
                            {{ $deal->title }}
                        </a>
                        @if($deal->pipelineStage)
                            <div class="text-xs text-gray-400">{{ $deal->pipelineStage->name }}</div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold" style="color:#6ee7b7">Rp{{ number_format($deal->value/1000000,1) }}M</div>
                        <span class="badge badge-{{ $deal->status }}" style="font-size:10px;">{{ ucfirst($deal->status) }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-gray-500"><i class="bi bi-bag block text-3xl mb-2 opacity-30"></i><p class="text-sm">Belum ada deal</p></div>
            @endforelse
        </div>
    </div>

    @if($company->notes)
    <div class="crm-card mt-4">
        <h3 class="crm-card-title mb-3"><i class="bi bi-journal-text text-yellow-400 mr-2"></i>Catatan</h3>
        <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $company->notes }}</p>
    </div>
    @endif
</div>
@endsection
