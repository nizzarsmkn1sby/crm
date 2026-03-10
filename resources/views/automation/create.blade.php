@extends('layouts.app')
@section('title', 'Buat Workflow')
@section('breadcrumb')
    <a href="{{ route('automation.index') }}" class="text-gray-400 text-sm">Otomasi</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm">Buat Workflow</span>
@endsection
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header">
        <h1 class="page-title">Buat Workflow Baru</h1>
        <a href="{{ route('automation.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <form action="{{ route('automation.store') }}" method="POST">
        @csrf
        <div class="flex flex-col gap-4">
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-lightning text-indigo-400 mr-2"></i>Detail Workflow</h3>
                <div class="form-group"><label class="form-label">Nama Workflow *</label><input type="text" name="name" class="form-control" required placeholder="Contoh: Auto WA saat lead baru"></div>
                <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2" placeholder="Jelaskan tujuan workflow ini..."></textarea></div>
                <div class="form-group">
                    <label class="form-label">Trigger *</label>
                    <select name="trigger" class="form-control" required>
                        <option value="lead_created">Lead Baru Masuk</option>
                        <option value="lead_status_changed">Status Lead Berubah</option>
                        <option value="deal_won">Deal Berhasil (Won)</option>
                        <option value="deal_lost">Deal Gagal (Lost)</option>
                        <option value="task_overdue">Task Terlambat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Aksi (JSON)</label>
                    <textarea name="actions" class="form-control" rows="6" required placeholder='[{"type":"send_whatsapp","message":"Halo {name}, terima kasih telah menghubungi kami!"}]'>[{"type": "send_whatsapp", "message": "Halo {name}, terima kasih sudah menghubungi kami. Tim kami akan segera menghubungi Anda."}]</textarea>
                    <p class="text-xs text-gray-500 mt-1">Format JSON: tipe aksi yang didukung: send_whatsapp, send_email, create_task</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="isActive" value="1" checked class="w-4 h-4">
                    <label for="isActive" class="form-label mb-0">Aktifkan workflow ini</label>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('automation.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Workflow</button>
            </div>
        </div>
    </form>
</div>
@endsection
