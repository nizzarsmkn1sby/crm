@extends('layouts.app')
@section('title', 'Tambah Deal')
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header"><h1 class="page-title">Tambah Deal Baru</h1><a href="{{ route('deals.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a></div>
    <form action="{{ route('deals.store') }}" method="POST">
        @csrf
        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-bag text-indigo-400 mr-2"></i>Informasi Deal</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Judul Deal *</label><input type="text" name="title" class="form-control" required placeholder="Contoh: Implementasi ERP - PT XYZ"></div>
                <div class="form-group"><label class="form-label">Lead Terkait</label>
                    <select name="lead_id" class="form-control"><option value="">-- Pilih Lead --</option>@foreach($leads as $l)<option value="{{ $l->id }}" {{ old('lead_id')==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select>
                </div>
                <div class="form-group"><label class="form-label">Stage Pipeline</label>
                    <select name="pipeline_stage_id" class="form-control">@foreach($stages as $s)<option value="{{ $s->id }}" {{ old('pipeline_stage_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select>
                </div>
                <div class="form-group"><label class="form-label">Nilai Deal (Rp)</label><input type="number" name="value" class="form-control" value="{{ old('value') }}" placeholder="50000000"></div>
                <div class="form-group"><label class="form-label">Probabilitas (%)</label><input type="number" name="probability" class="form-control" value="{{ old('probability',50) }}" min="0" max="100"></div>
                <div class="form-group"><label class="form-label">Ditugaskan ke</label>
                    <select name="assigned_to" class="form-control"><option value="">-- Pilih --</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to',auth()->id())==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select>
                </div>
                <div class="form-group"><label class="form-label">Target Closing</label><input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date') }}" min="{{ now()->toDateString() }}"></div>
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Catatan</label><textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea></div>
            </div>
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('deals.index') }}" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Deal</button></div>
    </form>
</div>
@endsection
