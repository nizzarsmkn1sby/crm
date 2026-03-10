@extends('layouts.app')
@section('title', 'Edit Workflow')
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header">
        <h1 class="page-title">Edit Workflow</h1>
        <a href="{{ route('automation.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <form action="{{ route('automation.update', $automation) }}" method="POST">
        @csrf @method('PUT')
        <div class="crm-card">
            <div class="form-group"><label class="form-label">Nama *</label><input type="text" name="name" class="form-control" value="{{ old('name',$automation->name) }}" required></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2">{{ old('description',$automation->description) }}</textarea></div>
            <div class="form-group">
                <label class="form-label">Trigger *</label>
                <select name="trigger" class="form-control" required>
                    @foreach(['lead_created'=>'Lead Baru','lead_status_changed'=>'Status Lead Berubah','deal_won'=>'Deal Won','deal_lost'=>'Deal Lost','task_overdue'=>'Task Terlambat'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('trigger',$automation->trigger)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Aksi (JSON)</label>
                <textarea name="actions" class="form-control" rows="6" required>{{ old('actions', json_encode($automation->actions, JSON_PRETTY_PRINT)) }}</textarea>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ $automation->is_active?'checked':'' }}>
                <label class="form-label mb-0">Aktif</label>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-4">
            <a href="{{ route('automation.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Perbarui</button>
        </div>
    </form>
</div>
@endsection
