@extends('layouts.app')
@section('title', 'Edit: ' . $company->name)
@section('breadcrumb')
    <a href="{{ route('companies.index') }}" class="text-gray-400 hover:text-white text-sm">Perusahaan</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <a href="{{ route('companies.show', $company) }}" class="text-gray-400 hover:text-white text-sm">{{ $company->name }}</a>
    <i class="bi bi-chevron-right text-gray-600 text-xs"></i>
    <span class="text-gray-300 text-sm font-medium">Edit</span>
@endsection

@section('content')
<div class="fade-in" style="max-width:700px;margin:0 auto;">
    <h1 class="page-title mb-6">Edit Perusahaan</h1>

    <form method="POST" action="{{ route('companies.update', $company) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-building text-indigo-400 mr-2"></i>Informasi Perusahaan</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Nama Perusahaan <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Industri</label>
                    <select name="industry" class="form-control">
                        <option value="">— Pilih Industri —</option>
                        @foreach(['Teknologi','Manufaktur','Perdagangan','Jasa Keuangan','Kesehatan','Pendidikan','Properti','Media & Hiburan','Transportasi','Pertanian','Energi','Lainnya'] as $ind)
                            <option value="{{ $ind }}" {{ old('industry', $company->industry) === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Jumlah Karyawan</label>
                    <input type="number" name="employees" value="{{ old('employees', $company->employees) }}" class="form-control" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" value="{{ old('website', $company->website) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Annual Revenue (Rp)</label>
                    <input type="number" name="annual_revenue" value="{{ old('annual_revenue', $company->annual_revenue) }}" class="form-control">
                </div>
            </div>
        </div>

        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-telephone text-green-400 mr-2"></i>Kontak Perusahaan</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" value="{{ old('city', $company->city) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Negara</label>
                    <input type="text" name="country" value="{{ old('country', $company->country) }}" class="form-control">
                </div>
                <div class="form-group" style="grid-column:span 2;">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $company->address) }}</textarea>
                </div>
            </div>
        </div>

        <div class="crm-card mb-4">
            <h3 class="crm-card-title mb-4"><i class="bi bi-journal-text text-yellow-400 mr-2"></i>Catatan</h3>
            <div class="form-group mb-0">
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $company->notes) }}</textarea>
            </div>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
