@extends('layouts.app')
@section('title', 'Edit Deal')
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header"><h1 class="page-title">Edit Deal</h1><a href="{{ route('deals.show', $deal) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a></div>
    <form action="{{ route('deals.update', $deal) }}" method="POST">
        @csrf @method('PUT')
        <div class="crm-card mb-4">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Judul Deal *</label><input type="text" name="title" class="form-control" value="{{ old('title',$deal->title) }}" required></div>
                <div class="form-group"><label class="form-label">Lead</label>
                    <select name="lead_id" class="form-control"><option value="">—</option>@foreach($leads as $l)<option value="{{ $l->id }}" {{ old('lead_id',$deal->lead_id)==$l->id?'selected':'' }}>{{ $l->name }}</option>@endforeach</select>
                </div>
                <div class="form-group"><label class="form-label">Stage</label>
                    <select name="pipeline_stage_id" class="form-control">@foreach($stages as $s)<option value="{{ $s->id }}" {{ old('pipeline_stage_id',$deal->pipeline_stage_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select>
                </div>
                <div class="form-group"><label class="form-label">Nilai (Rp)</label><input type="number" name="value" class="form-control" value="{{ old('value',$deal->value) }}"></div>
                <div class="form-group"><label class="form-label">Probabilitas (%)</label><input type="number" name="probability" class="form-control" value="{{ old('probability',$deal->probability) }}" min="0" max="100"></div>
                <div class="form-group"><label class="form-label">Status</label>
                    <select name="status" class="form-control">@foreach(['open'=>'Open','won'=>'Won','lost'=>'Lost'] as $v=>$l)<option value="{{ $v }}" {{ old('status',$deal->status)===$v?'selected':'' }}>{{ $l }}</option>@endforeach</select>
                </div>
                <div class="form-group"><label class="form-label">Target Closing</label><input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date',$deal->expected_close_date) }}"></div>
                <div class="form-group"><label class="form-label">Ditugaskan ke</label>
                    <select name="assigned_to" class="form-control"><option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to',$deal->assigned_to)==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select>
                </div>
                <div class="form-group" style="grid-column:span 2;"><label class="form-label">Catatan</label><textarea name="description" class="form-control" rows="3">{{ old('description',$deal->description) }}</textarea></div>
            </div>
        </div>
        <div class="flex justify-end gap-3"><a href="{{ route('deals.show', $deal) }}" class="btn btn-secondary">Batal</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button></div>
    </form>
</div>
@endsection
