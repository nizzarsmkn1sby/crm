@extends('layouts.app')
@section('title', 'Buat Kampanye')
@section('breadcrumb')
    <a href="{{ route('campaigns.index') }}" class="text-gray-400 hover:text-white text-sm">Kampanye</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Buat Baru</span>
@endsection
@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <div class="page-header">
        <h1 class="page-title">Buat Kampanye Baru</h1>
        <a href="{{ route('campaigns.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <form action="{{ route('campaigns.store') }}" method="POST">
        @csrf
        <div class="flex flex-col gap-4">
            <div class="crm-card">
                <h3 class="crm-card-title mb-4"><i class="bi bi-megaphone text-indigo-400 mr-2"></i>Informasi Kampanye</h3>
                <div class="form-group"><label class="form-label">Nama Kampanye *</label><input type="text" name="name" class="form-control" required placeholder="Contoh: Promo Akhir Tahun 2025"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Tipe Kampanye *</label>
                        <select name="type" class="form-control" required id="campaignType">
                            <option value="email">✉️ Email</option>
                            <option value="whatsapp">💬 WhatsApp</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jadwalkan (opsional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control">
                    </div>
                </div>
                <div class="form-group" id="subjectGroup">
                    <label class="form-label">Subjek Email *</label>
                    <input type="text" name="subject" class="form-control" placeholder="Subjek email...">
                </div>
                <div class="form-group">
                    <label class="form-label">Konten / Pesan *</label>
                    <textarea name="content" class="form-control" rows="8" required placeholder="Tulis pesan kampanye...&#10;&#10;Variabel: {name}, {company}, {phone}"></textarea>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Deskripsi (Internal)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan internal kampanye..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Kampanye</button>
            </div>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('campaignType').addEventListener('change', function() {
    document.getElementById('subjectGroup').style.display = this.value === 'email' ? '' : 'none';
});
</script>
@endpush
