@extends('layouts.app')

@section('title', 'WhatsApp Gateway Status')

@section('content')
<div class="sm:flex sm:items-center mb-6">
    <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">WhatsApp Gateway Status</h1>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-400">Hubungkan dan kelola koneksi WhatsApp Gateway lokal untuk pengiriman pesan otomatis gratis.</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Left Column: Status Card -->
    <div class="lg:col-span-2 space-y-6">
        <div class="overflow-hidden bg-white dark:bg-slate-800 shadow sm:rounded-lg card border-t-4 border-emerald-500">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-slate-200 dark:border-slate-700">
                <div>
                    <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white">Informasi Koneksi</h3>
                    <p class="mt-1 text-xs text-slate-500">Status penyedia pesan aktif: <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ strtoupper($status['active_provider']) }}</span></p>
                </div>
                <div id="status-badge">
                    @if($status['connected'])
                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1.5 text-xs font-semibold text-emerald-800 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Terhubung
                        </span>
                    @else
                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1.5 text-xs font-semibold text-red-800 dark:text-red-400 ring-1 ring-inset ring-red-600/20">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Terputus
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="px-4 py-5 sm:p-6 space-y-6">
                @if($status['active_provider'] !== 'selfhosted')
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4 border-l-4 border-yellow-400">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400">Gateway Mandiri Tidak Aktif</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-500">
                                    <p>Penyedia aktif saat ini diatur ke <strong>{{ $status['active_provider'] }}</strong>. Silakan ubah variabel lingkungan berikut di berkas `.env` untuk mengaktifkan Gateway Mandiri:</p>
                                    <pre class="mt-2 bg-slate-900 text-slate-100 p-2 rounded text-xs">WHATSAPP_PROVIDER=selfhosted</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div id="connection-error" class="hidden rounded-md bg-red-50 dark:bg-red-900/20 p-4 border-l-4 border-red-400">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-red-800 dark:text-red-400">Koneksi Gateway Gagal</h3>
                                <div class="mt-1 text-sm text-red-700 dark:text-red-400" id="error-message">
                                    {{ $status['error'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Connected View -->
                    <div id="connected-panel" class="{{ $status['connected'] ? '' : 'hidden' }} text-center py-8">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                            <svg class="h-10 w-10 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold text-slate-900 dark:text-white">WhatsApp Gateway Terhubung!</h2>
                        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Nomor WhatsApp Anda telah berhasil dipasangkan. Sistem siap mengirim dokumen PDF terproteksi secara otomatis.</p>
                    </div>

                    <!-- Disconnected View / Scan QR -->
                    <div id="disconnected-panel" class="{{ $status['connected'] ? 'hidden' : '' }} flex flex-col items-center py-6">
                        <div class="text-center mb-6">
                            <h2 class="text-md font-semibold text-slate-900 dark:text-white">Pindai QR Code untuk Masuk</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Buka aplikasi WhatsApp di HP Anda -> Perangkat Tertaut -> Tautkan Perangkat</p>
                        </div>
                        
                        <!-- QR Container -->
                        <div class="relative bg-slate-50 p-6 rounded-xl border border-slate-200 shadow-inner flex items-center justify-center w-64 h-64">
                            <!-- QR Image -->
                            <img id="qr-image" src="{{ $status['qr'] ?? '' }}" alt="WhatsApp QR Code" class="{{ $status['qr'] ? '' : 'hidden' }} w-52 h-52">
                            
                            <!-- Loading Spinner -->
                            <div id="qr-loading" class="{{ $status['qr'] ? 'hidden' : '' }} flex flex-col items-center">
                                <svg class="animate-spin h-10 w-10 text-emerald-600 mb-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-xs text-slate-500 font-medium animate-pulse">Menghubungkan ke Client...</span>
                            </div>
                        </div>
                        
                        <p class="mt-6 text-xs text-slate-400 flex items-center gap-1">
                            <svg class="h-4 w-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mendengarkan kode QR baru dari terminal...
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Instructions / Guide -->
    <div class="space-y-6">
        <div class="overflow-hidden bg-white dark:bg-slate-800 shadow sm:rounded-lg card">
            <div class="px-4 py-5 sm:px-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Panduan Penggunaan</h3>
            </div>
            <div class="px-4 py-5 sm:p-6 text-sm text-slate-600 dark:text-slate-400 space-y-4">
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-1">1. Jalankan Gateway</h4>
                    <p class="text-xs">Pastikan terminal backend Node.js berjalan di folder proyek Anda dengan perintah:</p>
                    <pre class="mt-1 bg-slate-950 text-slate-100 p-2 rounded text-xs select-all">cd whatsapp-gateway
node server.js</pre>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-1">2. Pindai Sekali Saja</h4>
                    <p class="text-xs">Sesi masuk akan disimpan di folder lokal. Anda tidak perlu memindai QR Code setiap kali server dinyalakan ulang.</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-1">3. Keamanan</h4>
                    <p class="text-xs">Data sesi disimpan dengan aman secara lokal pada komputer Anda. Pastikan tidak menghapus folder `.wwebjs_auth` agar sesi tidak keluar secara tiba-tiba.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($status['active_provider'] === 'selfhosted')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let GATEWAY_URL = "{{ config('whatsapp.providers.selfhosted.url', 'http://localhost:3000') }}";
        
        // Jika diakses secara remote, arahkan localhost ke IP/hostname server yang sedang aktif
        if (GATEWAY_URL.includes('localhost') || GATEWAY_URL.includes('127.0.0.1')) {
            try {
                const urlObj = new URL(GATEWAY_URL);
                GATEWAY_URL = window.location.protocol + '//' + window.location.hostname + ':' + urlObj.port;
            } catch (e) {
                GATEWAY_URL = window.location.protocol + '//' + window.location.hostname + ':3000';
            }
        }
        
        const statusBadge = document.getElementById('status-badge');
        const connectionError = document.getElementById('connection-error');
        const errorMessage = document.getElementById('error-message');
        const connectedPanel = document.getElementById('connected-panel');
        const disconnectedPanel = document.getElementById('disconnected-panel');
        const qrImage = document.getElementById('qr-image');
        const qrLoading = document.getElementById('qr-loading');

        function checkGatewayStatus() {
            fetch(GATEWAY_URL + '/status')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide any previous connection errors
                    connectionError.classList.add('hidden');
                    
                    if (data.ready) {
                        // Connected State
                        statusBadge.innerHTML = `
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-3 py-1.5 text-xs font-semibold text-emerald-800 dark:text-emerald-400 ring-1 ring-inset ring-emerald-600/20">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Terhubung
                            </span>
                        `;
                        connectedPanel.classList.remove('hidden');
                        disconnectedPanel.classList.add('hidden');
                    } else {
                        // Disconnected State (needs scan)
                        statusBadge.innerHTML = `
                            <span class="inline-flex items-center gap-x-1.5 rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1.5 text-xs font-semibold text-red-800 dark:text-red-400 ring-1 ring-inset ring-red-600/20">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                Terputus
                            </span>
                        `;
                        connectedPanel.classList.add('hidden');
                        disconnectedPanel.classList.remove('hidden');
                        
                        if (data.qr) {
                            qrImage.src = data.qr;
                            qrImage.classList.remove('hidden');
                            qrLoading.classList.add('hidden');
                        } else {
                            qrImage.classList.add('hidden');
                            qrLoading.classList.remove('hidden');
                        }
                    }
                })
                .catch(error => {
                    // Gateway server is offline or unreachable
                    statusBadge.innerHTML = `
                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1.5 text-xs font-semibold text-red-800 dark:text-red-400 ring-1 ring-inset ring-red-600/20">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Offline
                        </span>
                    `;
                    connectedPanel.classList.add('hidden');
                    disconnectedPanel.classList.add('hidden');
                    connectionError.classList.remove('hidden');
                    errorMessage.textContent = 'Tidak dapat terhubung ke WhatsApp Gateway di ' + GATEWAY_URL + '. Silakan jalankan "node server.js" di folder "whatsapp-gateway" untuk mengaktifkan.';
                });
        }

        // Poll status every 3 seconds
        checkGatewayStatus();
        setInterval(checkGatewayStatus, 3000);
    });
</script>
@endif
@endsection
