@extends('layouts.app')

@section('title', 'Monitor Reminder')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Monitor Reminder WhatsApp & Email</h1>
            <p class="page-header-desc">Pantau status pengiriman pesan notifikasi dan pengingat kepada pasien.</p>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Jadwal Kirim</th>
                    <th>Pasien</th>
                    <th>Channel</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reminders as $reminder)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $reminder->scheduled_at->format('d M Y H:i') }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">
                            {{ $reminder->followUpSchedule->patient->name ?? $reminder->recipient_name }}
                        </td>
                        <td>
                            @if($reminder->channel === 'whatsapp')
                                <span class="badge-green">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                    WA
                                </span>
                            @else
                                <span class="badge-blue">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                    EMAIL
                                </span>
                            @endif
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs">
                            {{ $reminder->recipient_phone ?? $reminder->recipient_email }}
                        </td>
                        <td>
                            @php
                                $statusBadge = match($reminder->status) {
                                    'sent' => 'badge-green',
                                    'pending' => 'badge-yellow',
                                    'failed' => 'badge-red',
                                    default => 'badge-blue',
                                };
                            @endphp
                            <span class="{{ $statusBadge }}">{{ strtoupper($reminder->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                                <h3 class="empty-state-title">Belum ada log reminder</h3>
                                <p class="empty-state-desc">Reminder yang dijadwalkan akan tampil di sini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $reminders->links() }}</div>
</div>
@endsection
