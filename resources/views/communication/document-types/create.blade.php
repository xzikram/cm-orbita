@extends('layouts.app')

@section('title', 'Tambah Tipe Dokumen')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Tambah Tipe Dokumen</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Buat jenis/kategori dokumen baru untuk pengiriman.</p>
        </div>
        <a href="{{ route('communication.document-types.index') }}" class="btn-secondary mt-4 sm:mt-0">Kembali</a>
    </div>
    <div class="card p-6">
        <form action="{{ route('communication.document-types.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 gap-y-6">
                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Kode *</label>
                    <input type="text" name="code" id="code" required value="{{ old('code') }}" class="input-field mt-2" placeholder="Contoh: LVC">
                    <p class="mt-1 text-xs text-slate-500">Kode singkat untuk referensi internal (unik).</p>
                    @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">Nama Tipe *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="input-field mt-2" placeholder="Contoh: Laporan Pemeriksaan Lensa Kontak">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <div class="flex items-center gap-x-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-600 dark:border-slate-600 dark:bg-slate-700">
                        <label for="is_active" class="text-sm font-medium text-slate-900 dark:text-slate-200">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-slate-700 pt-6">
                <button type="submit" class="btn-primary">Simpan Tipe Dokumen</button>
            </div>
        </form>
    </div>
</div>
@endsection
