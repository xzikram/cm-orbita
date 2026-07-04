@extends('layouts.app')

@section('title', 'Detail Event: ' . $event->name)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="page-header-title">{{ $event->name }}</h1>
                    @if($event->is_active)
                        <span class="badge-green">Aktif</span>
                    @else
                        <span class="badge-red">Nonaktif</span>
                    @endif
                </div>
                <p class="page-header-desc">Diselenggarakan pada {{ $event->event_date->format('d M Y') }} di {{ $event->location }}.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex gap-3">
                <a href="{{ route('follow-up.events.index') }}" class="btn-secondary">Kembali</a>
                <form action="{{ route('follow-up.events.toggle-active', $event) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-secondary {{ $event->is_active ? 'text-red-600 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-700' }}">
                        {{ $event->is_active ? 'Nonaktifkan Event' : 'Aktifkan Event' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Event Details & QR Code -->
        <div class="card p-6 space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">Informasi Event</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Kode Akses</span>
                        <span class="font-mono text-slate-900 dark:text-white">{{ $event->code }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</span>
                        <span class="text-slate-900 dark:text-white">{{ $event->event_date->format('l, d F Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Lokasi</span>
                        <span class="text-slate-900 dark:text-white">{{ $event->location }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan</span>
                        <span class="text-slate-600 dark:text-slate-400">{{ $event->description ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="border-t border-slate-200 dark:border-slate-800 pt-4 flex flex-col items-center text-center space-y-4">
                <div>
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">QR Code Pendaftaran Mandiri</h4>
                    <p class="text-xs text-slate-500 mt-1">Tempel QR code ini di meja pendaftaran atau banner lokasi acara agar pengunjung bisa mendaftar lewat HP.</p>
                </div>

                <!-- Branded Card Preview -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-md flex flex-col items-center text-center w-full max-w-sm mx-auto" style="font-family: 'Inter', sans-serif;">
                    <!-- Logo JEC-ORBITA -->
                    <img src="/Logo RS JEC ORBITA.png" onerror="this.src='/logo.png'" style="height: 48px; object-fit: contain; margin-bottom: 16px; display: block;">
                    
                    <!-- Registrasi Capsule -->
                    <div style="background-color: #1b4e80; color: white; border-radius: 9999px; padding: 8px 32px; font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 8px; box-shadow: 0 4px 6px rgba(27,78,128,0.2);">
                        REGISTRASI
                    </div>
                    
                    <!-- Code & Location -->
                    <div class="text-xs font-semibold text-slate-600 mb-6">
                        {{ $event->code }} - {{ $event->location }}
                    </div>

                    <!-- QR Code with Center Logo -->
                    <div style="position: relative; display: inline-block; background: white; padding: 12px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <img src="{{ $qrcodeBase64 }}" style="width: 200px; height: 200px; display: block;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 38px; height: 38px; background: white; display: flex; align-items: center; justify-content: center; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                            <img src="/jec-logo.png" style="width: 30px; height: auto;">
                        </div>
                    </div>
                </div>

                <div class="w-full space-y-2">
                    <a href="{{ route('events.register', $event->code) }}" target="_blank" class="btn-primary w-full text-center flex justify-center items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                        Buka Halaman Pendaftaran
                    </a>
                    <button onclick="printQRCode()" class="btn-secondary w-full text-center flex justify-center items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.82l2.68-2.68m0 0l2.68 2.68M9.4 11.14v6.48m5.4-9.36a6 6 0 11-10.8 3.6M15 12h.008v.008H15V12z" /></svg>
                        Cetak Poster QR
                    </button>
                </div>
            </div>
        </div>

        <!-- Registered Patients List -->
        <div class="card p-6 lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Peserta Terdaftar</h3>
                <span class="badge-blue text-xs font-bold">{{ $patients->total() }} Terdaftar</span>
            </div>

            <div class="table-container">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Nama Pasien</th>
                            <th>No. RM Sementara</th>
                            <th>No. WhatsApp</th>
                            <th>Umur / JK</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td class="font-semibold text-slate-900 dark:text-white">
                                    {{ $patient->name }}
                                </td>
                                <td class="font-mono text-xs text-slate-500 dark:text-slate-400">
                                    {{ $patient->medical_record_number }}
                                </td>
                                <td class="text-slate-600 dark:text-slate-300">
                                    {{ $patient->phone }}
                                </td>
                                <td class="text-slate-600 dark:text-slate-300">
                                    {{ $patient->age ? $patient->age . ' Thn' : '-' }} ({{ $patient->gender }})
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-x-2">
                                        <a href="{{ route('follow-up.patients.show', $patient) }}" class="table-action-primary">Profil</a>
                                        <!-- Periksa Event Link -->
                                        <a href="{{ route('follow-up.examinations.create', ['patient_id' => $patient->id]) }}" class="text-xs font-semibold px-2 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/20 transition-colors">Periksa</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-8 text-slate-400">
                                        Belum ada peserta yang mendaftar untuk event ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $patients->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Print Only Area for QR Code -->
<div id="print-area" class="hidden">
    <div style="text-align: center; padding: 60px 40px; font-family: 'Inter', sans-serif; background: white;">
        <!-- Logo JEC-ORBITA -->
        <img src="/Logo RS JEC ORBITA.png" onerror="this.src='/logo.png'" style="height: 80px; object-fit: contain; margin-bottom: 24px; display: inline-block;">
        
        <br>
        <!-- Registrasi Capsule -->
        <div style="background-color: #1b4e80; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; border-radius: 9999px; padding: 14px 56px; font-size: 32px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 12px; box-shadow: 0 4px 6px rgba(27,78,128,0.2);">
            REGISTRASI
        </div>
        
        <!-- Code & Location -->
        <div style="font-size: 18px; font-weight: 700; color: #475569; margin-bottom: 36px; text-transform: uppercase; letter-spacing: 0.02em;">
            {{ $event->code }} - {{ $event->location }}
        </div>

        <!-- QR Code with Center Logo -->
        <div style="position: relative; display: inline-block; background: white; padding: 16px; border-radius: 24px; border: 2px solid #e2e8f0; box-shadow: 0 6px 12px rgba(0,0,0,0.05);">
            <img src="{{ $qrcodeBase64 }}" style="width: 320px; height: 320px; display: block;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 60px; height: 60px; background: white; display: flex; align-items: center; justify-content: center; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                <img src="/jec-logo.png" style="width: 48px; height: auto;">
            </div>
        </div>
    </div>
</div>

<script>
    function printQRCode() {
        var printContents = document.getElementById("print-area").innerHTML;
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Cetak QR Code Event</title>');
        printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    }
</script>
@endsection
