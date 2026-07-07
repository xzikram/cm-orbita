@extends('layouts.app')

@section('title', 'Detail Pasien')

@section('content')
<div class="space-y-6">
    @if($patient->needs_follow_up)
        <div class="card p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between gap-4 dark:bg-amber-950/20 dark:border-amber-800">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 text-amber-600 dark:text-amber-400">
                    <svg class="h-5 w-5 fill-amber-500 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.63-1.515 2.63H3.72c-1.346 0-2.188-1.463-1.515-2.63L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">Pasien Memerlukan Follow-Up</h4>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Catatan: {{ $patient->follow_up_notes ?? 'Tidak ada catatan' }}</p>
                </div>
            </div>
            <div>
                <button type="button" 
                        class="btn-primary bg-amber-600 hover:bg-amber-500 border-amber-600 focus-visible:outline-amber-600 text-xs py-1.5 px-3 rounded-xl whitespace-nowrap"
                        onclick="openResolveModal()">
                    Selesaikan Follow-Up
                </button>
            </div>
        </div>
    @endif

    <!-- Header Patient -->
    <div class="card p-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex sm:space-x-5">
                <div class="flex-shrink-0">
                    <div class="h-20 w-20 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400 text-3xl font-bold">
                        {{ substr($patient->name, 0, 1) }}
                    </div>
                </div>
                <div class="mt-4 text-center sm:mt-0 sm:pt-1 sm:text-left">
                    <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap mb-1">
                        <p class="text-xl font-bold text-slate-900 dark:text-white sm:text-2xl">{{ $patient->name }}</p>
                        @if($patient->is_downtime_entry)
                            <span class="badge-yellow text-[10px] py-0.5 px-2 font-bold uppercase tracking-wide">Downtime SIMRS</span>
                        @endif
                    </div>
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">No. RM: {{ $patient->medical_record_number }} | HP: {{ $patient->phone ?? '-' }}</p>
                </div>
            </div>
            <div class="mt-5 flex justify-center gap-3 sm:mt-0">
                @if(!$patient->needs_follow_up)
                    <button type="button" onclick="openResolveModal()" class="btn-secondary text-xs py-1.5 px-3 rounded-xl border border-slate-300 dark:border-slate-700 flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21v11h-6l-1-1H5v4m0-4h16" />
                        </svg>
                        Tandai Follow-Up
                    </button>
                @endif
                <a href="{{ route('communication.deliveries.create', ['patient_id' => $patient->id]) }}" class="btn-secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Kirim Dokumen
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $patient->phone) }}" target="_blank" class="btn-primary bg-green-600 hover:bg-green-500 focus-visible:outline-green-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        <!-- Kolom Kiri: Riwayat Pemeriksaan -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profil Pasien Card -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-semibold leading-6 text-slate-900 dark:text-white">Profil Pasien</h3>
                    <a href="{{ route('follow-up.patients.edit', $patient) }}" class="table-action-edit">Edit Profil</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">NIK (No. KTP)</span>
                        <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $patient->nik ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">No. Rekam Medis</span>
                        <span class="text-slate-800 dark:text-slate-200 font-mono font-bold">{{ $patient->medical_record_number }}</span>
                        @if($patient->temporary_medical_record_number)
                            <span class="block text-[10px] text-amber-600 dark:text-amber-400 font-mono mt-0.5">Sebelumnya: {{ $patient->temporary_medical_record_number }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Jenis Kelamin</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ $patient->gender == 'L' ? 'Laki-laki' : ($patient->gender == 'P' ? 'Perempuan' : '-') }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal Lahir / Umur</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ $patient->date_of_birth ? $patient->date_of_birth->format('d F Y') . ' (' . $patient->age . ' tahun)' : '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Orangtua / Pasangan</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ $patient->parent_spouse_name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nomor HP/WA</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ $patient->phone ?? '-' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Alamat Lengkap</span>
                        <span class="text-slate-800 dark:text-slate-200">{{ $patient->address ?? '-' }}</span>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Kontak Darurat (Nama)</span>
                            <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $patient->emergency_contact_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">No. HP Kontak Darurat</span>
                            <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $patient->emergency_contact_phone ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white mb-4">Riwayat Pemeriksaan</h3>
                @if($patient->examinations->isEmpty())
                    <p class="text-sm text-slate-500">Belum ada data pemeriksaan.</p>
                @else
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($patient->examinations as $exam)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-slate-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-slate-800">
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-slate-500 dark:text-slate-400">Pemeriksaan oleh <span class="font-medium text-slate-900 dark:text-white">{{ $exam->doctor->name ?? '-' }}</span></p>
                                                <div class="mt-2 text-sm text-slate-700 dark:text-slate-300">
                                                    <p>Lensa: {{ $exam->lens_brand ?? '-' }} ({{ $exam->lens_type ?? '-' }})</p>
                                                    <p>OD: {{ $exam->od_visus }} | OS: {{ $exam->os_visus }}</p>
                                                </div>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-slate-500 dark:text-slate-400">
                                                <time datetime="{{ $exam->examination_date->format('Y-m-d') }}">{{ $exam->examination_date->format('d M Y') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan: Unified Communication Timeline -->
        <div class="lg:col-span-1">
            <div class="card p-6">
                <h3 class="text-lg font-medium leading-6 text-slate-900 dark:text-white mb-4">Communication Timeline</h3>
                
                @if($timeline->isEmpty())
                    <p class="text-sm text-slate-500">Belum ada riwayat komunikasi.</p>
                @else
                    <div class="flow-root mt-4">
                        <ul role="list" class="-mb-8">
                            @foreach($timeline as $event)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-slate-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full 
                                                @if($event['type'] == 'email') bg-indigo-500 
                                                @elseif($event['type'] == 'whatsapp') bg-teal-500 
                                                @elseif($event['type'] == 'visit') bg-green-500 
                                                @elseif($event['type'] == 'follow_up_log') bg-amber-500
                                                @else bg-blue-500 @endif 
                                                flex items-center justify-center ring-8 ring-white dark:ring-slate-800">
                                                
                                                @if($event['type'] == 'email')
                                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                @elseif($event['type'] == 'whatsapp')
                                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                                @elseif($event['type'] == 'visit')
                                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                @elseif($event['type'] == 'follow_up_log')
                                                    @if(isset($event['action']) && $event['action'] === 'marked')
                                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21v11h-6l-1-1H5v4m0-4h16" />
                                                        </svg>
                                                    @else
                                                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                @else
                                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-2 pt-1.5">
                                            <div>
                                                <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $event['title'] }}</p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $event['description'] }}</p>
                                                
                                                @if(isset($event['status']))
                                                    <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium mt-1
                                                        @if($event['status'] == 'sent' || $event['status'] == 'completed') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/30 dark:text-green-400
                                                        @elseif($event['status'] == 'failed') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-400
                                                        @else bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-400
                                                        @endif
                                                    ">
                                                        {{ ucfirst($event['status']) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="whitespace-nowrap text-right text-xs text-slate-500 dark:text-slate-400">
                                                <time datetime="{{ \Carbon\Carbon::parse($event['date'])->toIso8601String() }}">{{ \Carbon\Carbon::parse($event['date'])->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Follow-Up -->
<div id="followup-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md hidden" x-cloak>
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl border border-slate-200 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">
            {{ $patient->needs_follow_up ? 'Selesaikan Follow-Up' : 'Tandai Pasien Perlu Follow-Up' }}
        </h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
            {{ $patient->needs_follow_up ? 'Tulis catatan penyelesaian follow-up untuk pasien ini.' : 'Tulis catatan awal mengapa pasien ini memerlukan follow-up.' }}
        </p>
        
        <form action="{{ route('follow-up.patients.mark-follow-up', $patient) }}" method="POST" class="space-y-4">
            @csrf
            
            <input type="hidden" name="needs_follow_up" value="{{ $patient->needs_follow_up ? '0' : '1' }}">

            <div class="space-y-1">
                <label for="follow_up_notes" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    {{ $patient->needs_follow_up ? 'Catatan Penyelesaian (Opsional)' : 'Catatan Follow-Up (Opsional)' }}
                </label>
                <textarea name="follow_up_notes" id="follow_up_notes" rows="3" class="input-field" placeholder="Masukkan catatan di sini..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button onclick="closeResolveModal()" type="button" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    {{ $patient->needs_follow_up ? 'Selesaikan' : 'Tandai' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResolveModal() {
        document.getElementById('followup-modal').classList.remove('hidden');
    }
    function closeResolveModal() {
        document.getElementById('followup-modal').classList.add('hidden');
    }
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('followup-modal');
        if (e.target === modal) {
            closeResolveModal();
        }
    });
</script>
@endsection
