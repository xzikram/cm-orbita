@extends('layouts.app')

@section('title', 'Jadwal Kontrol')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Jadwal Kontrol Pasien</h1>
            <p class="page-header-desc">Daftar jadwal follow-up pasien beserta status pelaksanaannya.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] shadow-sm hover:shadow-md transition-all duration-300">
            <dt class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Semua</dt>
            <dd class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $statusCounts['all'] ?? 0 }}</dd>
        </div>
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 ring-amber-500/20 shadow-sm hover:shadow-md transition-all duration-300">
            <dt class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pending</dt>
            <dd class="mt-2 text-2xl font-extrabold tracking-tight text-amber-600 dark:text-amber-400">{{ $statusCounts['pending'] ?? 0 }}</dd>
        </div>
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 ring-emerald-500/20 shadow-sm hover:shadow-md transition-all duration-300">
            <dt class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Selesai</dt>
            <dd class="mt-2 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $statusCounts['completed'] ?? 0 }}</dd>
        </div>
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 ring-red-500/20 shadow-sm hover:shadow-md transition-all duration-300">
            <dt class="text-xs font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider">Missed</dt>
            <dd class="mt-2 text-2xl font-extrabold tracking-tight text-red-600 dark:text-red-400">{{ $statusCounts['missed'] ?? 0 }}</dd>
        </div>
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 ring-orange-500/20 bg-orange-50/50 dark:bg-orange-900/10 shadow-sm hover:shadow-md transition-all duration-300">
            <dt class="text-xs font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wider">Overdue</dt>
            <dd class="mt-2 text-2xl font-extrabold tracking-tight text-orange-600 dark:text-orange-400">{{ $statusCounts['overdue'] ?? 0 }}</dd>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Tanggal Jadwal</th>
                    <th>Pasien</th>
                    <th>Dokter Awal</th>
                    <th>Jenis Kontrol</th>
                    <th>Status Kunjungan</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white">
                            {{ $schedule->scheduled_date->format('d M Y') }}
                        </td>
                        <td>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $schedule->patient->name }}</div>
                            <div class="text-xs text-slate-400">RM: {{ $schedule->patient->medical_record_number }}</div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">
                            {{ $schedule->examination->doctor->name ?? '-' }}
                        </td>
                        <td class="font-medium text-slate-600 dark:text-slate-300">
                            {{ $schedule->label }}
                        </td>
                        <td>
                            @php
                                $statusConfig = match($schedule->status) {
                                    'completed' => ['class' => 'badge-green', 'label' => 'SELESAI'],
                                    'pending' => ['class' => 'badge-yellow', 'label' => 'PENDING'],
                                    'missed' => ['class' => 'badge-red', 'label' => 'MISSED'],
                                    'rescheduled' => ['class' => 'badge-blue', 'label' => 'RESCHEDULE'],
                                    default => ['class' => 'badge-blue', 'label' => strtoupper($schedule->status)],
                                };
                            @endphp
                            <span class="{{ $statusConfig['class'] }}">{{ $statusConfig['label'] }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                @if($schedule->status === 'pending' || $schedule->status === 'missed')
                                    <!-- Status pengiriman WA -->
                                    @if($schedule->reminder_sent)
                                        <span class="inline-flex items-center gap-x-1 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 px-3 py-2 text-xs font-semibold text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20 dark:ring-emerald-500/20" title="Terkirim pada {{ $schedule->reminder_sent_at ? $schedule->reminder_sent_at->format('d M Y H:i') : '' }}">
                                            <svg class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                            WA Terkirim
                                        </span>
                                    @elseif(!$schedule->isReadyForReminder())
                                        <!-- Belum Masuk Waktunya -->
                                        <button type="button" class="inline-flex items-center gap-x-1 rounded-xl bg-slate-100 dark:bg-slate-800/40 px-3 py-2 text-xs font-semibold text-slate-400 dark:text-slate-500 ring-1 ring-inset ring-slate-200 dark:ring-slate-800 cursor-not-allowed" title="Belum masuk waktu pengiriman reminder" disabled>
                                            <svg class="h-3.5 w-3.5 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0" /></svg>
                                            Belum Waktunya
                                        </button>
                                    @else
                                        <!-- Tombol Kirim WA -->
                                        <form action="{{ route('follow-up.schedules.send-reminder', $schedule) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-x-1.5 rounded-xl bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition-all duration-200" title="Kirim Pengingat WhatsApp">
                                                <svg class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                                Kirim WA
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('follow-up.schedules.record', $schedule) }}" class="table-action-primary">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Catat Kehadiran
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 dark:text-slate-500">Tercatat</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada jadwal kontrol</h3>
                                <p class="empty-state-desc">Jadwal akan muncul setelah pemeriksaan ditambahkan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $schedules->links() }}
    </div>
</div>
@endsection
