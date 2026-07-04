@extends('layouts.app')

@section('title', 'Daftar Pemeriksaan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Daftar Pemeriksaan</h1>
                <p class="page-header-desc">Daftar riwayat pemeriksaan awal pasien yang digunakan sebagai dasar follow-up.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex flex-wrap items-center gap-3">
                <a href="{{ route('follow-up.examinations.export-csv', request()->all()) }}" class="btn-secondary" title="Ekspor daftar pemeriksaan/transaksi sebagai Excel">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Ekspor Excel
                </a>
                <a href="{{ route('follow-up.examinations.import') }}" class="btn-secondary" title="Impor masal transaksi downtime dari Excel/Sheets">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15M9 12l3 3m0 0l3-3m-3 3V2.25" /></svg>
                    Impor Transaksi
                </a>
                <a href="{{ route('follow-up.examinations.create-downtime') }}" class="btn-primary bg-amber-600 hover:bg-amber-500 focus-visible:outline-amber-600 border-none" title="Catat transaksi manual downtime">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Catat Transaksi Downtime
                </a>
                <a href="{{ route('follow-up.examinations.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Catat Pemeriksaan
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar & Filters -->
    <div class="card p-4">
        <form action="{{ route('follow-up.examinations.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center">
            <div class="flex-1 relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pasien atau No. RM..." class="input-field pl-11">
            </div>
            <div class="flex gap-3 items-center">
                <select name="downtime" onchange="this.form.submit()" class="input-field py-2.5">
                    <option value="">-- Semua Status SIMRS --</option>
                    <option value="1" {{ request('downtime') === '1' ? 'selected' : '' }}>Downtime SIMRS</option>
                    <option value="0" {{ request('downtime') === '0' ? 'selected' : '' }}>Normal</option>
                </select>
                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->anyFilled(['search', 'downtime']))
                    <a href="{{ route('follow-up.examinations.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Registrasi</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Guarantor</th>
                    <th>Tindakan / Lensa</th>
                    <th>Jumlah</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examinations as $exam)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            <div class="text-sm">{{ $exam->examination_date->format('d M Y') }}</div>
                            @if($exam->registration_date)
                                <div class="text-[10px] text-slate-400">Reg: {{ $exam->registration_date->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="font-mono text-xs text-slate-600 dark:text-slate-400">
                            {{ $exam->registration_number ?? '-' }}
                        </td>
                        <td>
                            <div class="flex items-center gap-2 flex-wrap">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $exam->patient->name }}</div>
                                @if($exam->is_downtime_entry)
                                    <span class="badge-yellow text-[9px] py-0.5 px-1.5 font-bold uppercase tracking-wide">Downtime</span>
                                @endif
                            </div>
                            <div class="text-xs text-slate-400">
                                RM: {{ $exam->patient->medical_record_number }}
                                @if($exam->patient->temporary_medical_record_number)
                                    <span class="block text-[10px] text-amber-600 dark:text-amber-400 font-mono">Smt: {{ $exam->patient->temporary_medical_record_number }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 font-medium">{{ $exam->doctor->name ?? '-' }}</td>
                        <td>
                            @if($exam->guarantor)
                                <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-400">
                                    {{ $exam->guarantor }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">
                            @if($exam->tindakan)
                                <div class="font-medium text-slate-900 dark:text-white text-xs">{{ $exam->tindakan }}</div>
                            @endif
                            <div class="text-[11px]">{{ $exam->lens_type ?? '-' }} - {{ $exam->lens_brand ?? '-' }}</div>
                        </td>
                        <td class="font-mono text-slate-900 dark:text-white font-semibold">
                            Rp {{ number_format($exam->total_payment, 0, ',', '.') }}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('follow-up.examinations.show', $exam) }}" class="table-action-primary">Detail</a>
                                @if(Auth::user()->hasRole('super-admin'))
                                    <button type="button" 
                                            @click="$dispatch('open-delete-modal', { url: '{{ route('follow-up.examinations.destroy', $exam) }}', name: 'Pemeriksaan {{ addslashes($exam->patient->name) }}' })" 
                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-xs font-semibold inline-flex items-center gap-1.5 ml-1">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada data pemeriksaan</h3>
                                <p class="empty-state-desc">Mulai dengan mencatat pemeriksaan baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $examinations->links() }}
    </div>

    <!-- Modal Konfirmasi Hapus Data dengan Alasan -->
    <div x-data="{ open: false, url: '', name: '', reason: '' }"
         @open-delete-modal.window="open = true; url = $event.detail.url; name = $event.detail.name; reason = ''"
         x-show="open" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md" 
         x-cloak>
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl border border-slate-200 dark:border-slate-800" @click.outside="open = false">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 font-display">Konfirmasi Penghapusan</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Apakah Anda yakin ingin menghapus data <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="name"></span>? Tindakan ini menggunakan Soft Delete.</p>
            
            <form :action="url" method="POST" class="space-y-4">
                @csrf
                @method('DELETE')
                <div class="space-y-1">
                    <label for="delete_reason" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Alasan Penghapusan</label>
                    <input type="text" id="delete_reason" name="reason" x-model="reason" class="input-field" required placeholder="Masukkan alasan penghapusan data...">
                </div>
                
                <div class="flex justify-end gap-3 pt-2">
                    <button @click="open = false" type="button" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-danger" :disabled="reason.trim().length < 5">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
