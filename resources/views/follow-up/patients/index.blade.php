@extends('layouts.app')

@section('title', 'Daftar Pasien')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Daftar Pasien</h1>
                <p class="page-header-desc">Daftar seluruh pasien yang terdaftar di klinik beserta riwayat pemeriksaan dan kontrol mereka.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3" x-data="{ open: false, password: '' }">
                @if($patients->total() > 0)
                    <button @click="open = true; password = ''" class="btn-danger">
                        Hapus Semua
                    </button>
                @endif
                <a href="{{ route('follow-up.patients.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Pasien
                </a>

                <!-- Modal Konfirmasi Hapus Semua -->
                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md" x-cloak>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl border border-slate-200 dark:border-slate-800" @click.outside="open = false">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Semua Pasien?</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Tindakan ini akan menghapus seluruh data master pasien beserta data pemeriksaan dan kunjungan kontrol terkait secara permanen. Masukkan password konfirmasi untuk melanjutkan.</p>
                        
                        <input type="password" x-model="password" class="input-field mb-4" placeholder="Masukkan password konfirmasi...">
                        
                        <div class="flex justify-end gap-3">
                            <button @click="open = false; password = ''" type="button" class="btn-secondary">Batal</button>
                            <form action="{{ route('follow-up.patients.deleteAll') }}" method="POST">
                                @csrf
                                <input type="hidden" name="confirm_password" :value="password">
                                <button type="submit" class="btn-danger" :disabled="password.length === 0">Hapus Semua</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="max-w-md">
        <form action="{{ route('follow-up.patients.index') }}" method="GET" class="relative">
            <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau No. RM..." class="input-field pl-11">
            <button type="submit" class="hidden">Search</button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>No. Rekam Medis</th>
                    <th>Kontak</th>
                    <th>Tanggal Lahir</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>
                            <div class="flex items-center gap-x-3">
                                <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold ring-1 ring-primary-200/50 dark:ring-primary-700/30">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $patient->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-mono text-slate-500 dark:text-slate-400">{{ $patient->medical_record_number }}</td>
                        <td>
                            <div class="text-slate-600 dark:text-slate-300">{{ $patient->phone ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $patient->email ?? '' }}</div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">
                            {{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : '-' }}
                        </td>
                        <td>
                            @if($patient->is_active)
                                <span class="badge-green">
                                    <svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                    Aktif
                                </span>
                            @else
                                <span class="badge-red">
                                    <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('follow-up.patients.show', $patient) }}" class="table-action-primary">Profil</a>
                                <a href="{{ route('follow-up.patients.edit', $patient) }}" class="table-action-edit">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada data pasien</h3>
                                <p class="empty-state-desc">Mulai dengan menambahkan pasien baru ke sistem.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $patients->links() }}
    </div>
</div>
@endsection
