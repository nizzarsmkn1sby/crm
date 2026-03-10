@extends('layouts.app')
@section('title', 'Edit Lead — '.$lead->name)
@section('breadcrumb')
    <a href="{{ route('leads.index') }}" class="text-gray-400 hover:text-white text-sm">Lead</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <a href="{{ route('leads.show', $lead) }}" class="text-gray-400 hover:text-white text-sm">{{ $lead->name }}</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Edit</span>
@endsection
@section('content')
<div class="fade-in" style="max-width:800px;margin:0 auto;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Lead</h1>
            <p class="page-subtitle">{{ $lead->name }}</p>
        </div>
        <a href="{{ route('leads.show', $lead) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <form action="{{ route('leads.update', $lead) }}" method="POST">
        @csrf @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="crm-card" style="grid-column:span 2;">
                <h3 class="crm-card-title mb-4"><i class="bi bi-person-circle text-indigo-400 mr-2"></i>Informasi Dasar</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Nama *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $lead->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Perusahaan</label>
                        <input type="text" name="company" class="form-control" value="{{ old('company', $lead->company) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position', $lead->position) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $lead->whatsapp) }}">
                    </div>
                </div>
            </div>
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-tags text-yellow-400 mr-2"></i>Klasifikasi</h3>
                <div class="form-group">
                    <label class="form-label">Sumber *</label>
                    <select name="source" class="form-control" required>
                        @foreach(['website','referral','campaign','whatsapp','email','manual','social','event'] as $src)
                            <option value="{{ $src }}" {{ old('source',$lead->source)===$src?'selected':'' }}>{{ ucfirst($src) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control" required>
                        @foreach(['new'=>'Baru','contacted'=>'Dihubungi','qualified'=>'Qualified','proposal'=>'Proposal','negotiation'=>'Negosiasi','won'=>'Won','lost'=>'Lost'] as $val=>$lab)
                            <option value="{{ $val }}" {{ old('status',$lead->status)===$val?'selected':'' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas *</label>
                    <select name="priority" class="form-control" required>
                        @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High','urgent'=>'Urgent'] as $val=>$lab)
                            <option value="{{ $val }}" {{ old('priority',$lead->priority)===$val?'selected':'' }}>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Stage Pipeline</label>
                    <select name="pipeline_stage_id" class="form-control">
                        <option value="">-- Pilih Stage --</option>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}" {{ old('pipeline_stage_id',$lead->pipeline_stage_id)==$stage->id?'selected':'' }}>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-currency-dollar text-green-400 mr-2"></i>Informasi Deal</h3>
                <div class="form-group">
                    <label class="form-label">Estimasi Nilai (Rp)</label>
                    <input type="number" name="estimated_value" class="form-control" value="{{ old('estimated_value', $lead->estimated_value) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Ditugaskan ke</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- Tidak Ditugaskan --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to',$lead->assigned_to)==$user->id?'selected':'' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Target Closing</label>
                    <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', $lead->expected_close_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags', $lead->tags) }}" placeholder="vip, korporat">
                </div>
            </div>
            <div class="crm-card" style="grid-column:span 2;">
                <h3 class="crm-card-title mb-4"><i class="bi bi-journal-text text-blue-400 mr-2"></i>Catatan</h3>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('leads.show', $lead) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
