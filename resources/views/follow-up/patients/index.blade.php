@extends('layouts.app')

@section('title', 'Daftar Pasien')

@section('content')
<div class="space-y-6" x-data="{ quickRmOpen: false, quickRmPatientId: null, quickRmPatientName: '', quickRmOldNumber: '', quickRmNewNumber: '' }">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Daftar Pasien</h1>
                <p class="page-header-desc">Daftar seluruh pasien yang terdaftar di klinik beserta riwayat pemeriksaan dan kontrol mereka.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex flex-wrap items-center gap-3" x-data="{ open: false, password: '' }">
                <a href="{{ route('follow-up.patients.export-csv', request()->all()) }}" class="btn-secondary" title="Ekspor daftar pasien sebagai Excel">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Ekspor Excel
                </a>
                <a href="{{ route('follow-up.patients.import-mapping') }}" class="btn-secondary" title="Update Masal No. RM pasca Downtime">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                    Update RM Masal
                </a>
                @if($patients->total() > 0)
                    <button @click="open = true; password = ''" class="btn-danger">
                        Hapus Semua
                    </button>
                @endif
                <a href="{{ route('follow-up.patients.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Pasien
                </a>

                <!-- Modal Konfirmasi Hapus Semua -->
                <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md" x-cloak>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl border border-slate-200 dark:border-slate-800" @click.outside="open = false">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Hapus Semua Pasien?</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Tindakan ini akan menghapus seluruh data master pasien beserta data pemeriksaan dan kunjungan kontrol terkait secara permanen. Masukkan password konfirmasi untuk melanjutkan.</p>
                        
                        <input type="password" x-model="password" class="input-field mb-4" placeholder="Masukkan password konfirmasi...">
                        
                        <div class="flex justify-end gap-3">
                            <button @click="open = false; password = ''" type="button" class="btn-secondary">Batal</button>
                            <form action="{{ route('follow-up.patients.deleteAll') }}" method="POST">
                                @csrf
                                <input type="hidden" name="confirm_password" :value="password">
                                <button type="submit" class="btn-danger" :disabled="password.length === 0">Hapus Semua</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Cepat Nomor RM -->
                <div x-show="quickRmOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md" x-cloak>
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl border border-slate-200 dark:border-slate-800" @click.outside="quickRmOpen = false">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Update Nomor RM Resmi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Update nomor rekam medis sementara pasien <strong x-text="quickRmPatientName"></strong> menjadi nomor RM resmi.</p>
                        
                        <form :action="'/follow-up/patients/' + quickRmPatientId + '/quick-update-rm'" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor RM Sementara</label>
                                <input type="text" :value="quickRmOldNumber" readonly class="input-field bg-slate-50 dark:bg-slate-800/40 text-slate-500 cursor-not-allowed">
                            </div>

                            <div>
                                <label for="medical_record_number_quick" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nomor RM Resmi Baru *</label>
                                <input type="text" name="medical_record_number" id="medical_record_number_quick" x-model="quickRmNewNumber" required class="input-field" placeholder="Masukkan No. RM Baru (misal: 016-xxx)">
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button @click="quickRmOpen = false" type="button" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary" :disabled="quickRmNewNumber.trim().length === 0">Perbarui RM</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar & Filters -->
    <div class="card p-4">
        <form action="{{ route('follow-up.patients.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-stretch md:items-center">
            <div class="flex-1 relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau No. RM..." class="input-field pl-11">
            </div>
            <div class="flex flex-wrap gap-3 items-center">
                <select name="registration_source" onchange="this.form.submit()" class="input-field py-2.5">
                    <option value="">-- Semua Sumber Registrasi --</option>
                    <option value="admin" {{ request('registration_source') === 'admin' ? 'selected' : '' }}>Admin (Langsung)</option>
                    <option value="downtime" {{ request('registration_source') === 'downtime' ? 'selected' : '' }}>Downtime SIMRS</option>
                    <option value="document_delivery" {{ request('registration_source') === 'document_delivery' ? 'selected' : '' }}>Kirim Berkas</option>
                    <option value="event" {{ request('registration_source') === 'event' ? 'selected' : '' }}>Event Gratis</option>
                    <option value="marketing" {{ request('registration_source') === 'marketing' ? 'selected' : '' }}>IG Promo Link</option>
                </select>
                <select name="downtime" onchange="this.form.submit()" class="input-field py-2.5">
                    <option value="">-- Semua Status SIMRS --</option>
                    <option value="1" {{ request('downtime') === '1' ? 'selected' : '' }}>Downtime SIMRS</option>
                    <option value="0" {{ request('downtime') === '0' ? 'selected' : '' }}>Normal</option>
                </select>
                <button type="submit" class="btn-primary">Filter</button>
                @if(request()->anyFilled(['search', 'downtime', 'registration_source']))
                    <a href="{{ route('follow-up.patients.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>No. Rekam Medis</th>
                    <th>Kontak</th>
                    <th>Tanggal Lahir</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>
                            <div class="flex items-center gap-x-3">
                                <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold ring-1 ring-primary-200/50 dark:ring-primary-700/30">
                                    {{ substr($patient->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <div class="font-semibold text-slate-900 dark:text-white">{{ $patient->name }}</div>
                                        @if($patient->registration_source === 'downtime' || $patient->is_downtime_entry)
                                            <span class="badge-yellow text-[9px] py-0.5 px-1.5 font-bold uppercase tracking-wide">Downtime SIMRS</span>
                                        @elseif($patient->registration_source === 'event')
                                            <span class="badge-green text-[9px] py-0.5 px-1.5 font-bold uppercase tracking-wide">Event</span>
                                        @elseif($patient->registration_source === 'marketing')
                                            <span class="badge-blue text-[9px] py-0.5 px-1.5 font-bold uppercase tracking-wide">IG Promo</span>
                                        @elseif($patient->registration_source === 'document_delivery')
                                            <span class="inline-flex items-center gap-x-1 rounded-full bg-indigo-50 px-1.5 py-0.5 text-[9px] font-bold text-indigo-700 ring-1 ring-inset ring-indigo-600/10 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20 uppercase tracking-wide">Kirim Berkas</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $patient->gender == 'L' ? 'Laki-laki' : ($patient->gender == 'P' ? 'Perempuan' : '-') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-mono text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <span>{{ $patient->medical_record_number }}</span>
                                @if($patient->is_downtime_entry || str_starts_with($patient->medical_record_number, 'TEMP-') || preg_match('/^\d{8}-\d{6}$/', $patient->medical_record_number))
                                    <button @click="quickRmOpen = true; quickRmPatientId = '{{ $patient->id }}'; quickRmPatientName = '{{ addslashes($patient->name) }}'; quickRmOldNumber = '{{ $patient->medical_record_number }}'; quickRmNewNumber = ''" 
                                            class="text-amber-500 hover:text-amber-600 dark:text-amber-400 dark:hover:text-amber-300"
                                            title="Update ke RM Resmi">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-slate-600 dark:text-slate-300">{{ $patient->phone ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $patient->email ?? '' }}</div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">
                            {{ $patient->date_of_birth ? $patient->date_of_birth->format('d M Y') : '-' }}
                        </td>
                        <td>
                            @if($patient->is_active)
                                <span class="badge-green">
                                    <svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                    Aktif
                                </span>
                            @else
                                <span class="badge-red">
                                    <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('follow-up.patients.show', $patient) }}" class="table-action-primary">Profil</a>
                                <a href="{{ route('follow-up.patients.edit', $patient) }}" class="table-action-edit">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada data pasien</h3>
                                <p class="empty-state-desc">Mulai dengan menambahkan pasien baru ke sistem.</p>
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
@endsection
