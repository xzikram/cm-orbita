@extends('layouts.app')

@section('title', 'WhatsApp Gateway Status')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div>
            <h1 class="page-header-title">WhatsApp Gateway Status</h1>
            <p class="page-header-desc">Hubungkan dan kelola koneksi WhatsApp Gateway lokal untuk pengiriman pesan otomatis gratis.</p>
        </div>
    </div>

<div class="grid grid-cols-1 gap-6">
    <!-- Status Card -->
    <div class="space-y-6">
        <div class="overflow-hidden bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm shadow-sm sm:rounded-2xl ring-1 ring-slate-900/[0.04] dark:ring-white/[0.06] border-t-4 border-emerald-500">
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
                        
                        <!-- Reset Button -->
                        <div class="mt-6">
                            <button id="btn-reset-session" class="inline-flex items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 transition duration-150 ease-in-out">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Putuskan & Reset Koneksi
                            </button>
                        </div>
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

                        <!-- Reset Button Disconnected (if session gets stuck) -->
                        <div class="mt-4">
                            <button id="btn-reset-session-dc" class="inline-flex items-center gap-x-1.5 text-xs font-semibold text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400 transition duration-150 ease-in-out">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Reset Sesi yang Macet
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
 
@if($status['active_provider'] === 'selfhosted')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientId = 'user-{{ Auth::id() }}';
        // Menggunakan reverse proxy Nginx agar tidak perlu membuka port 3000 ke publik
        let GATEWAY_URL = window.location.origin + '/whatsapp-api';
        
        const statusBadge = document.getElementById('status-badge');
        const connectionError = document.getElementById('connection-error');
        const errorMessage = document.getElementById('error-message');
        const connectedPanel = document.getElementById('connected-panel');
        const disconnectedPanel = document.getElementById('disconnected-panel');
        const qrImage = document.getElementById('qr-image');
        const qrLoading = document.getElementById('qr-loading');
 
        function checkGatewayStatus() {
            fetch(GATEWAY_URL + '/status?clientId=' + clientId)
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
 
        // Function to reset session
        function resetSession() {
            if (!confirm('Apakah Anda yakin ingin memutuskan koneksi WhatsApp ini? Anda harus memindai QR Code baru lagi.')) {
                return;
            }
            
            const btn = document.getElementById('btn-reset-session');
            const btnDc = document.getElementById('btn-reset-session-dc');
            if (btn) btn.disabled = true;
            if (btnDc) btnDc.disabled = true;
 
            fetch(GATEWAY_URL + '/reset-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ clientId: clientId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Sesi koneksi WhatsApp berhasil direset. Halaman akan dimuat ulang.');
                    window.location.reload();
                } else {
                    alert('Gagal mereset sesi: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error resetting session:', error);
                alert('Tidak dapat menghubungi gateway untuk mereset sesi.');
            })
            .finally(() => {
                if (btn) btn.disabled = false;
                if (btnDc) btnDc.disabled = false;
            });
        }
 
        const btnReset = document.getElementById('btn-reset-session');
        const btnResetDc = document.getElementById('btn-reset-session-dc');
        if (btnReset) btnReset.addEventListener('click', resetSession);
        if (btnResetDc) btnResetDc.addEventListener('click', resetSession);
 
        // Poll status every 3 seconds
        checkGatewayStatus();
        setInterval(checkGatewayStatus, 3000);
    });
</script>
@endif
@endsection
