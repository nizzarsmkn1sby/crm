@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('breadcrumb')
    <i class="bi bi-gear text-gray-500"></i>
    <span class="text-gray-300 text-sm font-medium">Pengaturan</span>
@endsection

@section('content')
<div class="fade-in" style="max-width:800px;margin:0 auto;">
    <h1 class="page-title mb-6">Pengaturan Sistem</h1>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        {{-- Company Info --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-buildings text-indigo-400 mr-2"></i>Info Perusahaan</h3>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;" class="mt-4">
                <div class="form-group">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" name="company_name" class="form-control"
                        value="{{ $settings['company_name']->value ?? 'WebCare CRM' }}"
                        placeholder="WebCare CRM">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Perusahaan</label>
                    <input type="email" name="company_email" class="form-control"
                        value="{{ $settings['company_email']->value ?? '' }}"
                        placeholder="info@perusahaan.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="company_phone" class="form-control"
                        value="{{ $settings['company_phone']->value ?? '' }}"
                        placeholder="0811-1234-5678">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="company_address" class="form-control"
                        value="{{ $settings['company_address']->value ?? '' }}"
                        placeholder="Jl. Sudirman No.1, Jakarta">
                </div>
            </div>
        </div>

        {{-- WhatsApp Config --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-whatsapp text-green-400 mr-2"></i>Konfigurasi WhatsApp</h3>
                <span class="badge badge-success text-xs">Aktif</span>
            </div>
            <div class="mt-4">
                <div class="form-group">
                    <label class="form-label">Provider</label>
                    <select name="whatsapp_provider" class="form-control">
                        <option value="fonnte" {{ ($settings['whatsapp_provider']->value ?? 'fonnte') === 'fonnte' ? 'selected' : '' }}>Fonnte</option>
                        <option value="ultramsg" {{ ($settings['whatsapp_provider']->value ?? '') === 'ultramsg' ? 'selected' : '' }}>UltraMsg</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">API Token</label>
                    <input type="password" name="whatsapp_token" class="form-control"
                        placeholder="Biarkan kosong jika tidak ingin mengubah token..."
                        autocomplete="new-password">
                    @if(!empty($settings['whatsapp_token']->value))
                        <p class="text-xs text-green-400 mt-1"><i class="bi bi-check-circle"></i> Token sudah dikonfigurasi</p>
                    @else
                        <p class="text-xs text-yellow-400 mt-1"><i class="bi bi-exclamation-triangle"></i> Belum ada token</p>
                    @endif
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Nomor Pengirim</label>
                    <input type="text" name="whatsapp_sender" class="form-control"
                        value="{{ $settings['whatsapp_sender']->value ?? '' }}"
                        placeholder="628121234567">
                </div>
            </div>
        </div>

        {{-- Email / SMTP Config --}}
        <div class="crm-card mb-4">
            <div class="crm-card-header">
                <h3 class="crm-card-title"><i class="bi bi-envelope text-blue-400 mr-2"></i>Konfigurasi Email (SMTP)</h3>
            </div>
            <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;" class="mt-4">
                <div class="form-group">
                    <label class="form-label">Host SMTP</label>
                    <input type="text" name="mail_host" class="form-control"
                        value="{{ $settings['mail_host']->value ?? env('MAIL_HOST', '') }}"
                        placeholder="smtp.gmail.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Port</label>
                    <input type="number" name="mail_port" class="form-control"
                        value="{{ $settings['mail_port']->value ?? env('MAIL_PORT', '587') }}"
                        placeholder="587">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="email" name="mail_username" class="form-control"
                        value="{{ $settings['mail_username']->value ?? env('MAIL_USERNAME', '') }}"
                        placeholder="email@domain.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Password / App Password</label>
                    <input type="password" name="mail_password" class="form-control"
                        placeholder="Kosongkan jika tidak ingin mengubah..."
                        autocomplete="new-password">
                    @if(!empty($settings['mail_password']->value) || env('MAIL_PASSWORD'))
                        <p class="text-xs text-green-400 mt-1"><i class="bi bi-check-circle"></i> Password sudah dikonfigurasi</p>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">From Address</label>
                    <input type="email" name="mail_from_address" class="form-control"
                        value="{{ $settings['mail_from_address']->value ?? env('MAIL_FROM_ADDRESS', '') }}"
                        placeholder="noreply@perusahaan.com">
                </div>
                <div class="form-group">
                    <label class="form-label">From Name</label>
                    <input type="text" name="mail_from_name" class="form-control"
                        value="{{ $settings['mail_from_name']->value ?? env('MAIL_FROM_NAME', 'WebCare CRM') }}"
                        placeholder="WebCare CRM">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Enkripsi</label>
                    <select name="mail_encryption" class="form-control">
                        <option value="tls" {{ ($settings['mail_encryption']->value ?? env('MAIL_ENCRYPTION', 'tls')) === 'tls' ? 'selected' : '' }}>TLS (Rekomendasi)</option>
                        <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="" {{ ($settings['mail_encryption']->value ?? '') === '' ? 'selected' : '' }}>Tidak ada</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500"><i class="bi bi-info-circle mr-1"></i>Pengaturan disimpan ke database dan file .env secara otomatis.</p>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save mr-1"></i>Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
