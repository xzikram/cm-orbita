@extends('layouts.app')

@section('title', 'Document Deliveries')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Pengiriman Dokumen</h1>
                <p class="page-header-desc">Riwayat pengiriman dokumen (PDF) ke pasien melalui Email atau WhatsApp.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('communication.deliveries.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                    Kirim Dokumen
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Waktu Kirim</th>
                    <th>Pasien</th>
                    <th>Tipe Dokumen</th>
                    <th>Saluran</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $delivery)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $delivery->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $delivery->patient->name }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">
                                @if(($delivery->channel ?? 'email') === 'whatsapp')
                                    WA: {{ $delivery->recipient_phone ?? '-' }}
                                @else
                                    Email: {{ $delivery->recipient_email ?? '-' }}
                                @endif
                            </div>
                        </td>
                        <td class="text-slate-600 dark:text-slate-300">
                            {{ $delivery->documentType->name }}
                        </td>
                        <td>
                            @if(($delivery->channel ?? 'email') === 'whatsapp')
                                <span class="badge-green">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                                    WHATSAPP
                                </span>
                            @else
                                <span class="badge-blue">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                    EMAIL
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(in_array($delivery->status, ['sent', 'success']))
                                <span class="badge-green">SENT</span>
                            @elseif($delivery->status === 'failed')
                                <span class="badge-red">FAILED</span>
                            @else
                                <span class="badge-yellow">{{ strtoupper($delivery->status) }}</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('communication.deliveries.show', $delivery) }}" class="table-action-primary">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada riwayat pengiriman</h3>
                                <p class="empty-state-desc">Dokumen yang dikirim akan tampil di sini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $deliveries->links() }}
    </div>
</div>
@endsection
