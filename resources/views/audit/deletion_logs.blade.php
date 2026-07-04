@extends('layouts.app')

@section('title', 'Laporan Penghapusan Data')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Laporan Penghapusan Data</h1>
                <p class="page-header-desc">Riwayat penghapusan (soft-delete) dokter dan pasien beserta alasan penghapusan.</p>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card p-4">
        <form action="{{ route('audit.deletion-logs') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama data, NIK/RM, atau alasan..." class="input-field pl-11">
            </div>
            <button type="submit" class="btn-primary">Cari</button>
            @if(request('search'))
                <a href="{{ route('audit.deletion-logs') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Waktu Hapus</th>
                    <th>Tipe Data</th>
                    <th>Nama Data</th>
                    <th>No. RM / Identitas</th>
                    <th>Alasan Penghapusan</th>
                    <th>Dihapus Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap font-mono text-xs text-slate-500 dark:text-slate-400">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="whitespace-nowrap">
                            @if(str_contains($log->model_type, 'Patient'))
                                <span class="inline-flex items-center gap-x-1.5 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-500/20">
                                    Pasien
                                </span>
                            @else
                                <span class="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-400 dark:ring-indigo-500/20">
                                    Dokter
                                </span>
                            @endif
                        </td>
                        <td class="font-semibold text-slate-900 dark:text-white">
                            {{ $log->model_name }}
                        </td>
                        <td class="font-mono text-xs text-slate-600 dark:text-slate-300">
                            {{ $log->model_identifier }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 max-w-xs break-words">
                            {{ $log->reason }}
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-primary-500 flex items-center justify-center text-white text-[10px] font-bold">
                                    {{ strtoupper(substr($log->user->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="text-xs font-medium text-slate-900 dark:text-white">{{ $log->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <h3 class="empty-state-title">Tidak ada log penghapusan</h3>
                                <p class="empty-state-desc">Seluruh penghapusan data pasien & dokter akan tercatat di sini.</p>
                            </div>
                        </td>
                    </tr>
                @forelse($logs as $log)
                @endforelse
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $logs->links() }}
    </div>
</div>
@endsection
