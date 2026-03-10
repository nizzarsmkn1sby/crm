@extends('layouts.app')
@section('title', 'Kontak')
@section('breadcrumb')
    <i class="bi bi-people text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Kontak</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div><h1 class="page-title">Manajemen Kontak</h1><p class="page-subtitle">{{ $contacts->total() }} kontak terdaftar</p></div>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kontak</a>
    </div>
    <form method="GET" action="{{ route('contacts.index') }}">
        <div class="filter-bar">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari nama, email, telepon..." class="filter-input" style="flex:1;min-width:200px;">
            <select name="company_id" class="filter-input">
                <option value="">Semua Perusahaan</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('contacts.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x-circle"></i></a>
        </div>
    </form>
    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead><tr><th>NAMA</th><th>PERUSAHAAN</th><th>KONTAK</th><th>JABATAN</th><th>LEAD TERKAIT</th><th>AKSI</th></tr></thead>
            <tbody>
                @forelse($contacts as $contact)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="{{ $contact->avatar ? asset('storage/'.$contact->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($contact->name).'&background=8b5cf6&color=fff' }}" class="w-9 h-9 rounded-full flex-shrink-0" alt="">
                                <a href="{{ route('contacts.show', $contact) }}" class="font-semibold text-white hover:text-indigo-400">{{ $contact->name }}</a>
                            </div>
                        </td>
                        <td class="text-gray-400">{{ $contact->company?->name ?? $contact->company_name ?? '—' }}</td>
                        <td>
                            @if($contact->email)<div class="text-xs text-gray-400"><i class="bi bi-envelope text-blue-400 mr-1"></i>{{ $contact->email }}</div>@endif
                            @if($contact->phone)<div class="text-xs text-gray-400"><i class="bi bi-telephone text-green-400 mr-1"></i>{{ $contact->phone }}</div>@endif
                        </td>
                        <td class="text-gray-400 text-sm">{{ $contact->position ?? '—' }}</td>
                        <td>{{ $contact->leads_count ?? 0 }} lead</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('contacts.show', $contact) }}" class="btn btn-secondary btn-icon"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary btn-icon"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Hapus kontak?')">@csrf @method('DELETE')<button class="btn btn-danger btn-icon"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-person-x"></i></div><div class="empty-state-title">Belum ada kontak</div><a href="{{ route('contacts.create') }}" class="btn btn-primary mt-2">Tambah Kontak</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $contacts->links() }}</div>
</div>
@endsection
