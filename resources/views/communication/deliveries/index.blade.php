@extends('layouts.app')

@section('title', 'Document Deliveries')

@section('content')
<div class="space-y-6" x-data="{ 
    showResendModal: false, 
    deliveryId: null, 
    patientName: '', 
    newPhone: '', 
    actionUrl: '',
    openResendModal(id, phone, name) {
        this.deliveryId = id;
        this.newPhone = phone || '';
        this.patientName = name || '';
        this.actionUrl = '{{ url('communication/deliveries') }}/' + id + '/resend-phone';
        this.showResendModal = true;
    }
}">
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

<style>
    /* Allow patient column to wrap naturally instead of stretching the table */
    .deliveries-table tbody td.col-pasien {
        white-space: normal !important;
    }
</style>
    <!-- Table -->
    <div class="table-container">
        <table class="premium-table deliveries-table">
            <thead>
                <tr>
                    <th class="w-40 whitespace-nowrap">Waktu Kirim</th>
                    <th class="min-w-[200px]">Pasien</th>
                    <th class="whitespace-nowrap">Tipe Dokumen</th>
                    <th class="w-32 whitespace-nowrap">Saluran</th>
                    <th class="w-32 whitespace-nowrap">Status</th>
                    <th class="text-right whitespace-nowrap min-w-[260px]">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $delivery)
                    @php
                        $statusInfo = $delivery->status_info;
                    @endphp
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap text-xs sm:text-sm">
                            {{ $delivery->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="col-pasien">
                            <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $delivery->patient->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                @if(($delivery->channel ?? 'email') === 'whatsapp')
                                    <span>WA: <strong class="font-mono">{{ $delivery->recipient_phone ?? '-' }}</strong></span>
                                @else
                                    <span>Email: {{ $delivery->recipient_email ?? '-' }}</span>
                                @endif
                            </div>
                            @if($delivery->status === 'failed')
                                <div class="text-[11px] text-amber-600 dark:text-amber-400 font-medium flex items-center gap-1 mt-0.5" title="{{ $delivery->error_message }}">
                                    <span class="shrink-0">⚠️</span>
                                    <span class="line-clamp-1 max-w-sm">{{ Str::limit($statusInfo['description'], 45) }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 whitespace-nowrap text-sm">
                            {{ $delivery->documentType->name }}
                        </td>
                        <td class="whitespace-nowrap">
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
                        <td class="whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $statusInfo['class'] }}">
                                {{ $statusInfo['label'] }}
                            </span>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Opsi Kirim Ulang --}}
                                <form action="{{ route('communication.deliveries.resend', $delivery) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                        onclick="return confirmDeliveryResend('{{ addslashes($delivery->patient->name) }}', '{{ $delivery->recipient_phone ?? $delivery->recipient_email }}', '{{ $delivery->status }}', '{{ $delivery->sent_at?->format('d M Y, H:i') }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/40 ring-1 ring-inset ring-sky-600/30 rounded-lg hover:bg-sky-100 dark:hover:bg-sky-900/40 transition-all shadow-xs" 
                                        title="Kirim Ulang Dokumen Sekarang">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        <span>Kirim Ulang</span>
                                    </button>
                                </form>

                                @if($delivery->status === 'failed' && ($delivery->channel ?? 'email') === 'whatsapp')
                                    <button type="button" 
                                        @click="openResendModal({{ $delivery->id }}, '{{ $delivery->recipient_phone }}', '{{ addslashes($delivery->patient->name) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 ring-1 ring-inset ring-amber-600/30 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all shadow-xs"
                                        title="Koreksi Nomor & Kirim Ulang">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                        </svg>
                                        <span>Koreksi<span class="hidden xl:inline"> Nomor</span></span>
                                    </button>
                                @endif

                                @if($delivery->status === 'failed')
                                    <form action="{{ route('communication.deliveries.markAsSent', $delivery) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Tandai pengiriman ini sebagai SENT (Terkirim)?')" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 ring-1 ring-inset ring-emerald-600/20 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-all shadow-xs" title="Tandai Sudah Terkirim">
                                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                            <span><span class="hidden xl:inline">Tandai </span>Terkirim</span>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('communication.deliveries.show', $delivery) }}" class="table-action-primary !px-2.5 !py-1 text-xs">Detail</a>
                            </div>
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

    <!-- Modal Koreksi Nomor & Kirim Ulang -->
    <div x-show="showResendModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showResendModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            @click="showResendModal = false">
        </div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showResendModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700 p-6">
                
                <form :action="actionUrl" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-700 pb-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white" id="modal-title">Koreksi Nomor & Kirim Ulang</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Kirim ulang dokumen yang sama ke nomor WhatsApp yang valid.</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 p-3.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-300 space-y-1">
                        <div>Pasien: <strong class="text-slate-900 dark:text-white" x-text="patientName"></strong></div>
                        <div class="text-[11px] text-slate-500">Dokumen PDF ber-kop & proteksi yang telah dibuat akan langsung dikirimkan ke nomor baru.</div>
                    </div>

                    <div>
                        <label for="modal_new_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nomor WhatsApp Baru *</label>
                        <input type="text" 
                            name="new_phone" 
                            id="modal_new_phone" 
                            x-model="newPhone" 
                            required 
                            placeholder="Contoh: 081355427971" 
                            class="input-field mt-1.5 font-mono text-sm">
                        <p class="text-[11px] text-slate-500 mt-1">Pastikan nomor aktif dan terdaftar di WhatsApp (minimal 10 digit angka).</p>
                    </div>

                    <div class="mt-5 sm:mt-6 flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                        <button type="button" 
                            @click="showResendModal = false" 
                            class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-all">
                            Batal
                        </button>
                        <button type="submit" 
                            class="btn-primary py-2 px-4 text-xs font-semibold flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                            Kirim Ulang Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeliveryResend(patientName, target, status, sentAt) {
    let msg = "";
    if (status === 'sent') {
        msg = "⚠️ PERINGATAN BAHAYA (DANGER)!\n\n" +
              "Dokumen ini SUDAH PERNAH TERKIRIM sebelumnya kepada pasien pada " + (sentAt || 'waktu sebelumnya') + ".\n\n" +
              "Mengirim ulang berisiko membingungkan pasien (" + patientName + ") karena akan menerima dokumen ganda via WhatsApp/Email (" + target + ").\n\n" +
              "Apakah Anda benar-benar yakin ingin TETAP MENGIRIM ULANG dokumen ini sekarang?\n\n" +
              "(Klik OK untuk tetap melanjutkan pengiriman)";
    } else {
        msg = "⚠️ KONFIRMASI PENGIRIMAN ULANG (DANGER / PERINGATAN):\n\n" +
              "Kirim ulang dokumen sekarang ke tujuan: " + target + " (Pasien: " + patientName + ")?\n\n" +
              "Pastikan koneksi WhatsApp Gateway aktif sebelum mengirim.\n\n" +
              "(Klik OK untuk melanjutkan pengiriman)";
    }
    return confirm(msg);
}
</script>
@endsection
