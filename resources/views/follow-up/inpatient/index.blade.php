@extends('layouts.app')

@section('title', 'Follow-Up Pasien Rawat Inap')

@section('content')
<div class="space-y-6" x-data="inpatientFollowUp()">
    <!-- Page Header -->
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-x-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center shadow-md shadow-primary-500/20 shrink-0">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V5.625c0-.621-.504-1.125-1.125-1.125h-4.5c-.621 0-1.125.504-1.125 1.125v1.875M3 14.25V7.5a2.25 2.25 0 0 1 2.25-2.25h1.5A2.25 2.25 0 0 1 9 7.5v6.75m-6 0h18" />
                    </svg>
                </div>
                <div>
                    <h1 class="page-header-title">Follow-Up Pasien Rawat Inap</h1>
                    <p class="page-header-desc">Penjangkauan & evaluasi klinis kondisi pasien pada <strong>H+3 pasca kepulangan</strong> via WhatsApp.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Tombol Tarik dari SIM RS -->
            <form action="{{ route('follow-up.inpatient.sync-simrs') }}" method="POST" class="inline" onsubmit="return confirm('Tarik data kepulangan pasien rawat inap terbaru dari SIM RS?');">
                @csrf
                <button type="submit" class="btn-secondary text-xs py-2 px-4 gap-x-1.5">
                    <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Tarik dari SIM RS
                </button>
            </form>

            <!-- Tombol Tambah Manual -->
            <a href="{{ route('follow-up.inpatient.create') }}" class="btn-primary text-xs py-2 px-4 gap-x-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Manual
            </a>
        </div>
    </div>

    <!-- Stats Cards (Interactive Filter Pills) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- Semua -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'all']) }}" class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $tab === 'all' ? 'ring-2 ring-primary-500 bg-primary-50/40 dark:bg-primary-950/20 shadow-sm' : 'ring-slate-900/[0.04] dark:ring-white/[0.06]' }}">
            <dt class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Semua</dt>
            <dd class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $statusCounts['all'] ?? 0 }}</dd>
            <p class="text-[10px] text-slate-400 mt-1">Total Terdata</p>
        </a>

        <!-- H+3 Hari Ini -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'due_today']) }}" class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $tab === 'due_today' ? 'ring-2 ring-amber-500 bg-amber-50/60 dark:bg-amber-950/30 shadow-sm' : 'ring-amber-500/20 bg-amber-50/20 dark:bg-amber-950/10' }}">
            <div class="flex items-center justify-center gap-1.5">
                <dt class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">H+3 Hari Ini</dt>
                @if(($statusCounts['due_today'] ?? 0) > 0)
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                @endif
            </div>
            <dd class="mt-1.5 text-2xl font-extrabold tracking-tight text-amber-600 dark:text-amber-400">{{ $statusCounts['due_today'] ?? 0 }}</dd>
            <p class="text-[10px] text-amber-600/70 dark:text-amber-400/70 mt-1">Jatuh Tempo Hari Ini</p>
        </a>

        <!-- Belum Dikirim -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'pending']) }}" class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $tab === 'pending' ? 'ring-2 ring-slate-400 bg-slate-100/70 dark:bg-slate-700/50 shadow-sm' : 'ring-slate-900/[0.04] dark:ring-white/[0.06]' }}">
            <dt class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Belum Kirim</dt>
            <dd class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-700 dark:text-slate-200">{{ $statusCounts['pending'] ?? 0 }}</dd>
            <p class="text-[10px] text-slate-400 mt-1">Menunggu WA</p>
        </a>

        <!-- Pesan Terkirim -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'sent']) }}" class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $tab === 'sent' ? 'ring-2 ring-sky-500 bg-sky-50/60 dark:bg-sky-950/30 shadow-sm' : 'ring-sky-500/20 bg-sky-50/20 dark:bg-sky-950/10' }}">
            <dt class="text-[11px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Terkirim</dt>
            <dd class="mt-1.5 text-2xl font-extrabold tracking-tight text-sky-600 dark:text-sky-400">{{ $statusCounts['sent'] ?? 0 }}</dd>
            <p class="text-[10px] text-sky-500/70 mt-1">Menunggu Respon</p>
        </a>

        <!-- Respon Selesai -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'completed']) }}" class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $tab === 'completed' ? 'ring-2 ring-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30 shadow-sm' : 'ring-emerald-500/20 bg-emerald-50/20 dark:bg-emerald-950/10' }}">
            <dt class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Selesai</dt>
            <dd class="mt-1.5 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $statusCounts['completed'] ?? 0 }}</dd>
            <p class="text-[10px] text-emerald-600/70 mt-1">Evaluasi Tercatat</p>
        </a>

        <!-- Perlu Perhatian Dokter -->
        <a href="{{ route('follow-up.inpatient.index', ['status' => 'needs_review']) }}" class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-4 text-center ring-1 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $tab === 'needs_review' ? 'ring-2 ring-rose-500 bg-rose-50/60 dark:bg-rose-950/30 shadow-sm' : 'ring-rose-500/20 bg-rose-50/20 dark:bg-rose-950/10' }}">
            <dt class="text-[11px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Atensi Dokter</dt>
            <dd class="mt-1.5 text-2xl font-extrabold tracking-tight text-rose-600 dark:text-rose-400">{{ $statusCounts['needs_review'] ?? 0 }}</dd>
            <p class="text-[10px] text-rose-500/70 mt-1">Perlu Eskalasi DPJP</p>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card p-4">
        <form method="GET" action="{{ route('follow-up.inpatient.index') }}" class="flex flex-col md:flex-row items-center gap-3">
            <input type="hidden" name="status" value="{{ $tab }}">

            <!-- Search Input -->
            <div class="relative flex-1 w-full">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pasien, No. RM, Dokter DPJP, ruangan..." class="input-field pl-10 text-xs">
            </div>

            <!-- Date Range Inputs -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <div class="w-full md:w-40">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-field text-xs py-2 px-3" title="Tanggal Pulang Dari">
                </div>
                <span class="text-xs text-slate-400">s/d</span>
                <div class="w-full md:w-40">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-field text-xs py-2 px-3" title="Tanggal Pulang Sampai">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 w-full md:w-auto">
                <button type="submit" class="btn-primary text-xs py-2.5 px-4 w-full md:w-auto">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to']))
                <a href="{{ route('follow-up.inpatient.index', ['status' => $tab]) }}" class="btn-secondary text-xs py-2.5 px-3" title="Reset Filter">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Pasien</th>
                    <th>Kepulangan & Kamar</th>
                    <th>DPJP & Tindakan</th>
                    <th>Jadwal H+3</th>
                    <th>Status Follow-Up</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($followUps as $item)
                <tr class="{{ $item->needs_doctor_review ? 'bg-rose-50/40 dark:bg-rose-950/20' : '' }}">
                    <!-- Pasien -->
                    <td>
                        <div class="flex items-center gap-x-3">
                            <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-primary-500/10 to-primary-600/20 dark:from-primary-400/20 dark:to-primary-600/30 text-primary-700 dark:text-primary-300 flex items-center justify-center font-bold text-xs shrink-0 ring-1 ring-primary-500/20">
                                {{ strtoupper(substr($item->patient_name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white truncate">{{ $item->patient_name }}</div>
                                <div class="flex items-center gap-x-1.5 text-xs text-slate-400 mt-0.5">
                                    <span class="font-mono bg-slate-100 dark:bg-slate-700/80 px-1.5 py-0.5 rounded text-[11px] text-slate-600 dark:text-slate-300 font-semibold">{{ $item->medical_record_number }}</span>
                                    <span>•</span>
                                    <span>{{ $item->patient_age ? $item->patient_age . ' th' : '-' }} ({{ $item->gender ?: '-' }})</span>
                                </div>
                                @if($item->patient_phone)
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 font-mono">
                                    <svg class="h-3 w-3 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    {{ $item->patient_phone }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    <!-- Kepulangan & Kamar -->
                    <td>
                        <div class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $item->discharge_date ? $item->discharge_date->format('d M Y') : '-' }}
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $item->room_bed ?: 'Kamar -' }}
                        </div>
                        @if($item->source === 'manual')
                        <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-purple-700 dark:text-purple-300 ring-1 ring-inset ring-purple-600/20 mt-1">Manual</span>
                        @else
                        <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-700/60 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-300 ring-1 ring-inset ring-slate-500/10 mt-1">SIM RS</span>
                        @endif
                    </td>

                    <!-- DPJP & Tindakan -->
                    <td>
                        <div class="font-medium text-slate-800 dark:text-slate-200">
                            {{ $item->doctor_dpjp ?: '-' }}
                        </div>
                        <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5 max-w-xs truncate" title="{{ $item->diagnosis_or_procedure }}">
                            {{ $item->diagnosis_or_procedure ?: '-' }}
                        </div>
                    </td>

                    <!-- Jadwal H+3 -->
                    <td>
                        @if($item->follow_up_due_date)
                        <div class="font-semibold {{ $item->isDueToday() ? 'text-amber-600 dark:text-amber-400 font-bold' : ($item->isOverdue() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300') }}">
                            {{ $item->follow_up_due_date->format('d M Y') }}
                        </div>
                        @if($item->isDueToday())
                        <span class="badge-yellow text-[10px] mt-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            H+3 Hari Ini
                        </span>
                        @elseif($item->isOverdue())
                        <span class="badge-red text-[10px] mt-1">
                            Lewat Jadwal
                        </span>
                        @endif
                        @else
                        <span class="text-slate-400">-</span>
                        @endif
                    </td>

                    <!-- Status Follow-Up -->
                    <td>
                        @if($item->status === 'completed')
                            <span class="badge-green">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                Respon Tercatat
                            </span>
                        @elseif($item->status === 'sent')
                            <span class="badge-blue">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                WA Terkirim
                            </span>
                        @else
                            <span class="badge-yellow">
                                Belum Dikirim
                            </span>
                        @endif

                        @if($item->needs_doctor_review)
                        <div class="mt-1.5">
                            <span class="badge-red font-bold animate-pulse">
                                <svg class="h-3 w-3 text-rose-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                Perlu Perhatian Dokter
                            </span>
                        </div>
                        @endif

                        @if($item->sent_at)
                        <div class="text-[10px] text-slate-400 mt-1">Kirim: {{ $item->sent_at->format('d/m/y H:i') }} ({{ $item->nurse_name }})</div>
                        @endif
                    </td>

                    <!-- Aksi -->
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-x-2">
                            <!-- Tombol Kirim WhatsApp -->
                            <button type="button" @click="openSendModal({{ json_encode($item) }})" class="inline-flex items-center gap-x-1.5 rounded-xl {{ $item->status === 'sent' || $item->status === 'completed' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' : 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm shadow-emerald-600/20' }} px-3 py-1.5 text-xs font-semibold transition-all" title="Kirim Follow-Up WhatsApp">
                                <svg class="h-3.5 w-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                {{ $item->status === 'sent' || $item->status === 'completed' ? 'Kirim Ulang' : 'Kirim WA' }}
                            </button>

                            <!-- Tombol Catat Respon -->
                            <a href="{{ route('follow-up.inpatient.record-response', $item) }}" class="table-action-primary" title="Catat 5 Poin Evaluasi Klinis">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                {{ $item->status === 'completed' ? 'Lihat Respon' : 'Catat Respon' }}
                            </a>

                            <!-- Hapus -->
                            <form action="{{ route('follow-up.inpatient.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data follow-up pasien rawat inap ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V5.625c0-.621-.504-1.125-1.125-1.125h-4.5c-.621 0-1.125.504-1.125 1.125v1.875M3 14.25V7.5a2.25 2.25 0 0 1 2.25-2.25h1.5A2.25 2.25 0 0 1 9 7.5v6.75m-6 0h18" />
                            </svg>
                            <h3 class="empty-state-title">Tidak ada data pasien rawat inap yang ditemukan</h3>
                            <p class="empty-state-desc">Gunakan tombol <strong>"Tarik dari SIM RS"</strong> untuk menarik data otomatis atau <strong>"Tambah Manual"</strong> untuk simulasi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($followUps->hasPages())
    <div class="mt-2">
        {{ $followUps->links() }}
    </div>
    @endif

    <!-- Modal Konfirmasi Kirim WhatsApp -->
    <div x-show="showSendModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showSendModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showSendModal = false"></div>

            <div x-show="showSendModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200/80 dark:border-slate-700/80">
                <form :action="'/follow-up/inpatient/' + activeItem.id + '/send-whatsapp'" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center gap-x-3 mb-5">
                            <div class="h-10 w-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 ring-1 ring-emerald-500/20 flex items-center justify-center shrink-0">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Kirim Pesan Follow-Up WhatsApp</h3>
                                <p class="text-xs text-slate-400">Verifikasi data penerima dan pengirim sebelum mengirim.</p>
                            </div>
                        </div>

                        <div class="space-y-4 text-xs">
                            <div>
                                <label class="form-label">Nama Pasien</label>
                                <input type="text" :value="activeItem.patient_name" readonly class="input-field bg-slate-100 dark:bg-slate-700/50 font-bold text-slate-800 dark:text-slate-200">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Nama Perawat Pengirim</label>
                                    <input type="text" name="nurse_name" x-model="nurseName" required class="input-field">
                                </div>
                                <div>
                                    <label class="form-label">Nomor WhatsApp Pasien</label>
                                    <input type="text" name="patient_phone" x-model="activeItem.patient_phone" required class="input-field font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="form-label">Preview Isi Pesan WhatsApp</label>
                                <div class="rounded-xl bg-slate-50 dark:bg-slate-900/70 p-4 ring-1 ring-slate-200/80 dark:ring-slate-700 text-slate-700 dark:text-slate-300 whitespace-pre-wrap font-sans text-xs leading-relaxed max-h-52 overflow-y-auto">
Selamat Pagi/Siang Bapak/Ibu.

"Perkenalkan, saya <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="nurseName"></span> Perawat Rawat Inap RS Mata JEC Orbita Makassar. Kami ingin melakukan tindak lanjut (follow up) terkait kondisi pasien atas nama <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="activeItem.patient_name + (activeItem.patient_age ? ' (' + activeItem.patient_age + ' tahun)' : '')"></span> setelah menjalani perawatan di rumah sakit.

Mohon bantuannya untuk menginformasikan:

1. Apakah saat ini pasien masih memiliki keluhan?
2. Apakah obat yang diberikan masih digunakan sesuai anjuran dokter?
3. Apakah terdapat efek samping atau reaksi yang tidak biasa setelah menggunakan obat?
4. Bagaimana kondisi luka operasi saat ini? Apakah ada kemerahan, bengkak, keluar cairan, atau keluhan lainnya?
5. Apakah terdapat perubahan atau kemajuan pada penglihatan pasien?

Mohon konfirmasinya apabila pesan ini telah diterima. Terima kasih atas kerja sama Bapak/Ibu.

Salam sehat,

<span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="nurseName"></span>
Perawat Rawat Inap
RS Mata JEC Orbita Makassar"</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/80 dark:bg-slate-900/50 px-6 py-4 flex items-center justify-end gap-x-2.5 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showSendModal = false" class="btn-secondary text-xs py-2 px-4">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary text-xs py-2 px-5 gap-x-1.5">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Kirim Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function inpatientFollowUp() {
    return {
        showSendModal: false,
        activeItem: {},
        nurseName: '{{ Auth::user()->name ?: "Perawat Rawat Inap" }}',
        openSendModal(item) {
            this.activeItem = item;
            this.showSendModal = true;
        }
    };
}
</script>
@endsection
