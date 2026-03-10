@extends('layouts.app')
@section('title', 'Manajemen Perusahaan')
@section('breadcrumb')
    <i class="bi bi-building text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Perusahaan</span>
@endsection

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">Perusahaan</h1>
            <p class="page-subtitle">{{ $companies->total() }} perusahaan terdaftar</p>
        </div>
        <a href="{{ route('companies.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Perusahaan
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('companies.index') }}" class="mb-4">
        <div class="filter-bar">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="🔍 Cari nama atau industri..." class="filter-input w-full">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Cari</button>
            <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x"></i> Reset</a>
        </div>
    </form>

    {{-- Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @forelse($companies as $company)
            <div class="crm-card hover:border-indigo-500/40 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                        class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                    <div class="min-w-0">
                        <a href="{{ route('companies.show', $company) }}"
                            class="font-bold text-white hover:text-indigo-400 transition-colors truncate block">
                            {{ $company->name }}
                        </a>
                        @if($company->industry)
                            <span class="text-xs text-gray-400">{{ $company->industry }}</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-1 mb-3 text-sm">
                    @if($company->website)
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="bi bi-globe text-blue-400 w-4"></i>
                            <a href="{{ $company->website }}" target="_blank"
                                class="hover:text-blue-400 truncate text-xs">{{ $company->website }}</a>
                        </div>
                    @endif
                    @if($company->phone)
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="bi bi-telephone text-green-400 w-4"></i>
                            <span class="text-xs">{{ $company->phone }}</span>
                        </div>
                    @endif
                    @if($company->city)
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="bi bi-geo-alt text-orange-400 w-4"></i>
                            <span class="text-xs">{{ $company->city }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 text-xs text-gray-500 mb-3 border-t border-gray-800/50 pt-3">
                    <span><i class="bi bi-people mr-1 text-indigo-400"></i>{{ $company->contacts_count }} kontak</span>
                    <span><i class="bi bi-bag mr-1 text-green-400"></i>{{ $company->deals_count }} deal</span>
                    @if($company->employees)
                        <span><i class="bi bi-person-workspace mr-1 text-blue-400"></i>{{ number_format($company->employees) }} kary.</span>
                    @endif
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary btn-sm flex-1 text-center">
                        <i class="bi bi-eye"></i> Detail
                    </a>
                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-secondary btn-icon" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('companies.destroy', $company) }}" method="POST"
                        onsubmit="return confirm('Hapus perusahaan {{ addslashes($company->name) }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-icon" title="Hapus"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        @empty
            <div class="crm-card text-center py-12 col-span-full">
                <div class="empty-state-icon"><i class="bi bi-building-x"></i></div>
                <div class="empty-state-title">Belum ada perusahaan</div>
                <div class="empty-state-desc">
                    <a href="{{ route('companies.create') }}" class="text-indigo-400">Tambah perusahaan pertama</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $companies->links() }}</div>
</div>
@endsection
