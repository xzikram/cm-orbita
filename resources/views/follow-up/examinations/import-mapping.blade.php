@extends('layouts.app')

@section('title', 'Impor Transaksi Masal')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Impor Transaksi Masal Pasca Downtime</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Salin dan tempel transaksi manual Anda dari Excel atau Google Sheets secara masal untuk disimpan sebagai transaksi resmi.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a href="{{ route('follow-up.examinations.index') }}" class="btn-secondary">
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
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Gagal mengimpor data:</h3>
                    <ul class="mt-2 list-disc pl-5 space-y-1 text-xs text-red-700 dark:text-red-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="card p-6 space-y-6">
        <form action="{{ route('follow-up.examinations.store-import') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-4">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/40 p-4 ring-1 ring-slate-200/50 dark:ring-slate-700/30 text-xs">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-1.5">Panduan Salin-Tempel (Excel / Sheets):</h4>
                    <p class="text-slate-600 dark:text-slate-400 mb-3 leading-relaxed">
                        1. Blok dan copy <strong>8 kolom data</strong> dari Excel/Google Sheets Anda (Kolom: Tanggal Periksa, Tanggal Daftar, No. Registrasi, Nama Dokter, Nama Pasien, No. RM, Guarantor, Jumlah Pembayaran).<br>
                        2. Tempel langsung ke kotak input di bawah.<br>
                        3. Jika pasien belum terdaftar, sistem akan **mendaftarkan pasien secara otomatis** dengan Nomor RM tersebut.
                    </p>
                    <div class="bg-white dark:bg-slate-900 p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 font-mono text-[9px] text-slate-500 overflow-x-auto whitespace-nowrap">
                        TANGGAL PEMERIKSAAN[TAB]TANGGAL REGISTRASI[TAB]NO. REGISTRASI[TAB]NAMA DOKTER[TAB]NAMA PASIEN[TAB]NOMOR REKAM MEDIK[TAB]GUARANTOR[TAB]JUMLAH<br>
                        24/04/2026[TAB]26/04/2026[TAB]REG/OP/260426-0001[TAB]FF[TAB]NURLIAH HASAN[TAB]016-000-18-56[TAB]PRIBADI[TAB]1.042.500<br>
                        24/04/2026[TAB]26/04/2026[TAB]REG/OP/260426-0002[TAB]FF[TAB]IDA KIRANA CINDY[TAB]012-011-84-22[TAB]PRIBADI[TAB]871.500
                    </div>
                </div>

                <div>
                    <label for="text" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Tempel Data di Sini *</label>
                    <textarea name="text" id="text" rows="10" required class="input-field font-mono text-xs leading-relaxed" placeholder="TANGGAL PEMERIKSAAN&#9;TANGGAL REGISTRASI&#9;NO. REGISTRASI&#9;NAMA DOKTER&#9;NAMA PASIEN&#9;NOMOR REKAM MEDIK&#9;GUARANTOR&#9;JUMLAH&#10;24/04/2026&#9;26/04/2026&#9;REG/OP/260426-0001&#9;FF&#9;NURLIAH HASAN&#9;016-000-18-56&#9;PRIBADI&#9;1.042.500"></textarea>
                    @error('text')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <a href="{{ route('follow-up.examinations.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    Proses Impor Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
