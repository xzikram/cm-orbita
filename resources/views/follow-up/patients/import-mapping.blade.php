@extends('layouts.app')

@section('title', 'Update Masal Nomor RM')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ tab: 'paste' }">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Update Masal Nomor RM Pasca Downtime</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Perbarui Nomor Rekam Medis (RM) sementara menjadi Nomor RM resmi secara masal dengan mengunggah berkas CSV atau menyalin langsung dari spreadsheet.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a href="{{ route('follow-up.patients.index') }}" class="btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-red-50 dark:bg-red-900/20 p-4 ring-1 ring-red-500/20 shadow-sm shadow-red-500/5">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Gagal memproses data:</h3>
                    <ul class="mt-2 list-disc pl-5 space-y-1 text-xs text-red-700 dark:text-red-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- TABS Navigation -->
    <div class="border-b border-slate-200 dark:border-slate-800 mb-6">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button @click="tab = 'paste'" :class="tab === 'paste' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-semibold flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Tempel dari Excel / Sheets (Rekomendasi)
            </button>
            <button @click="tab = 'upload'" :class="tab === 'upload' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-semibold flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                Unggah Berkas CSV
            </button>
        </nav>
    </div>

    <div class="card p-6 space-y-6">
        <form action="{{ route('follow-up.patients.store-import-mapping') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Paste Tab -->
            <div x-show="tab === 'paste'" class="space-y-4">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/40 p-4 ring-1 ring-slate-200/50 dark:ring-slate-700/30 text-xs">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-1.5">Panduan Salin-Tempel (Excel / Sheets):</h4>
                    <p class="text-slate-600 dark:text-slate-400 mb-2 leading-relaxed">
                        1. Blok dan copy <strong>2 kolom data</strong> dari Excel/Google Sheets Anda (Kolom pertama berisi <strong>RM Sementara</strong>, kolom kedua berisi <strong>RM Baru</strong>).<br>
                        2. Tempel langsung ke kotak input di bawah.
                    </p>
                    <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 font-mono text-[10px] text-slate-500">
                        20260424-172530[TAB]016-002-07-02<br>
                        20260424-172545[TAB]016-002-07-03
                    </div>
                </div>

                <div>
                    <label for="text" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Tempel Data di Sini *</label>
                    <textarea name="text" id="text" rows="8" :required="tab === 'paste'" class="input-field font-mono text-xs leading-relaxed" placeholder="PatientID&#9;NewMR&#10;20260424-172530&#9;016-002-07-02&#10;20260424-172545&#9;016-002-07-03"></textarea>
                    @error('text')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Upload Tab -->
            <div x-show="tab === 'upload'" class="space-y-4" style="display: none;">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/40 p-4 ring-1 ring-slate-200/50 dark:ring-slate-700/30 text-xs">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-1.5">Panduan Format File CSV:</h4>
                    <p class="text-slate-600 dark:text-slate-400 mb-2 leading-relaxed">
                        Berkas CSV harus memiliki header dan setidaknya memiliki kolom berikut (dapat menggunakan pemisah koma atau titik koma):
                    </p>
                    <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 font-mono text-[10px] text-slate-500">
                        patientid;NewMR<br>
                        20260424-172530;016-002-07-02<br>
                        20260424-172545;016-002-07-03
                    </div>
                </div>

                <div>
                    <label for="file" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Pilih Berkas CSV *</label>
                    <div class="mt-2">
                        <input type="file" name="file" id="file" :required="tab === 'upload'" accept=".csv,.txt" class="input-field py-2 text-xs">
                    </div>
                    @error('file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <a href="{{ route('follow-up.patients.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    Proses Update RM
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
