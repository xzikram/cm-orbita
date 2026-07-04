@extends('layouts.app')

@section('title', 'Event Pemeriksaan Gratis')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Event Pemeriksaan Gratis</h1>
                <p class="page-header-desc">Kelola event pemeriksaan mata gratis di luar klinik beserta pendaftaran peserta mandiri menggunakan QR Code.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('follow-up.events.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Event Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card p-4">
        <form action="{{ route('follow-up.events.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1 relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama event atau lokasi..." class="input-field pl-11">
            </div>
            <button type="submit" class="btn-primary">Cari</button>
            @if(request('search'))
                <a href="{{ route('follow-up.events.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Kode Akses (URL)</th>
                    <th>Tanggal Event</th>
                    <th>Lokasi</th>
                    <th>Jumlah Pendaftar</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $event->name }}</div>
                            <div class="text-xs text-slate-500 max-w-xs truncate">{{ $event->description ?? '-' }}</div>
                        </td>
                        <td class="font-mono text-xs text-slate-500">
                            <a href="{{ route('events.register', $event->code) }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">
                                /e/{{ $event->code }}
                            </a>
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">
                            {{ $event->event_date ? $event->event_date->format('d M Y') : '-' }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">
                            {{ $event->location }}
                        </td>
                        <td class="font-semibold text-slate-900 dark:text-white">
                            {{ $event->patients_count }} Orang
                        </td>
                        <td>
                            @if($event->is_active)
                                <span class="badge-green">Aktif</span>
                            @else
                                <span class="badge-red">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('follow-up.events.show', $event) }}" class="table-action-primary">Detail / QR</a>
                                <form action="{{ route('follow-up.events.toggle-active', $event) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors {{ $event->is_active ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $event->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada event</h3>
                                <p class="empty-state-desc">Silakan buat event pemeriksaan gratis baru untuk mulai menjaring pasien di lapangan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $events->links() }}
    </div>
</div>
@endsection
