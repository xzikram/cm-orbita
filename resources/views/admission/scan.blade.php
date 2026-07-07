<!DOCTYPE html>
<html lang="id" class="h-full dark">
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
        .spin-loader {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-100 bg-slate-950 flex flex-col justify-between overflow-hidden" style="background: #090f1d !important; color: #f1f5f9 !important;">
    
    <!-- Top Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-3 safe-top flex items-center justify-between z-10" style="background-color: #ffffff !important; border-bottom: 1px solid #e2e8f0 !important; padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 10;">
        <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 12px;">
            <!-- Logo directly on the white background -->
            <img src="{{ asset('Logo RS JEC ORBITA.png') }}" style="height: 28px; width: auto; object-fit: contain;" alt="JEC ORBITA Logo">
            
            <!-- Vertical Divider -->
            <div style="height: 20px; width: 1px; background-color: #cbd5e1;"></div>
            
            <!-- Title -->
            <h1 class="text-sm font-bold tracking-tight text-slate-800" style="color: #1e293b !important; font-size: 12px; font-weight: 700; margin: 0; line-height: 1; text-transform: uppercase; letter-spacing: 0.05em;">Scanner Admisi</h1>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 active:scale-95 text-white font-semibold transition-all" style="background-color: #0f172a; color: white !important; padding: 6px 14px; border-radius: 8px; font-size: 11px; text-decoration: none; font-weight: 600; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); display: inline-block;">
                Dashboard
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto px-4 py-4 space-y-4" style="padding: 16px; display: flex; flex-direction: column; gap: 16px; overflow-y: auto;">
        
        <!-- Tab 1: Scanner -->
        <div id="tab-scanner-content" class="space-y-4" style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Camera Preview Frame -->
            <div class="relative bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl aspect-[4/3] flex flex-col items-center justify-center" style="min-height: 280px; width: 100%; max-width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #0f172a; border: 1px solid #1e293b; border-radius: 24px; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
                <div id="scanner-view" class="w-full h-full object-cover" style="width: 100%; height: 100%; min-height: 280px; border-radius: 24px; overflow: hidden;"></div>
                
                <!-- Overlay scan target scanner frame -->
                <div id="scanner-frame-overlay" class="absolute inset-0 border-4 border-transparent flex items-center justify-center pointer-events-none" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; border: 4px solid transparent;">
                    <div class="w-48 h-48 border-2 border-dashed border-primary-500/80 rounded-2xl relative" style="width: 192px; height: 192px; border: 2px dashed rgba(16, 185, 129, 0.8); border-radius: 16px; position: relative;">
                        <!-- Corners indicators -->
                        <div class="absolute -top-1 -left-1 w-5 h-5 border-t-4 border-l-4 border-primary-500 rounded-tl-lg" style="position: absolute; top: -4px; left: -4px; width: 20px; height: 20px; border-top: 4px solid #10b981; border-left: 4px solid #10b981; border-top-left-radius: 8px;"></div>
                        <div class="absolute -top-1 -right-1 w-5 h-5 border-t-4 border-r-4 border-primary-500 rounded-tr-lg" style="position: absolute; top: -4px; right: -4px; width: 20px; height: 20px; border-top: 4px solid #10b981; border-right: 4px solid #10b981; border-top-right-radius: 8px;"></div>
                        <div class="absolute -bottom-1 -left-1 w-5 h-5 border-b-4 border-l-4 border-primary-500 rounded-bl-lg" style="position: absolute; bottom: -4px; left: -4px; width: 20px; height: 20px; border-bottom: 4px solid #10b981; border-left: 4px solid #10b981; border-bottom-left-radius: 8px;"></div>
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 border-b-4 border-r-4 border-primary-500 rounded-br-lg" style="position: absolute; bottom: -4px; right: -4px; width: 20px; height: 20px; border-bottom: 4px solid #10b981; border-right: 4px solid #10b981; border-bottom-right-radius: 8px;"></div>
                        <!-- Scanning line animation -->
                        <div class="absolute inset-x-2 top-0 h-0.5 bg-primary-500 shadow-[0_0_10px_rgba(56,165,246,0.8)]" style="position: absolute; left: 8px; right: 8px; top: 0; height: 2px; background-color: #10b981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.8);"></div>
                    </div>
                </div>

                <!-- Camera off / loading state -->
                <div id="scanner-loading" class="absolute inset-0 bg-slate-950/90 flex flex-col items-center justify-center space-y-3 p-6 text-center" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(9, 15, 29, 0.95); display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 24px; padding: 24px; text-align: center;">
                    <div class="h-10 w-10 border-4 border-primary-500 border-t-transparent rounded-full spin-loader" style="width: 40px; height: 40px; border: 4px solid #10b981; border-top: 4px solid transparent; border-radius: 50%;"></div>
                    <p class="text-sm font-semibold text-slate-300" style="color: #cbd5e1; font-size: 14px; margin-top: 12px; font-weight: 600;">Menghubungkan kamera perangkat...</p>
                </div>
            </div>

            <!-- Fallback manual input -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-lg space-y-3" style="background-color: #111827; border: 1px solid #1f2937; border-radius: 16px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
                <div class="flex items-center justify-between" style="display: flex; justify-content: space-between; align-items: center;">
                    <label for="manual-barcode" class="text-xs font-bold text-slate-400 uppercase tracking-wider block" style="font-size: 12px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Input Manual RM Sementara</label>
                    <span class="text-[10px] text-slate-500 font-semibold" style="font-size: 10px; color: #6b7280; font-weight: 600;">Gunakan jika kamera bermasalah</span>
                </div>
                <form id="manual-scan-form" class="flex gap-2" style="display: flex; gap: 8px;">
                    <input type="text" id="manual-barcode" placeholder="Contoh: TEMP-20260707..." class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary-500 font-mono" style="flex: 1; background-color: #030712; border: 1px solid #1f2937; border-radius: 12px; padding: 12px 16px; font-size: 14px; color: white; font-family: monospace; outline: none;">
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 active:scale-95 text-white font-bold text-sm px-5 rounded-xl transition-all" style="background-color: #10b981; color: white; font-weight: 700; font-size: 14px; padding: 0 20px; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s;">
                        Cek
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab 2: Logs Kehadiran Hari Ini -->
        <div id="tab-logs-content" class="hidden space-y-4" style="display: none; flex-direction: column; gap: 16px;">
            <div class="flex items-center justify-between" style="display: flex; justify-content: space-between; align-items: center;">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider" style="font-size: 12px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Pendaftar Event Hadir Hari Ini</span>
                <span class="bg-emerald-500/10 text-emerald-500 text-xs font-bold px-2 py-0.5 rounded-full border border-emerald-500/20" id="logs-count" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.2);">
                    {{ count($arrivedToday) }} Orang
                </span>
            </div>

            <div class="space-y-2" id="logs-list-container" style="display: flex; flex-direction: column; gap: 8px;">
                @forelse($arrivedToday as $p)
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex justify-between items-center shadow-md" style="background-color: #111827; border: 1px solid #1f2937; border-radius: 16px; padding: 16px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        <div class="space-y-1" style="display: flex; flex-direction: column; gap: 4px;">
                            <h4 class="text-sm font-bold text-white leading-tight" style="font-size: 14px; font-weight: 700; color: white; margin: 0;">{{ $p->name }}</h4>
                            <p class="text-xs text-slate-400 font-semibold font-mono" style="font-size: 12px; color: #9ca3af; font-family: monospace; margin: 0;">{{ $p->medical_record_number }}</p>
                            <span class="inline-block text-[9px] bg-slate-800 text-primary-400 border border-slate-700/50 px-2 py-0.5 rounded-md font-semibold" style="display: inline-block; font-size: 9px; background-color: #1f2937; color: #10b981; border: 1px solid rgba(31, 41, 55, 0.5); padding: 2px 8px; border-radius: 6px; font-weight: 600;">
                                {{ $p->event ? $p->event->name : '-' }}
                            </span>
                        </div>
                        <div class="text-right" style="text-align: right;">
                            <span class="text-xs font-bold text-emerald-500 block" style="font-size: 12px; font-weight: 700; color: #10b981; display: block;">Hadir</span>
                            <span class="text-[10px] text-slate-500 font-medium" style="font-size: 10px; color: #6b7280; font-weight: 500;">
                                {{ $p->hospital_arrival_at->timezone(config('app.timezone', 'Asia/Makassar'))->format('H:i') }} WITA
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-900/50 border border-slate-800/50 border-dashed rounded-2xl py-12 text-center text-slate-500" id="logs-empty-state" style="background-color: rgba(17, 24, 39, 0.5); border: 1px dashed rgba(31, 41, 55, 0.5); border-radius: 16px; padding: 48px 0; text-align: center; color: #6b7280;">
                        <svg class="mx-auto h-8 w-8 text-slate-600 mb-2" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#4b5563">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-xs" style="font-size: 12px; margin-top: 8px;">Belum ada pasien event yang check-in hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Success Modal Overlay -->
    <div id="success-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-6" style="position: fixed; inset: 0; z-index: 50; display: none; background-color: rgba(3, 7, 18, 0.8); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 24px;">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 w-full max-w-sm text-center shadow-2xl space-y-6" style="background-color: #111827; border: 1px solid #1f2937; border-radius: 24px; padding: 24px; width: 100%; max-width: 384px; text-align: center; display: flex; flex-direction: column; gap: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.55);">
            
            <!-- Animated Checkmark -->
            <div class="py-2" style="padding: 8px 0;">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" style="width: 80px; height: 80px; display: block; margin: 0 auto;">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>

            <!-- Success Content -->
            <div class="space-y-2" style="display: flex; flex-direction: column; gap: 8px;">
                <h3 class="text-lg font-extrabold text-white" id="success-title" style="font-size: 18px; font-weight: 800; color: white; margin: 0;">Check-in Sukses!</h3>
                <p class="text-sm text-slate-400" id="success-message" style="font-size: 14px; color: #9ca3af; margin: 0;">Pasien telah berhasil terdaftar kedatangannya.</p>
            </div>

            <!-- Patient Detail Card -->
            <div class="bg-slate-950 border border-slate-800/80 rounded-2xl p-4 text-left text-xs space-y-2.5" style="background-color: #030712; border: 1px solid rgba(31, 41, 55, 0.8); border-radius: 16px; padding: 16px; text-align: left; font-size: 12px; display: flex; flex-direction: column; gap: 10px;">
                <div class="flex justify-between" style="display: flex; justify-content: space-between;">
                    <span class="text-slate-500 font-medium" style="color: #6b7280; font-weight: 500;">Nama:</span>
                    <span class="font-extrabold text-white" id="success-patient-name" style="color: white; font-weight: 800;">-</span>
                </div>
                <div class="flex justify-between" style="display: flex; justify-content: space-between;">
                    <span class="text-slate-500 font-medium" style="color: #6b7280; font-weight: 500;">RM Sementara:</span>
                    <span class="font-mono font-bold text-white" id="success-patient-rm" style="color: white; font-family: monospace; font-weight: 700;">-</span>
                </div>
                <div class="flex justify-between" style="display: flex; justify-content: space-between;">
                    <span class="text-slate-500 font-medium" style="color: #6b7280; font-weight: 500;">Event:</span>
                    <span class="font-semibold text-primary-400" id="success-patient-event" style="color: #10b981; font-weight: 600;">-</span>
                </div>
                <div class="flex justify-between" style="display: flex; justify-content: space-between;">
                    <span class="text-slate-500 font-medium" style="color: #6b7280; font-weight: 500;">Check-in Jam:</span>
                    <span class="font-bold text-emerald-500" id="success-patient-time" style="color: #10b981; font-weight: 700;">-</span>
                </div>
            </div>

            <!-- Dismiss Button -->
            <div>
                <button id="dismiss-success-btn" class="w-full py-3 bg-primary-600 hover:bg-primary-700 active:scale-95 text-white font-bold text-sm rounded-xl transition-all" style="width: 100%; background-color: #10b981; color: white; font-weight: 700; font-size: 14px; padding: 12px 0; border-radius: 12px; border: none; cursor: pointer; transition: all 0.2s;">
                    Siap Memindai Lagi
                </button>
            </div>
        </div>
    </div>

    <!-- Error Modal / Toast Overlay -->
    <div id="error-toast" class="fixed top-4 inset-x-4 z-50 hidden flex justify-center pointer-events-none" style="position: fixed; top: 16px; left: 16px; right: 16px; z-index: 50; display: none; justify-content: center; pointer-events: none;">
        <div class="bg-red-500 text-white rounded-2xl px-5 py-4 shadow-2xl flex items-center gap-3 max-w-sm pointer-events-auto border border-red-400/20" style="background-color: #ef4444; color: white; border-radius: 16px; padding: 16px 20px; display: flex; align-items: center; gap: 12px; width: 100%; max-width: 384px; pointer-events: auto; border: 1px solid rgba(239, 68, 68, 0.2); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <svg class="h-6 w-6 text-white shrink-0" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="white">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1" style="flex: 1;">
                <h4 class="text-xs font-extrabold uppercase tracking-wider opacity-85" style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin: 0; opacity: 0.85;">Gagal Check-in</h4>
                <p class="text-xs font-semibold mt-0.5 leading-snug" id="error-toast-message" style="font-size: 12px; font-weight: 600; margin: 2px 0 0 0; leading-height: 1.25;">Kode barcode tidak valid atau tidak terdaftar.</p>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bg-slate-900/90 backdrop-blur-md border-t border-slate-800 px-6 pt-3 pb-6 safe-bottom flex justify-around items-center z-10" style="background-color: rgba(17, 24, 39, 0.9); backdrop-filter: blur(12px); border-top: 1px solid #1f2937; padding: 12px 24px 24px 24px; display: flex; justify-content: space-around; align-items: center;">
        <!-- Tab Button Scanner -->
        <button id="tab-scanner-btn" class="flex flex-col items-center gap-1 text-emerald-500 font-bold transition-all" style="background: none; border: none; display: flex; flex-direction: column; align-items: center; gap: 4px; font-weight: 700; cursor: pointer;">
            <svg class="h-6 w-6" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#10b981">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span class="text-[10px] tracking-wide" style="font-size: 10px; letter-spacing: 0.05em; color: #10b981;">Pindai Tiket</span>
        </button>

        <!-- Tab Button Logs -->
        <button id="tab-logs-btn" class="flex flex-col items-center gap-1 text-slate-500 font-bold hover:text-slate-400 transition-all" style="background: none; border: none; display: flex; flex-direction: column; align-items: center; gap: 4px; font-weight: 700; cursor: pointer;">
            <svg class="h-6 w-6" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#6b7280">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[10px] tracking-wide" style="font-size: 10px; letter-spacing: 0.05em; color: #6b7280;" id="logs-tab-label">Kehadiran</span>
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
            const logsTabLabel = document.getElementById('logs-tab-label');
            
            const scannerContent = document.getElementById('tab-scanner-content');
            const logsContent = document.getElementById('tab-logs-content');

            scannerBtn.addEventListener('click', function() {
                scannerBtn.querySelector('svg').setAttribute('stroke', '#10b981');
                scannerBtn.querySelector('span').style.color = '#10b981';
                
                logsBtn.querySelector('svg').setAttribute('stroke', '#6b7280');
                logsTabLabel.style.color = '#6b7280';

                scannerContent.style.display = 'flex';
                logsContent.style.display = 'none';
                
                // Restart scanner if tab changed
                startCamera();
            });

            logsBtn.addEventListener('click', function() {
                logsBtn.querySelector('svg').setAttribute('stroke', '#10b981');
                logsTabLabel.style.color = '#10b981';
                
                scannerBtn.querySelector('svg').setAttribute('stroke', '#6b7280');
                scannerBtn.querySelector('span').style.color = '#6b7280';

                logsContent.style.display = 'flex';
                scannerContent.style.display = 'none';

                // Stop scanner to save power
                stopCamera();
            });

            // Initialize Camera Scanner
            function startCamera() {
                if (html5Qrcode && html5Qrcode.isScanning) {
                    return;
                }

                const loadingDiv = document.getElementById('scanner-loading');
                loadingDiv.style.display = 'flex';
                
                // Check for secure context (Chrome camera restriction)
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    loadingDiv.innerHTML = `
                        <div style="padding: 16px; text-align: center; display: flex; flex-direction: column; gap: 8px; align-items: center; justify-content: center;">
                            <span style="font-size: 24px;">⚠️</span>
                            <p style="font-size: 13px; color: #f59e0b; font-weight: 700; margin: 0;">Kamera Diblokir (Harus HTTPS)</p>
                            <p style="font-size: 10px; color: #9ca3af; line-height: 1.4; margin: 0; max-width: 240px;">
                                Browser memblokir akses kamera pada koneksi HTTP biasa. Harap akses melalui <strong>HTTPS</strong>, atau tambahkan origin ini ke setelan Chrome Flag Anda:
                            </p>
                            <code style="font-size: 9px; color: #34d399; background-color: #030712; padding: 4px 8px; border-radius: 6px; font-family: monospace; display: block; word-break: break-all;">
                                chrome://flags/#unsafely-treat-insecure-origin-as-secure
                            </code>
                            <p style="font-size: 9px; color: #6b7280; margin: 0;">
                                Masukkan <strong>http://${window.location.host}</strong> di sana dan aktifkan (Enabled), lalu buka kembali.
                            </p>
                        </div>
                    `;
                    return;
                }
                
                // Instance of Html5Qrcode on scanner div
                html5Qrcode = new Html5Qrcode("scanner-view");
                
                Html5Qrcode.getCameras().then(devices => {
                    loadingDiv.style.display = 'none';
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
                            loadingDiv.style.display = 'flex';
                            loadingDiv.innerHTML = `
                                <p style="font-size: 11px; color: #ef4444; font-weight: 700;">Gagal membuka kamera: ${err.message || err}</p>
                            `;
                        });
                    } else {
                        loadingDiv.style.display = 'flex';
                        loadingDiv.innerHTML = `
                            <p style="font-size: 11px; color: #ef4444; font-weight: 700;">Kamera tidak ditemukan. Harap gunakan input manual.</p>
                        `;
                    }
                }).catch(err => {
                    loadingDiv.style.display = 'flex';
                    loadingDiv.innerHTML = `
                        <p style="font-size: 11px; color: #ef4444; font-weight: 700;">Gagal mengakses perangkat kamera: ${err.message || err}</p>
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
                    document.getElementById('success-title').style.color = "#f59e0b";
                    document.getElementById('success-message').innerText = "Pasien ini sudah check-in hari ini.";
                } else {
                    document.getElementById('success-title').innerText = "Check-in Sukses!";
                    document.getElementById('success-title').className = "text-lg font-extrabold text-white";
                    document.getElementById('success-title').style.color = "white";
                    document.getElementById('success-message').innerText = "Pasien berhasil dikonfirmasi kehadirannya.";
                }

                successModal.style.display = 'flex';
            }

            dismissBtn.addEventListener('click', function() {
                successModal.style.display = 'none';
                isProcessing = false; // clear processing state
            });

            // Toast Toggle
            const errorToast = document.getElementById('error-toast');
            const errorToastMessage = document.getElementById('error-toast-message');

            function showErrorToast(msg) {
                errorToastMessage.innerText = msg;
                errorToast.style.display = 'flex';
                setTimeout(() => {
                    errorToast.style.display = 'none';
                }, 3000);
            }

            // Real-time Update Kehadiran logs HTML list
            function updateLogsList(patient) {
                if (patient.already_checked_in) return;

                const emptyState = document.getElementById('logs-empty-state');
                if (emptyState) {
                    emptyState.remove();
                }

                const logsCountSpan = document.getElementById('logs-count');
                let countVal = parseInt(logsCountSpan.innerText) || 0;
                logsCountSpan.innerText = (countVal + 1) + " Orang";

                const logItem = `
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex justify-between items-center shadow-md animate-pulse" style="background-color: #111827; border: 1px solid #1f2937; border-radius: 16px; padding: 16px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                        <div class="space-y-1" style="display: flex; flex-direction: column; gap: 4px;">
                            <h4 class="text-sm font-bold text-white leading-tight" style="font-size: 14px; font-weight: 700; color: white; margin: 0;">${patient.name}</h4>
                            <p class="text-xs text-slate-400 font-semibold font-mono" style="font-size: 12px; color: #9ca3af; font-family: monospace; margin: 0;">${patient.medical_record_number}</p>
                            <span class="inline-block text-[9px] bg-slate-800 text-primary-400 border border-slate-700/50 px-2 py-0.5 rounded-md font-semibold" style="display: inline-block; font-size: 9px; background-color: #1f2937; color: #10b981; border: 1px solid rgba(31, 41, 55, 0.5); padding: 2px 8px; border-radius: 6px; font-weight: 600;">
                                ${patient.event_name}
                            </span>
                        </div>
                        <div class="text-right" style="text-align: right;">
                            <span class="text-xs font-bold text-emerald-500 block" style="font-size: 12px; font-weight: 700; color: #10b981; display: block;">Hadir</span>
                            <span class="text-[10px] text-slate-500 font-medium" style="font-size: 10px; color: #6b7280; font-weight: 500;">
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
