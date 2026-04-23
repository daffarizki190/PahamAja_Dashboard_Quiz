@extends('layouts.app')

@section('title', 'Pengaturan Sistem – PahamAja')

@section('head_extra')
<style>
    .settings-card {
        background: #fff; border: 1px solid #E8E6F0; border-radius: 20px;
        margin-bottom: 24px; overflow: hidden;
    }
    .settings-header {
        padding: 20px 24px; border-bottom: 1px solid #E8E6F0;
        background: #F9F8FD; display: flex; align-items: center; gap: 12px;
    }
    .settings-body { padding: 24px; }
    .settings-icon {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
</style>
@endsection

@section('content')
<div class="fade-up">
    <div style="margin-bottom:32px;">
        <h1 style="font-size:28px; font-weight:900; color:#1E1B4B; margin:0;">Pengaturan Sistem</h1>
        <p style="font-size:14px; color:#6B7280; font-weight:500; margin-top:4px;">Kelola konfigurasi platform dan integrasi AI.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <!-- AI Configuration -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="settings-icon" style="background:rgba(124,58,237,0.1); color:#7C3AED;">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <h3 style="font-size:16px; font-weight:800; color:#1E1B4B; margin:0;">Konfigurasi AI (Gemini)</h3>
            </div>
            <div class="settings-body">
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Gemini API Key</label>
                        <input type="password" name="gemini_api_key" value="{{ $settings->get('ai')?->firstWhere('key', 'gemini_api_key')?->value }}" class="form-input" placeholder="Masukkan API Key Anda...">
                        <p style="font-size:12px; color:#9CA3AF; margin-top:6px;">Kunci ini digunakan untuk fitur pembuatan kuis otomatis dan insight AI.</p>
                    </div>
                    <div>
                        <label class="form-label">Model AI Utama</label>
                        <select name="gemini_model" class="form-input">
                            @php $currentModel = $settings->get('ai')?->firstWhere('key', 'gemini_model')?->value ?? 'gemini-1.5-flash'; @endphp
                            <option value="gemini-1.5-flash" {{ $currentModel == 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash (Cepat & Hemat)</option>
                            <option value="gemini-1.5-pro" {{ $currentModel == 'gemini-1.5-pro' ? 'selected' : '' }}>Gemini 1.5 Pro (Sangat Cerdas)</option>
                            <option value="gemini-2.0-flash-exp" {{ $currentModel == 'gemini-2.0-flash-exp' ? 'selected' : '' }}>Gemini 2.0 Flash (Experimental)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Configuration -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="settings-icon" style="background:rgba(16,185,129,0.1); color:#10B981;">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <h3 style="font-size:16px; font-weight:800; color:#1E1B4B; margin:0;">Standar Sistem</h3>
            </div>
            <div class="settings-body">
                <div class="space-y-4">
                    <div style="max-width:300px;">
                        <label class="form-label">Nilai Kelulusan Default</label>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="number" name="default_passing_score" value="{{ $settings->get('system')?->firstWhere('key', 'default_passing_score')?->value ?? 70 }}" class="form-input" min="0" max="100">
                            <span style="font-weight:700; color:#6B7280;">%</span>
                        </div>
                        <p style="font-size:12px; color:#9CA3AF; margin-top:6px;">Nilai standar yang otomatis terisi saat membuat kuis baru.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Backup -->
        <div class="settings-card">
            <div class="settings-header">
                <div class="settings-icon" style="background:rgba(245,158,11,0.1); color:#D97706;">
                    <i class="fa-solid fa-database"></i>
                </div>
                <h3 style="font-size:16px; font-weight:800; color:#1E1B4B; margin:0;">Pencadangan Data</h3>
            </div>
            <div class="settings-body">
                <div style="display:flex; align-items:flex-start; gap:20px; flex-wrap:wrap;">
                    <div style="flex:1; min-width:300px;">
                        <p style="font-size:13px; color:#4B5563; line-height:1.6; margin:0;">
                            Gunakan fitur ini untuk mencadangkan seluruh data kuis, hasil peserta, dan file avatar karyawan. Cadangan akan diunduh dalam format <strong>.zip</strong> yang berisi file SQL database dan aset media.
                        </p>
                        <div style="margin-top:16px; padding:12px 16px; background:rgba(124,58,237,0.05); border-radius:12px; border:1px solid rgba(124,58,237,0.1);">
                            <div style="font-size:12px; font-weight:800; color:#7C3AED; text-transform:uppercase; margin-bottom:4px;">Rekomendasi Penjadwalan</div>
                            <div style="font-size:12px; color:#6B7280;">Harian, pukul <strong>02:00 - 05:00 subuh</strong>.</div>
                            <div style="font-size:11px; color:#9CA3AF; margin-top:8px; border-top:1px solid rgba(0,0,0,0.05); padding-top:8px;">
                                <i class="fa-solid fa-clock-rotate-left" style="margin-right:4px;"></i> Terakhir Cadangkan: 
                                <strong style="color:#4B5563;">{{ $latestBackup ?? 'Belum pernah' }}</strong>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center;">
                        <a href="{{ route('admin.settings.backup') }}" class="btn btn-ghost" style="background:#fff; border:1px solid #E5E7EB; padding:12px 24px;">
                            <i class="fa-solid fa-cloud-arrow-down" style="color:#7C3AED; margin-right:8px;"></i> Cadangkan Database Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:12px;">
            <button type="submit" class="btn btn-primary" style="padding:12px 32px; font-size:14px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

