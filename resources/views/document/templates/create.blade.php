@extends('layouts.app')

@section('title', 'Tambah Template Cover PDF')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Tambah Template Cover PDF</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Desain pengaturan margin dan logo untuk PDF Wrapper Document Processing Center.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a href="{{ route('dpc.templates.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card p-6">
        <form action="{{ route('dpc.templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Template *</label>
                    <input type="text" name="name" id="name" required class="input-field mt-2" placeholder="Contoh: Cover Lensa Kontak JEC">
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Deskripsi</label>
                    <textarea name="description" id="description" rows="2" class="input-field mt-2"></textarea>
                </div>

                <div>
                    <label for="margin_top" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Margin Atas (mm) *</label>
                    <input type="number" name="margin_top" id="margin_top" required value="40" min="0" class="input-field mt-2">
                </div>

                <div>
                    <label for="margin_bottom" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Margin Bawah (mm) *</label>
                    <input type="number" name="margin_bottom" id="margin_bottom" required value="30" min="0" class="input-field mt-2">
                </div>

                <div>
                    <label for="margin_left" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Margin Kiri (mm) *</label>
                    <input type="number" name="margin_left" id="margin_left" required value="20" min="0" class="input-field mt-2">
                </div>

                <div>
                    <label for="margin_right" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Margin Kanan (mm) *</label>
                    <input type="number" name="margin_right" id="margin_right" required value="20" min="0" class="input-field mt-2">
                </div>

                <div class="sm:col-span-2">
                    <label for="header_image" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Gambar Header (Opsional)</label>
                    <input type="file" name="header_image" id="header_image" accept="image/jpeg,image/png" class="input-field mt-2 bg-white p-2">
                    <p class="text-xs text-slate-500 mt-1">Logo atau kop surat bagian atas. Max 2MB (JPG/PNG).</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="footer_image" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Gambar Footer (Opsional)</label>
                    <input type="file" name="footer_image" id="footer_image" accept="image/jpeg,image/png" class="input-field mt-2 bg-white p-2">
                    <p class="text-xs text-slate-500 mt-1">Logo atau teks bawah (misal ISO). Max 2MB (JPG/PNG).</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="disclaimer_text" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Teks Disclaimer</label>
                    <textarea name="disclaimer_text" id="disclaimer_text" rows="3" class="input-field mt-2" placeholder="Dokumen ini di-generate oleh sistem dan sah tanpa tanda tangan basah."></textarea>
                </div>

                <div class="sm:col-span-2">
                    <div class="flex items-center gap-x-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                        <label for="is_active" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Template Aktif</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <button type="submit" class="btn-primary">
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
