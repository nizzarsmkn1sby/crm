@extends('layouts.app')
@section('title', 'Tambah Lead Baru')
@section('breadcrumb')
    <a href="{{ route('leads.index') }}" class="text-gray-400 hover:text-white text-sm">Lead</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Tambah Lead</span>
@endsection
@section('content')
<div class="fade-in" style="max-width:800px;margin:0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah Lead Baru</h1>
            <p class="page-subtitle">Masukkan informasi lead yang baru masuk</p>
        </div>
        <a href="{{ route('leads.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('leads.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            {{-- Info Dasar --}}
            <div class="crm-card" style="grid-column:span 2;">
                <h3 class="crm-card-title mb-4"><i class="bi bi-person-circle text-indigo-400 mr-2"></i>Informasi Dasar</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Ahmad Fauzi" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="Contoh: PT Maju Bersama">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="Contoh: Direktur, Manager IT">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="0812xxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" placeholder="0812xxxxxxxx">
                    </div>
                </div>
            </div>

            {{-- Klasifikasi --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-tags text-yellow-400 mr-2"></i>Klasifikasi</h3>
                <div class="form-group">
                    <label class="form-label">Sumber Lead *</label>
                    <select name="source" class="form-control" required>
                        <option value="">-- Pilih Sumber --</option>
                        @foreach(['website'=>'Website','referral'=>'Referral','campaign'=>'Campaign','whatsapp'=>'WhatsApp','email'=>'Email','manual'=>'Input Manual','social'=>'Social Media','event'=>'Event/Pameran'] as $val => $lab)
                            <option value="{{ $val }}" {{ old('source')===$val?'selected':'' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['new'=>'Baru','contacted'=>'Sudah Dihubungi','qualified'=>'Qualified','proposal'=>'Proposal','negotiation'=>'Negosiasi','won'=>'Won','lost'=>'Lost'] as $val => $lab)
                            <option value="{{ $val }}" {{ old('status','new')===$val?'selected':'' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas *</label>
                    <select name="priority" class="form-control" required>
                        <option value="low" {{ old('priority')==='low'?'selected':'' }}>🟢 Low</option>
                        <option value="medium" {{ old('priority','medium')==='medium'?'selected':'' }}>🟡 Medium</option>
                        <option value="high" {{ old('priority')==='high'?'selected':'' }}>🟠 High</option>
                        <option value="urgent" {{ old('priority')==='urgent'?'selected':'' }}>🔴 Urgent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Stage Pipeline</label>
                    <select name="pipeline_stage_id" class="form-control">
                        <option value="">-- Pilih Stage --</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" {{ old('pipeline_stage_id')==$stage->id?'selected':'' }}>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Deal Info --}}
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-currency-dollar text-green-400 mr-2"></i>Informasi Deal</h3>
                <div class="form-group">
                    <label class="form-label">Estimasi Nilai (Rp)</label>
                    <input type="number" name="estimated_value" class="form-control" value="{{ old('estimated_value') }}" placeholder="Contoh: 50000000">
                </div>
                <div class="form-group">
                    <label class="form-label">Ditugaskan ke</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- Pilih Salesperson --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to')==$user->id?'selected':'' }}>{{ $user->name }} ({{ $user->role_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Target Closing</label>
                    <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date') }}" min="{{ now()->toDateString() }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="Pisahkan dengan koma: vip, korporat">
                </div>
            </div>

            {{-- Notes --}}
            <div class="crm-card" style="grid-column:span 2;">
                <h3 class="crm-card-title mb-4"><i class="bi bi-journal-text text-blue-400 mr-2"></i>Catatan</h3>
                <div class="form-group mb-0">
                    <textarea name="notes" class="form-control" rows="4" placeholder="Tambahkan catatan tentang lead ini...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Lead</button>
        </div>
    </form>
</div>
@endsection
