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
            <div class="rounded-md bg-red-50 dark:bg-red-950/20 p-4 border border-red-200 dark:border-red-800/30">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Gagal Mengirim Dokumen</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                            <p>{{ $delivery->error_message }}</p>
                        </div>
                    </div>
                </div>
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
                        @if(in_array($delivery->status, ['sent', 'success']))
                            <span id="status-badge" class="inline-flex items-center rounded-full bg-green-50 dark:bg-green-900/20 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20">SENT</span>
                        @elseif($delivery->status === 'failed')
                            <span id="status-badge" class="inline-flex items-center rounded-full bg-red-50 dark:bg-red-900/20 px-2.5 py-0.5 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/20">FAILED</span>
                        @else
                            <span id="status-badge" class="inline-flex items-center rounded-full bg-yellow-50 dark:bg-yellow-900/20 px-2.5 py-0.5 text-xs font-medium text-yellow-700 dark:text-yellow-400 ring-1 ring-inset ring-yellow-600/20">{{ strtoupper($delivery->status) }}</span>
                        @endif
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
