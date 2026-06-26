@extends('layouts.app')

@section('title', 'Master Data Dokter')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Master Data Dokter</h1>
                <p class="page-header-desc">Kelola daftar dokter yang bertugas di klinik.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3" x-data="{ open: false, password: '' }">
                @if($doctors->total() > 0)
                    <button @click="open = true; password = ''" class="btn-danger">
                        Hapus Semua
                    </button>
                @endif
                <a href="{{ route('master-data.doctors.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Dokter
                </a>

                <!-- Modal Konfirmasi Hapus Semua -->
                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md" x-cloak>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl border border-slate-200 dark:border-slate-800" @click.outside="open = false">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Semua Dokter?</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Tindakan ini akan menghapus seluruh data master dokter di klinik ini beserta akun user dokter terkait. Masukkan password konfirmasi untuk melanjutkan.</p>
                        
                        <input type="password" x-model="password" class="input-field mb-4" placeholder="Masukkan password konfirmasi...">
                        
                        <div class="flex justify-end gap-3">
                            <button @click="open = false; password = ''" type="button" class="btn-secondary">Batal</button>
                            <form action="{{ route('master-data.doctors.deleteAll') }}" method="POST">
                                @csrf
                                <input type="hidden" name="confirm_password" :value="password">
                                <button type="submit" class="btn-danger" :disabled="password !== 'Ikr@21983'">Hapus Semua</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Dokter</th>
                    <th>Spesialisasi</th>
                    <th>SIP</th>
                    <th>Kontak</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doctor)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $doctor->name }}
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">{{ $doctor->specialization ?? '-' }}</td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs">{{ $doctor->sip_number ?? '-' }}</td>
                        <td>
                            <div class="text-slate-600 dark:text-slate-300">{{ $doctor->phone ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $doctor->email ?? '' }}</div>
                        </td>
                        <td>
                            @if($doctor->is_active)
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
                            <a href="{{ route('master-data.doctors.edit', $doctor) }}" class="table-action-edit">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada data dokter</h3>
                                <p class="empty-state-desc">Mulai dengan menambahkan dokter baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $doctors->links() }}
    </div>
</div>
@endsection
