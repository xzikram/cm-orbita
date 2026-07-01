@extends('layouts.app')

@section('title', 'Log Pengiriman Notifikasi')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">Log Pengiriman Notifikasi</h1>
            <p class="page-header-desc">Menampilkan detail histori pengiriman pesan (WhatsApp / Email) dari mesin Reminder.</p>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Waktu Eksekusi</th>
                    <th>Pasien</th>
                    <th>Tipe Pesan</th>
                    <th>Status API</th>
                    <th>Pesan Error</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $log->created_at->format('d M Y, H:i:s') }}
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">
                            {{ $log->reminder->followUpSchedule->patient->name ?? $log->reminder->recipient_name ?? 'Unknown' }}
                        </td>
                        <td>
                            @if(isset($log->reminder))
                                <span class="badge-blue">{{ strtoupper($log->reminder->recipient_type ?? 'PATIENT') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'success')
                                <span class="badge-green">SUCCESS</span>
                            @else
                                <span class="badge-red">FAILED</span>
                            @endif
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 text-xs max-w-xs truncate">
                            {{ $log->error_message ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                <h3 class="empty-state-title">Belum ada log pengiriman</h3>
                                <p class="empty-state-desc">Log pengiriman pesan dari API akan tampil di sini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $logs->links() }}</div>
</div>
@endsection
