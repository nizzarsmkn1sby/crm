@extends('layouts.app')
@section('title', 'Tambah Perusahaan')
@section('breadcrumb')
    <a href="{{ route('companies.index') }}" class="text-gray-400 hover:text-white text-sm">Perusahaan</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Tambah Baru</span>
@endsection

@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <h1 class="page-title mb-6">Tambah Perusahaan Baru</h1>

    <form method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-building text-indigo-400 mr-2"></i>Informasi Perusahaan</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Nama Perusahaan <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="PT. Maju Jaya">
                </div>
                <div class="form-group">
                    <label class="form-label">Industri</label>
                    <select name="industry" class="form-control">
                        <option value="">— Pilih Industri —</option>
                        @foreach(['Teknologi','Manufaktur','Perdagangan','Jasa Keuangan','Kesehatan','Pendidikan','Properti','Media & Hiburan','Transportasi','Pertanian','Energi','Lainnya'] as $ind)
                            <option value="{{ $ind }}" {{ old('industry') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Karyawan</label>
                    <input type="number" name="employees" value="{{ old('employees') }}" class="form-control" placeholder="100" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" value="{{ old('website') }}" class="form-control" placeholder="https://perusahaan.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Annual Revenue (Rp)</label>
                    <input type="number" name="annual_revenue" value="{{ old('annual_revenue') }}" class="form-control" placeholder="1000000000">
                </div>
            </div>
        </div>

        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-telephone text-green-400 mr-2"></i>Kontak Perusahaan</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="021-1234567">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="info@perusahaan.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="form-control" placeholder="Jakarta">
                </div>
                <div class="form-group">
                    <label class="form-label">Negara</label>
                    <input type="text" name="country" value="{{ old('country', 'Indonesia') }}" class="form-control" placeholder="Indonesia">
                </div>
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Jl. Sudirman No.1, Jakarta Pusat">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-journal-text text-yellow-400 mr-2"></i>Catatan</h3>
            <div class="form-group mb-0">
                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tentang perusahaan ini...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('companies.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perusahaan</button>
        </div>
    </form>
</div>
@endsection
