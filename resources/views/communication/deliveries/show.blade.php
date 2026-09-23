@extends('layouts.app')

@section('title', 'Detail Pengiriman Dokumen')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Detail Pengiriman Dokumen</h1>
            <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Riwayat lengkap pengiriman dokumen ke pasien.</p>
        </div>
        <a href="{{ route('communication.deliveries.index') }}" class="btn-secondary mt-4 sm:mt-0">Kembali</a>
    </div>

    <div class="space-y-6">
        @if($delivery->status === 'failed' && $delivery->error_message)
            @php $statusInfo = $delivery->status_info; @endphp
            <div class="rounded-2xl bg-amber-50 dark:bg-amber-950/20 p-5 border border-amber-200 dark:border-amber-800/40 space-y-4">
                <div class="flex items-start justify-between gap-x-4">
                    <div class="flex items-start gap-x-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-inset {{ $statusInfo['class'] }}">
                                {{ $statusInfo['label'] }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Pengiriman Dokumen Belum Berhasil</h3>
                            <div class="mt-1 text-sm text-slate-700 dark:text-slate-300">
                                <p>{{ $delivery->error_message }}</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('communication.deliveries.markAsSent', $delivery) }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengubah status pengiriman ini menjadi Terkirim (SENT)?')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                            Tandai Terkirim
                        </button>
                    </form>
                </div>

                @if(($delivery->channel ?? 'email') === 'whatsapp')
                    <div class="pt-3 border-t border-amber-200/70 dark:border-amber-800/40">
                        <form action="{{ route('communication.deliveries.resendPhone', $delivery) }}" method="POST" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <div class="flex-1 min-w-[220px]">
                                <label for="new_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Koreksi Nomor WhatsApp Tujuan</label>
                                <input type="text" name="new_phone" id="new_phone" value="{{ old('new_phone', $delivery->recipient_phone) }}" required class="input-field mt-1 text-sm py-1.5 font-mono" placeholder="Contoh: 081355427971">
                            </div>
                            <button type="submit" onclick="return confirm('Kirim ulang dokumen yang sama ke nomor baru ini?')" class="btn-primary py-2 px-4 text-xs font-semibold flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                </svg>
                                Kirim Ulang ke Nomor Baru
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif



        <div class="card p-6">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-700 pb-3 mb-4">Informasi Pengiriman</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <dt class="text-slate-500">Pasien</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->patient->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">No. RM</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->patient->medical_record_number }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Tipe Dokumen</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->documentType->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Metode Pengiriman</dt>
                    <dd class="mt-1">
                        @if(($delivery->channel ?? 'email') === 'whatsapp')
                            <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:text-emerald-400 ring-1 ring-inset ring-emerald-700/10">WHATSAPP</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/20 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10">EMAIL</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Dikirim Oleh</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->sender->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Waktu Kirim</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->created_at->format('d M Y, H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Status</dt>
                    <dd class="mt-1">
                        @php $statusInfo = $delivery->status_info; @endphp
                        <span id="status-badge" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $statusInfo['class'] }}">
                            {{ $statusInfo['label'] }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-500">Template Pesan</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->emailTemplate->name ?? '-' }}</dd>
                </div>

                @if(($delivery->channel ?? 'email') === 'whatsapp')
                    <div>
                        <dt class="text-slate-500">No. WhatsApp Tujuan</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->recipient_phone ?? '-' }}</dd>
                    </div>
                @else
                    <div>
                        <dt class="text-slate-500">Email Tujuan</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->recipient_email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">SMTP Account</dt>
                        <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->emailAccount->name ?? '-' }} ({{ $delivery->emailAccount->email_address ?? '' }})</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-slate-500">Nama Berkas</dt>
                    <dd class="mt-1 font-medium text-slate-900 dark:text-white">{{ $delivery->attachment_name ?? '-' }}</dd>
                </div>

                @if(($delivery->channel ?? 'email') === 'whatsapp' && $delivery->attachment_path)
                    <div class="sm:col-span-2 border-t border-slate-100 dark:border-slate-700/50 pt-4">
                        <dt class="text-slate-500 mb-2 font-medium">Tautan Unduhan Dokumen (PDF Terproteksi)</dt>
                        <dd>
                            <a href="{{ asset(Storage::url($delivery->attachment_path)) }}" target="_blank" class="btn-primary inline-flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Unduh Dokumen
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
</div>
@endsection
