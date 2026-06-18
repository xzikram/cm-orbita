@extends('layouts.app')

@section('title', 'Edit WhatsApp Template')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card p-6">
        <h2 class="text-base font-semibold leading-7 text-slate-900 dark:text-white">Edit WhatsApp Template</h2>
        <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-400">Ubah template pesan WhatsApp. Anda dapat menyisipkan variabel dinamis menggunakan kurung kurawal `{nama_variabel}`.</p>

        <form action="{{ route('communication.whatsapp-templates.update', $whatsappTemplate) }}" method="POST" class="mt-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Template</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required value="{{ old('name', $whatsappTemplate->name) }}" class="input-field" placeholder="contoh: Pengingat Kontrol H+1">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Jenis Template</label>
                    <div class="mt-2">
                        <select name="type" id="type" required class="input-field select-field">
                            <option value="follow_up" {{ old('type', $whatsappTemplate->type) === 'follow_up' ? 'selected' : '' }}>Follow Up / Rencana Kontrol</option>
                            <option value="appointment" {{ old('type', $whatsappTemplate->type) === 'appointment' ? 'selected' : '' }}>Kunjungan Baru / Janji Temu</option>
                            <option value="custom" {{ old('type', $whatsappTemplate->type) === 'custom' ? 'selected' : '' }}>Kustom Umum</option>
                        </select>
                    </div>
                    @error('type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200 mb-2">Isi Pesan (WhatsApp Plaintext)</label>
                <div class="mt-2">
                    <textarea name="content" id="content" required rows="8" class="input-field font-mono text-sm" placeholder="Yth. {patient_name},&#10;&#10;Ini adalah pengingat untuk jadwal kontrol lensa kontak Anda pada tanggal {scheduled_date}.&#10;&#10;Terima kasih.&#10;{clinic_name}">{{ old('content', $whatsappTemplate->content) }}</textarea>
                </div>
                
                <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-900 rounded-lg text-xs text-slate-600 dark:text-slate-400 space-y-2">
                    <p class="font-semibold text-slate-800 dark:text-slate-200">Petunjuk Variabel Dinamis:</p>
                    <p>Sistem akan otomatis mendeteksi variabel yang Anda tulis dalam kurung kurawal `{variabel}`. Beberapa contoh variabel standar:</p>
                    <div class="grid grid-cols-2 gap-2 font-mono">
                        <div><strong class="text-blue-600">{patient_name}</strong> : Nama Pasien</div>
                        <div><strong class="text-blue-600">{scheduled_date}</strong> : Tanggal Kontrol</div>
                        <div><strong class="text-blue-600">{doctor_name}</strong> : Nama Dokter</div>
                        <div><strong class="text-blue-600">{clinic_name}</strong> : Nama Klinik</div>
                        <div><strong class="text-blue-600">{follow_up_label}</strong> : Rencana Kontrol (H+1, dst)</div>
                        <div><strong class="text-blue-600">{medical_record_number}</strong> : No. Rekam Medis</div>
                    </div>
                </div>
                @error('content')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-y-3 sm:flex-row sm:items-center sm:gap-x-6">
                <div class="flex items-center gap-x-3">
                    <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $whatsappTemplate->is_default) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                    <label for="is_default" class="block text-sm leading-6 text-slate-900 dark:text-slate-200">Jadikan Template Default untuk Jenis Ini</label>
                </div>

                <div class="flex items-center gap-x-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $whatsappTemplate->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                    <label for="is_active" class="block text-sm leading-6 text-slate-900 dark:text-slate-200">Aktif</label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <a href="{{ route('communication.whatsapp-templates.index') }}" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-300 hover:text-slate-500">Batal</a>
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
