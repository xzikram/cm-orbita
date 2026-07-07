<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Admisi Scanner - RS JEC ORBITA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Load styles and scripts via Vite to support offline local execution -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }
        .safe-top {
            padding-top: env(safe-area-inset-top);
        }
        /* Custom animated checkmark */
        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 2;
            stroke: #10b981;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #10b981;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out 0s both;
            position: relative;
            margin: 0 auto;
        }
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke-miterlimit: 10;
            stroke: #10b981;
            fill: none;
            animation: stroke .6s cubic-bezier(0.650, 0.000, 0.450, 1.000) forwards;
        }
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke .3s cubic-bezier(0.650, 0.000, 0.450, 1.000) .8s forwards;
        }
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }
        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 40px #10b981; }
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950 flex flex-col justify-between overflow-hidden">
    
    <!-- Top Header -->
    <header class="bg-slate-900/90 backdrop-blur-md border-b border-slate-800 px-6 py-4 safe-top flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <div class="bg-primary-600/10 p-2 rounded-xl border border-primary-500/20">
                <!-- Explicit size added to prevent inflation if CSS load latency occurs -->
                <svg class="h-5 w-5 text-primary-500" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16.01h.01" />
                </svg>
            </div>
            <div>
                <h1 class="text-sm font-extrabold tracking-tight text-white leading-none">Scanner Admisi</h1>
                <span class="text-[10px] font-semibold text-slate-400">RS JEC ORBITA @ MAKASSAR</span>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="text-xs bg-slate-850 hover:bg-slate-700 px-3 py-1.5 rounded-lg border border-slate-700 font-semibold transition-colors">
                Dashboard
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto px-4 py-4 space-y-4">
        
        <!-- Tab 1: Scanner -->
        <div id="tab-scanner-content" class="space-y-4">
            <!-- Camera Preview Frame -->
            <div class="relative bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl aspect-[4/3] flex flex-col items-center justify-center">
                <div id="scanner-view" class="w-full h-full object-cover"></div>
                
                <!-- Overlay scan target scanner frame -->
                <div id="scanner-frame-overlay" class="absolute inset-0 border-4 border-transparent flex items-center justify-center pointer-events-none">
                    <div class="w-48 h-48 border-2 border-dashed border-primary-500/80 rounded-2xl relative">
                        <!-- Corners indicators -->
                        <div class="absolute -top-1 -left-1 w-5 h-5 border-t-4 border-l-4 border-primary-500 rounded-tl-lg"></div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 border-t-4 border-r-4 border-primary-500 rounded-tr-lg"></div>
                        <div class="absolute -bottom-1 -left-1 w-5 h-5 border-b-4 border-l-4 border-primary-500 rounded-bl-lg"></div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 border-b-4 border-r-4 border-primary-500 rounded-br-lg"></div>
                        <!-- Scanning line animation -->
                        <div class="absolute inset-x-2 top-0 h-0.5 bg-primary-500 shadow-[0_0_10px_rgba(56,165,246,0.8)] animate-pulse" style="animation: scanLine 2s infinite linear;"></div>
                    </div>
                </div>

                <!-- Camera off / loading state -->
                <div id="scanner-loading" class="absolute inset-0 bg-slate-950/90 flex flex-col items-center justify-center space-y-3 p-6 text-center">
                    <div class="h-10 w-10 border-4 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-semibold text-slate-300">Menghubungkan kamera perangkat...</p>
                </div>
            </div>

            <!-- Fallback manual input -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg space-y-3">
                <div class="flex items-center justify-between">
                    <label for="manual-barcode" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Input Manual RM Sementara</label>
                    <span class="text-[10px] text-slate-500 font-semibold">Gunakan jika kamera bermasalah</span>
                </div>
                <form id="manual-scan-form" class="flex gap-2">
                    <input type="text" id="manual-barcode" placeholder="Contoh: TEMP-20260707..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary-500 font-mono">
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white font-bold text-sm px-5 rounded-xl transition-all">
                        Cek
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab 2: Logs Kehadiran Hari Ini -->
        <div id="tab-logs-content" class="hidden space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendaftar Event Hadir Hari Ini</span>
                <span class="bg-emerald-500/10 text-emerald-500 text-xs font-bold px-2 py-0.5 rounded-full border border-emerald-500/20" id="logs-count">
                    {{ count($arrivedToday) }} Orang
                </span>
            </div>

            <div class="space-y-2" id="logs-list-container">
                @forelse($arrivedToday as $p)
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex justify-between items-center shadow-md">
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white leading-tight">{{ $p->name }}</h4>
                            <p class="text-xs text-slate-400 font-semibold font-mono">{{ $p->medical_record_number }}</p>
                            <span class="inline-block text-[9px] bg-slate-800 text-primary-400 border border-slate-700/50 px-2 py-0.5 rounded-md font-semibold">
                                {{ $p->event ? $p->event->name : '-' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-500 block">Hadir</span>
                            <span class="text-[10px] text-slate-500 font-medium">
                                {{ $p->hospital_arrival_at->timezone(config('app.timezone', 'Asia/Makassar'))->format('H:i') }} WITA
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-900/50 border border-slate-800/50 border-dashed rounded-2xl py-12 text-center text-slate-500" id="logs-empty-state">
                        <svg class="mx-auto h-8 w-8 text-slate-600 mb-2" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-xs">Belum ada pasien event yang check-in hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Success Modal Overlay -->
    <div id="success-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-sm text-center shadow-2xl space-y-6">
            
            <!-- Animated Checkmark -->
            <div class="py-4">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>

            <!-- Success Content -->
            <div class="space-y-2">
                <h3 class="text-lg font-extrabold text-white" id="success-title">Check-in Sukses!</h3>
                <p class="text-sm text-slate-400" id="success-message">Pasien telah berhasil terdaftar kedatangannya.</p>
            </div>

            <!-- Patient Detail Card -->
            <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-4 text-left text-xs space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Nama:</span>
                    <span class="font-extrabold text-white" id="success-patient-name">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">RM Sementara:</span>
                    <span class="font-mono font-bold text-white" id="success-patient-rm">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Event:</span>
                    <span class="font-semibold text-primary-400" id="success-patient-event">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Check-in Jam:</span>
                    <span class="font-bold text-emerald-500" id="success-patient-time">-</span>
                </div>
            </div>

            <!-- Dismiss Button -->
            <div>
                <button id="dismiss-success-btn" class="w-full py-3 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white font-bold text-sm rounded-xl transition-all">
                    Siap Memindai Lagi
                </button>
            </div>
        </div>
    </div>

    <!-- Error Modal / Toast Overlay -->
    <div id="error-toast" class="fixed top-4 inset-x-4 z-50 hidden flex justify-center pointer-events-none">
        <div class="bg-red-500 text-white rounded-2xl px-5 py-4 shadow-2xl flex items-center gap-3 max-w-sm pointer-events-auto border border-red-400/20">
            <svg class="h-6 w-6 text-white shrink-0" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <h4 class="text-xs font-extrabold uppercase tracking-wider opacity-85">Gagal Check-in</h4>
                <p class="text-xs font-semibold mt-0.5 leading-snug" id="error-toast-message">Kode barcode tidak valid atau tidak terdaftar.</p>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bg-slate-900/90 backdrop-blur-md border-t border-slate-800 px-6 pt-3 pb-6 safe-bottom flex justify-around items-center z-10">
        <!-- Tab Button Scanner -->
        <button id="tab-scanner-btn" class="flex flex-col items-center gap-1 text-emerald-500 font-bold transition-all">
            <svg class="h-6 w-6" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span class="text-[10px] tracking-wide">Pindai Tiket</span>
        </button>

        <!-- Tab Button Logs -->
        <button id="tab-logs-btn" class="flex flex-col items-center gap-1 text-slate-500 font-bold hover:text-slate-400 transition-all">
            <svg class="h-6 w-6" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[10px] tracking-wide">Kehadiran</span>
        </button>
    </nav>

    <!-- Scan Sound simulation Beep (Audio synthesis API) -->
    <script>
        function playBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(1000, audioCtx.currentTime); // 1000Hz frequency
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);
                
                oscillator.start();
                setTimeout(() => {
                    oscillator.stop();
                    audioCtx.close();
                }, 100);
            } catch (e) {
                console.log("Audio synth failed", e);
            }
        }
    </script>

    <!-- HTML5-QRCode Scanner Library (Local) -->
    <script src="{{ asset('vendor/html5-qrcode.min.js') }}"></script>
    
    <script>
        let html5Qrcode = null;
        let isProcessing = false;

        document.addEventListener('DOMContentLoaded', function() {
            // Setup Tab toggling
            const scannerBtn = document.getElementById('tab-scanner-btn');
            const logsBtn = document.getElementById('tab-logs-btn');
            
            const scannerContent = document.getElementById('tab-scanner-content');
            const logsContent = document.getElementById('tab-logs-content');

            scannerBtn.addEventListener('click', function() {
                scannerBtn.classList.add('text-emerald-500');
                scannerBtn.classList.remove('text-slate-500');
                logsBtn.classList.remove('text-emerald-500');
                logsBtn.classList.add('text-slate-500');

                scannerContent.classList.remove('hidden');
                logsContent.classList.add('hidden');
                
                // Restart scanner if tab changed
                startCamera();
            });

            logsBtn.addEventListener('click', function() {
                logsBtn.classList.add('text-emerald-500');
                logsBtn.classList.remove('text-slate-500');
                scannerBtn.classList.remove('text-emerald-500');
                scannerBtn.classList.add('text-slate-500');

                logsContent.classList.remove('hidden');
                scannerContent.classList.add('hidden');

                // Stop scanner to save power
                stopCamera();
            });

            // Initialize Camera Scanner
            function startCamera() {
                if (html5Qrcode && html5Qrcode.isScanning) {
                    return;
                }

                document.getElementById('scanner-loading').classList.remove('hidden');
                
                // Instance of Html5Qrcode on scanner div
                html5Qrcode = new Html5Qrcode("scanner-view");
                
                Html5Qrcode.getCameras().then(devices => {
                    document.getElementById('scanner-loading').classList.add('hidden');
                    if (devices && devices.length > 0) {
                        // Prefer back camera
                        let backCamera = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('rear') || d.label.toLowerCase().includes('environment'));
                        let cameraId = backCamera ? backCamera.id : devices[0].id;
                        
                        html5Qrcode.start(
                            cameraId, 
                            {
                                fps: 15,
                                qrbox: { width: 220, height: 220 }
                            },
                            (decodedText, decodedResult) => {
                                // Scanned text callback
                                onBarcodeScanned(decodedText);
                            },
                            (errorMessage) => {
                                // silent verbose errors
                            }
                        ).catch(err => {
                            console.error("Gagal memulai scanner", err);
                        });
                    } else {
                        document.getElementById('scanner-loading').innerHTML = `
                            <p class="text-xs text-red-500 font-bold">Kamera tidak ditemukan. Harap gunakan input manual.</p>
                        `;
                    }
                }).catch(err => {
                    document.getElementById('scanner-loading').innerHTML = `
                        <p class="text-xs text-red-500 font-bold">Izin kamera ditolak. Harap gunakan input manual.</p>
                    `;
                });
            }

            function stopCamera() {
                if (html5Qrcode && html5Qrcode.isScanning) {
                    html5Qrcode.stop().then(() => {
                        console.log("Scanner stopped.");
                    }).catch(err => console.error("Error stopping scanner", err));
                }
            }

            // Start camera initially
            startCamera();

            // Handle Barcode Submission
            function onBarcodeScanned(barcode) {
                if (isProcessing) return;
                isProcessing = true;
                playBeep();

                // Call AJAX Check-In
                fetch("{{ route('admission.check-in') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ barcode: barcode })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    if (status === 200 && body.success) {
                        showSuccessModal(body.patient);
                        updateLogsList(body.patient);
                    } else {
                        showErrorToast(body.message || "Gagal check-in.");
                        setTimeout(() => { isProcessing = false; }, 2000); // cooldown
                    }
                })
                .catch(err => {
                    console.error("AJAX Error", err);
                    showErrorToast("Terjadi kesalahan koneksi server.");
                    setTimeout(() => { isProcessing = false; }, 2000);
                });
            }

            // Manual Submit Form
            document.getElementById('manual-scan-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const manualInput = document.getElementById('manual-barcode');
                const val = manualInput.value.trim();
                if (val !== "") {
                    onBarcodeScanned(val);
                    manualInput.value = "";
                }
            });

            // Modal Toggles
            const successModal = document.getElementById('success-modal');
            const dismissBtn = document.getElementById('dismiss-success-btn');

            function showSuccessModal(patient) {
                document.getElementById('success-patient-name').innerText = patient.name;
                document.getElementById('success-patient-rm').innerText = patient.medical_record_number;
                document.getElementById('success-patient-event').innerText = patient.event_name;
                document.getElementById('success-patient-time').innerText = patient.hospital_arrival_at;
                
                if (patient.already_checked_in) {
                    document.getElementById('success-title').innerText = "Check-in Ulang";
                    document.getElementById('success-title').className = "text-lg font-extrabold text-amber-500";
                    document.getElementById('success-message').innerText = "Pasien ini sudah check-in hari ini.";
                } else {
                    document.getElementById('success-title').innerText = "Check-in Sukses!";
                    document.getElementById('success-title').className = "text-lg font-extrabold text-white";
                    document.getElementById('success-message').innerText = "Pasien berhasil dikonfirmasi kehadirannya.";
                }

                successModal.classList.remove('hidden');
            }

            dismissBtn.addEventListener('click', function() {
                successModal.classList.add('hidden');
                isProcessing = false; // clear processing state
            });

            // Toast Toggle
            const errorToast = document.getElementById('error-toast');
            const errorToastMessage = document.getElementById('error-toast-message');

            function showErrorToast(msg) {
                errorToastMessage.innerText = msg;
                errorToast.classList.remove('hidden');
                setTimeout(() => {
                    errorToast.classList.add('hidden');
                }, 3000);
            }

            // Real-time Update Kehadiran logs HTML list
            function updateLogsList(patient) {
                // If patient check-in is not a duplicate, update count and list
                if (patient.already_checked_in) return;

                const emptyState = document.getElementById('logs-empty-state');
                if (emptyState) {
                    emptyState.remove();
                }

                const logsCountSpan = document.getElementById('logs-count');
                let countVal = parseInt(logsCountSpan.innerText) || 0;
                logsCountSpan.innerText = (countVal + 1) + " Orang";

                const logItem = `
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex justify-between items-center shadow-md animate-pulse">
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-white leading-tight">${patient.name}</h4>
                            <p class="text-xs text-slate-400 font-semibold font-mono">${patient.medical_record_number}</p>
                            <span class="inline-block text-[9px] bg-slate-800 text-primary-400 border border-slate-700/50 px-2 py-0.5 rounded-md font-semibold">
                                ${patient.event_name}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-500 block">Hadir</span>
                            <span class="text-[10px] text-slate-500 font-medium">
                                ${patient.hospital_arrival_at.split(' ').slice(-2, -1)[0] || 'Just now'} WITA
                            </span>
                        </div>
                    </div>
                `;

                const listContainer = document.getElementById('logs-list-container');
                listContainer.insertAdjacentHTML('afterbegin', logItem);
            }
        });

        // Keyframe animations via script injection
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes scanLine {
                0% { top: 0%; opacity: 0.8; }
                50% { top: 100%; opacity: 1; }
                100% { top: 0%; opacity: 0.8; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
