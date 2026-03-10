@extends('layouts.app')
@section('title', 'Dokumen')
@section('breadcrumb')
    <i class="bi bi-folder text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Dokumen</span>
@endsection
@section('content')
<div class="fade-in">
    <div class="page-header">
        <div><h1 class="page-title">Manajemen Dokumen</h1><p class="page-subtitle">{{ $documents->total() }} dokumen tersimpan</p></div>
    </div>
    {{-- Upload Form --}}
    <div class="crm-card mb-4">
        <h3 class="crm-card-title mb-4"><i class="bi bi-cloud-upload text-indigo-400 mr-2"></i>Upload Dokumen</h3>
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:12px;align-items:end;">
                <div class="form-group mb-0"><label class="form-label">File *</label><input type="file" name="file" class="form-control" required></div>
                <div class="form-group mb-0"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" placeholder="Nama dokumen..."></div>
                <div class="form-group mb-0"><label class="form-label">Kategori</label>
                    <select name="category" class="form-control">
                        <option value="proposal">Proposal</option>
                        <option value="contract">Kontrak</option>
                        <option value="invoice">Invoice</option>
                        <option value="presentation">Presentasi</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>
                <div class="form-group mb-0"><label class="form-label">Catatan</label><input type="text" name="notes" class="form-control" placeholder="Catatan..."></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-bar mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari dokumen..." class="filter-input" style="flex:1;">
        <select name="category" class="filter-input">
            <option value="">Semua Kategori</option>
            @foreach(['proposal','contract','invoice','presentation','other'] as $cat)
                <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
        <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-x"></i></a>
    </form>

    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead><tr><th>NAMA</th><th>KATEGORI</th><th>UKURAN</th><th>TERKAIT</th><th>DIUPLOAD OLEH</th><th>TANGGAL</th><th>AKSI</th></tr></thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <i class="bi {{ $doc->file_icon }} text-2xl flex-shrink-0"></i>
                                <div>
                                    <div class="font-semibold text-sm text-white">{{ $doc->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $doc->category }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-pending">{{ ucfirst($doc->category) }}</span></td>
                        <td class="text-gray-400 text-sm">{{ $doc->file_size_formatted }}</td>
                        <td class="text-gray-400 text-sm">
                            @if($doc->lead)<a href="{{ route('leads.show', $doc->lead) }}" class="text-indigo-400 hover:text-indigo-300 text-xs">{{ $doc->lead->name }}</a>@endif
                            @if($doc->deal)<a href="{{ route('deals.show', $doc->deal) }}" class="text-indigo-400 hover:text-indigo-300 text-xs">{{ $doc->deal->title }}</a>@endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <img src="{{ $doc->uploader?->avatar_url }}" class="w-6 h-6 rounded-full" alt="">
                                <span class="text-xs text-gray-400">{{ $doc->uploader?->name }}</span>
                            </div>
                        </td>
                        <td class="text-gray-400 text-sm">{{ $doc->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('documents.download', $doc) }}" class="btn btn-secondary btn-icon"><i class="bi bi-download"></i></a>
                                <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-icon"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-state-icon"><i class="bi bi-folder-x"></i></div><div class="empty-state-title">Belum ada dokumen</div></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $documents->links() }}</div>
</div>
@endsection
