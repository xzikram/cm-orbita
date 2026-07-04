@extends('layouts.app')

@section('title', 'Impor Data Dokter')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Impor Data Dokter</h1>
            <p class="page-header-desc">Unggah berkas CSV atau tempel tabel langsung dari Excel untuk mengimpor data dokter secara massal.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-sm text-red-700 dark:text-red-400">
            <h4 class="font-bold mb-1">Beberapa baris gagal diimpor:</h4>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Instructions -->
        <div class="md:col-span-1 space-y-6">
            <div class="card p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Petunjuk Format</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Pastikan susunan kolom pada Excel atau CSV Anda mengikuti salah satu format berikut:
                </p>
                <div class="border border-slate-100 dark:border-slate-800 rounded-lg p-3 bg-slate-50 dark:bg-slate-950 font-mono text-[10px] text-slate-600 dark:text-slate-400 space-y-2">
                    <div>
                        <strong class="text-slate-800 dark:text-slate-200">Format A (3 Kolom):</strong>
                        <div class="mt-1">No | Singkatan | Nama Dokter</div>
                        <div class="text-slate-400 mt-0.5">Contoh: 1 | AB | dr. Muh. Abrar, Sp.M</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <strong class="text-slate-800 dark:text-slate-200">Format B (2 Kolom):</strong>
                        <div class="mt-1">Singkatan | Nama Dokter</div>
                        <div class="text-slate-400 mt-0.5">Contoh: AB | dr. Muh. Abrar, Sp.M</div>
                    </div>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-2 leading-relaxed">
                    <p>💡 <strong class="text-slate-700 dark:text-slate-300">Deteksi Otomatis:</strong> Sistem akan otomatis mengekstrak spesialisasi (seperti Sp.M, Sp.M(K), atau Sp.PD) dari kolom Nama Dokter.</p>
                    <p>🔄 <strong class="text-slate-700 dark:text-slate-300">Pembaruan Otomatis:</strong> Jika Singkatan atau Nama Dokter sudah ada, sistem akan memperbarui data dokter tersebut.</p>
                </div>
            </div>
        </div>

        <!-- Form Import -->
        <div class="md:col-span-2 space-y-6">
            <div class="card p-6">
                <form action="{{ route('master-data.doctors.store-import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- Text Copy-Paste Area -->
                    <div class="space-y-2">
                        <label for="text" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Tempel Data dari Excel</label>
                        <textarea name="text" id="text" rows="8" class="input-field font-mono text-xs" placeholder="Salin tabel di Excel (termasuk kolom Singkatan & Nama Dokter), lalu tempel di sini..."></textarea>
                    </div>

                    <div class="relative flex py-2 items-center">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
                        <span class="flex-shrink mx-4 text-xs text-slate-400 font-medium uppercase tracking-wider">Atau Unggah Berkas</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2">
                        <label for="file" class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Pilih Berkas CSV / TXT</label>
                        <input type="file" name="file" id="file" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-slate-800 dark:file:text-slate-200">
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('master-data.doctors.index') }}" class="btn-secondary">Kembali</a>
                        <button type="submit" class="btn-primary">Proses Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
