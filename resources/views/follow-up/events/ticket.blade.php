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
                    <div id="ticket-qrcode-container" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center" style="position: relative; display: inline-block;">
                        <img src="{{ $qrcodeBase64 }}" alt="QR Code" style="width: 128px; height: 128px; display: block;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 28px; height: 28px; background: white; display: flex; align-items: center; justify-content: center; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                            <img src="/jec-logo.png" style="width: 22px; height: auto;">
                        </div>
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

                <!-- Screenshot Optimization Button -->
                <div class="pt-2">
                    <button id="open-screenshot-btn" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md flex items-center justify-center gap-2 transition-all">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Optimasi & Simpan Tiket (Screenshot)
                    </button>
                </div>

                <!-- Print/Download Notice -->
                <div class="text-center text-[10px] text-slate-400">
                    <p>💡 Tip: Gunakan tombol di atas untuk mempermudah pemindaian barcode saat di lokasi event.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Screenshot Optimized Overlay -->
    <div id="screenshot-overlay" class="fixed inset-0 z-50 hidden bg-slate-950 flex flex-col justify-between p-6 select-none" style="font-family: 'Inter', sans-serif;">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <img src="/Logo RS JEC ORBITA.png" onerror="this.src='/logo.png'" style="height: 32px; object-fit: contain;">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">JEC ORBITA @ MAKASSAR</span>
            </div>
            <button id="close-screenshot-btn" class="bg-slate-800/80 text-white rounded-full p-2 hover:bg-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Ticket Body (High Contrast) -->
        <div class="bg-white rounded-3xl p-6 text-center space-y-4 my-auto shadow-2xl relative overflow-hidden">
            <!-- Ticket stub cuts -->
            <div class="absolute top-[35%] -left-3 h-6 w-6 rounded-full bg-slate-950"></div>
            <div class="absolute top-[35%] -right-3 h-6 w-6 rounded-full bg-slate-950"></div>

            <div class="border-b border-dashed border-slate-200 pb-4">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TIKET MASUK EVENT</span>
                <span class="text-2xl font-black text-[#1b4e80] tracking-tight block mt-1">{{ $event->name }}</span>
                <span class="text-xs text-slate-500 font-semibold block mt-0.5">{{ $event->location }} - {{ $event->event_date->format('d M Y') }}</span>
            </div>

            <div class="py-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NOMOR ANTREAN</span>
                <span class="text-5xl font-black text-emerald-600 tracking-tight block my-1">{{ $queueCode }}</span>
            </div>

            <!-- Barcodes -->
            <div class="flex flex-col items-center justify-center space-y-3 py-2 bg-slate-50 rounded-2xl p-4 border border-slate-100">
                <!-- QR Code Container -->
                <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm flex items-center justify-center relative">
                    <img src="{{ $qrcodeBase64 }}" alt="QR Code" style="width: 140px; height: 140px; display: block;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 28px; height: 28px; background: white; display: flex; align-items: center; justify-content: center; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                        <img src="/jec-logo.png" style="width: 22px; height: auto;">
                    </div>
                </div>

                <!-- 1D Barcode Container -->
                <div class="flex flex-col items-center">
                    <svg id="screenshot-barcode" class="max-w-full"></svg>
                    <span class="text-[10px] font-mono text-slate-600 mt-1 font-bold">{{ $patient->medical_record_number }}</span>
                </div>
            </div>

            <!-- Bio details -->
            <div class="text-left text-xs space-y-1.5 border-t border-slate-100 pt-3">
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Nama Pasien:</span>
                    <span class="font-bold text-slate-800">{{ $patient->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">No. RM Sementara:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $patient->medical_record_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">WhatsApp:</span>
                    <span class="font-bold text-slate-800">{{ $patient->phone }}</span>
                </div>
            </div>
        </div>

        <!-- Footer action tips -->
        <div class="text-center text-[10px] text-slate-400 space-y-1 mt-auto">
            <p class="font-semibold text-emerald-400">✨ Silakan ambil Tangkapan Layar (Screenshot) halaman ini</p>
            <p>Tunjukkan screenshot ini ke petugas saat melakukan admisi di rumah sakit.</p>
        </div>
    </div>

    <!-- Scanner CDN Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mrn = "{{ $patient->medical_record_number }}";

            // Generate 1D Barcodes
            function drawBarcodes() {
                JsBarcode("#ticket-barcode", mrn, {
                    format: "CODE128",
                    width: 1.5,
                    height: 45,
                    displayValue: false,
                    lineColor: "#0f172a",
                    background: "transparent"
                });

                JsBarcode("#screenshot-barcode", mrn, {
                    format: "CODE128",
                    width: 1.8,
                    height: 50,
                    displayValue: false,
                    lineColor: "#0f172a",
                    background: "transparent"
                });
            }

            drawBarcodes();

            // Screenshot Mode Toggles
            const openBtn = document.getElementById('open-screenshot-btn');
            const closeBtn = document.getElementById('close-screenshot-btn');
            const overlay = document.getElementById('screenshot-overlay');

            if (openBtn && closeBtn && overlay) {
                openBtn.addEventListener('click', function() {
                    overlay.classList.remove('hidden');
                });

                closeBtn.addEventListener('click', function() {
                    overlay.classList.add('hidden');
                });
            }
        });
    </script>
</x-guest-layout>
