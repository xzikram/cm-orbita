<x-guest-layout>
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        
        <!-- Ticket Stub Container -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-2xl relative">
            
            <!-- Ticket Header -->
            <div class="bg-gradient-to-r from-primary-600 to-indigo-600 p-6 text-center text-white relative">
                <span class="text-xs font-semibold tracking-widest uppercase opacity-75">TANDA MASUK EVENT</span>
                <h2 class="text-xl font-black mt-1 leading-tight">{{ $event->name }}</h2>
                <p class="text-xs opacity-75 mt-1">Lokasi: {{ $event->location }}</p>
                
                <!-- Ticket Notch Left -->
                <div class="absolute -bottom-3 -left-3 h-6 w-6 rounded-full bg-slate-50 dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800"></div>
                <!-- Ticket Notch Right -->
                <div class="absolute -bottom-3 -right-3 h-6 w-6 rounded-full bg-slate-50 dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800"></div>
            </div>

            <!-- Queue Code Section -->
            <div class="p-6 text-center border-b border-dashed border-slate-200 dark:border-slate-800 relative bg-slate-50/50 dark:bg-slate-800/20">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Nomor Antrean Anda</span>
                <span class="text-5xl font-black text-primary-600 dark:text-primary-400 tracking-tight block my-2">
                    {{ $queueCode }}
                </span>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Tunjukkan nomor ini ke petugas di lokasi.</span>
            </div>

            <!-- Scanner & Patient Info Section -->
            <div class="p-6 space-y-6">
                <!-- Barcode & QR Code Section -->
                <div class="flex flex-col items-center justify-center space-y-4">
                    <!-- QR Code Container -->
                    <div id="ticket-qrcode-container" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center">
                        <div id="ticket-qrcode"></div>
                    </div>

                    <!-- 1D Barcode Container -->
                    <div class="flex flex-col items-center">
                        <svg id="ticket-barcode" class="max-w-full"></svg>
                        <span class="text-[10px] font-mono text-slate-400 mt-1 uppercase">{{ $patient->medical_record_number }}</span>
                    </div>
                </div>

                <!-- Patient Bio Details -->
                <div class="bg-slate-50 dark:bg-slate-800/40 rounded-2xl p-4 space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-medium">Nama Pasien</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $patient->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-medium">No. RM Sementara</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $patient->medical_record_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-medium">Nomor WhatsApp</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $patient->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-medium">Tanggal Lahir</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $patient->date_of_birth->format('d M Y') }}</span>
                    </div>
                </div>

                <!-- Print/Download Notice -->
                <div class="text-center text-[10px] text-slate-400 space-y-1">
                    <p>💡 Tip: Ambil tangkapan layar (screenshot) halaman ini sebagai bukti antrean Anda di lokasi.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner CDN Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mrn = "{{ $patient->medical_record_number }}";

            // 1. Generate 2D QR Code (contains RM number)
            new QRCode(document.getElementById("ticket-qrcode"), {
                text: mrn,
                width: 120,
                height: 120,
                colorDark : "#0f172a",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.M
            });

            // 2. Generate 1D Barcode (standard Code128 encoding)
            JsBarcode("#ticket-barcode", mrn, {
                format: "CODE128",
                width: 1.5,
                height: 45,
                displayValue: false,
                lineColor: "#0f172a",
                background: "transparent"
            });
        });
    </script>
</x-guest-layout>
