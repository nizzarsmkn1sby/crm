@extends('layouts.app')
@section('title', 'Tambah Kontak')
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header">
        <h1 class="page-title">Tambah Kontak Baru</h1>
        <a href="{{ route('contacts.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <form action="{{ route('contacts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-person text-indigo-400 mr-2"></i>Informasi Kontak</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                <div class="form-group"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" placeholder="0812xxxxxxxx"></div>
                <div class="form-group"><label class="form-label">WhatsApp</label><input type="text" name="whatsapp" class="form-control" placeholder="0812xxxxxxxx"></div>
                <div class="form-group"><label class="form-label">Jabatan</label><input type="text" name="position" class="form-control"></div>
                <div class="form-group"><label class="form-label">Perusahaan</label>
                    <select name="company_id" class="form-control">
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach($companies as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Foto Profil</label><input type="file" name="avatar" class="form-control" accept="image/*"></div>
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
            </div>
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('contacts.index') }}" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button></div>
    </form>
</div>
@endsection
