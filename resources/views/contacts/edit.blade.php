@extends('layouts.app')
@section('title', 'Edit Kontak')
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header">
        <h1 class="page-title">Edit Kontak</h1>
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <form action="{{ route('contacts.update', $contact) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="crm-card mb-4">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="name" class="form-control" value="{{ old('name',$contact->name) }}" required></div>
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email',$contact->email) }}"></div>
                <div class="form-group"><label class="form-label">Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone',$contact->phone) }}"></div>
                <div class="form-group"><label class="form-label">WhatsApp</label><input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp',$contact->whatsapp) }}"></div>
                <div class="form-group"><label class="form-label">Jabatan</label><input type="text" name="position" class="form-control" value="{{ old('position',$contact->position) }}"></div>
                <div class="form-group"><label class="form-label">Perusahaan</label>
                    <select name="company_id" class="form-control">
                        <option value="">-- Pilih --</option>
                        @foreach($companies as $c)<option value="{{ $c->id }}" {{ old('company_id',$contact->company_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Ganti Foto (opsional)</label><input type="file" name="avatar" class="form-control" accept="image/*"></div>
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3">{{ old('notes',$contact->notes) }}</textarea></div>
            </div>
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('contacts.show', $contact) }}" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button></div>
    </form>
</div>
@endsection
