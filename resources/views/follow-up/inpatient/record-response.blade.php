@extends('layouts.app')

@section('title', 'Pencatatan Respon Follow-Up Rawat Inap')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="page-header flex items-center justify-between">
        <div>
            <div class="flex items-center gap-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('follow-up.inpatient.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Follow-Up Rawat Inap</a>
                <span>/</span>
                <span class="text-slate-600 dark:text-slate-300 font-semibold">Pencatatan Respon Pasien</span>
            </div>
            <h1 class="page-header-title">Evaluasi 5 Poin Klinis Pasca Pulang Ranap</h1>
            <p class="page-header-desc">Catat respon dan evaluasi kondisi pasien berdasarkan balasan pesan WhatsApp.</p>
        </div>

        <a href="{{ route('follow-up.inpatient.index') }}" class="btn-secondary text-xs py-2 px-4 gap-x-1.5">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Info Pasien & Pesan Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Ringkasan Pasien & Log Kirim -->
        <div class="space-y-4">
            <div class="card p-5 space-y-4 text-xs">
                <div class="flex items-center gap-x-3 pb-3 border-b border-slate-100 dark:border-slate-700/60">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-md shadow-primary-500/20">
                        {{ strtoupper(substr($followUp->patient_name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-900 dark:text-white truncate">{{ $followUp->patient_name }}</h3>
                        <p class="font-mono text-slate-400 mt-0.5">RM: {{ $followUp->medical_record_number }}</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-slate-600 dark:text-slate-300">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Usia / Gender:</span>
                        <span class="font-semibold">{{ $followUp->patient_age ? $followUp->patient_age . ' tahun' : '-' }} ({{ $followUp->gender ?: '-' }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">WhatsApp:</span>
                        <span class="font-semibold font-mono text-emerald-600 dark:text-emerald-400">{{ $followUp->patient_phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tgl Kepulangan:</span>
                        <span class="font-semibold">{{ $followUp->discharge_date ? $followUp->discharge_date->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kamar / Ruang:</span>
                        <span class="font-semibold">{{ $followUp->room_bed ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Dokter DPJP:</span>
                        <span class="font-semibold text-right">{{ $followUp->doctor_dpjp ?: '-' }}</span>
                    </div>
                    @if($followUp->diagnosis_or_procedure)
                    <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60">
                        <span class="text-slate-400 block mb-1">Diagnosa / Tindakan:</span>
                        <p class="font-medium text-slate-800 dark:text-slate-200">{{ $followUp->diagnosis_or_procedure }}</p>
                    </div>
                    @endif
                </div>

                @if($followUp->sent_at)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/40 -mx-5 -mb-5 p-4 rounded-b-2xl">
                    <div class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-bold mb-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Pesan Terkirim
                    </div>
                    <p class="text-[11px] text-slate-400">
                        {{ $followUp->sent_at->format('d M Y H:i') }} WITA<br>
                        Pengirim: <strong class="text-slate-700 dark:text-slate-200">{{ $followUp->nurse_name }}</strong>
                    </p>
                </div>
                @endif
            </div>

            @if($followUp->whatsapp_message)
            <div class="card p-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 mb-2">Pesan WA Yang Telah Dikirim:</p>
                <div class="text-xs text-slate-600 dark:text-slate-300 whitespace-pre-wrap leading-relaxed max-h-48 overflow-y-auto font-sans bg-slate-50/80 dark:bg-slate-900/60 p-3.5 rounded-xl ring-1 ring-slate-200/60 dark:ring-slate-700">
{{ $followUp->whatsapp_message }}
                </div>
            </div>
            @endif
        </div>

        <!-- Kolom Kanan: Form Respon 5 Pertanyaan Klinis -->
        <div class="lg:col-span-2">
            <div class="form-section">
                <form action="{{ route('follow-up.inpatient.store-response', $followUp) }}" method="POST" class="space-y-6 text-xs">
                    @csrf

                    <div class="border-b border-slate-100 dark:border-slate-700/60 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Form Evaluasi Kondisi Pasien (5 Poin)</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Isi poin evaluasi sesuai hasil konfirmasi/balasan dari pasien atau keluarga.</p>
                    </div>

                    <!-- Pertanyaan 1: Keluhan Saat Ini -->
                    <div>
                        <label for="response_complaints" class="form-label font-bold">
                            1. Apakah saat ini pasien masih memiliki keluhan?
                        </label>
                        <textarea name="response_complaints" id="response_complaints" rows="2" placeholder="Tuliskan keluhan yang dirasakan pasien (atau 'Tidak ada keluhan')..." class="input-field">{{ old('response_complaints', $followUp->response_complaints) }}</textarea>
                    </div>

                    <!-- Pertanyaan 2: Kepatuhan Obat -->
                    <div>
                        <label class="form-label font-bold">
                            2. Apakah obat yang diberikan masih digunakan sesuai anjuran dokter? <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                                <input type="radio" name="response_medication_compliance" value="patuh" {{ old('response_medication_compliance', $followUp->response_medication_compliance ?: 'patuh') === 'patuh' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-600">
                                <div>
                                    <p class="font-bold text-emerald-600 dark:text-emerald-400">Patuh & Rutin</p>
                                    <p class="text-[10px] text-slate-400">Sesuai anjuran dokter</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                                <input type="radio" name="response_medication_compliance" value="sebagian" {{ old('response_medication_compliance', $followUp->response_medication_compliance) === 'sebagian' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-600">
                                <div>
                                    <p class="font-bold text-amber-600 dark:text-amber-400">Sebagian</p>
                                    <p class="text-[10px] text-slate-400">Ada obat yang terlupa</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                                <input type="radio" name="response_medication_compliance" value="tidak_patuh" {{ old('response_medication_compliance', $followUp->response_medication_compliance) === 'tidak_patuh' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-600">
                                <div>
                                    <p class="font-bold text-rose-600 dark:text-rose-400">Tidak Digunakan</p>
                                    <p class="text-[10px] text-slate-400">Berhenti minum/tetes</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Pertanyaan 3: Efek Samping Obat -->
                    <div>
                        <label for="response_side_effects" class="form-label font-bold">
                            3. Apakah terdapat efek samping atau reaksi yang tidak biasa setelah menggunakan obat?
                        </label>
                        <textarea name="response_side_effects" id="response_side_effects" rows="2" placeholder="Tuliskan jika ada efek samping (contoh: perih berat, alergi gatal, pusing, mual, dll)..." class="input-field">{{ old('response_side_effects', $followUp->response_side_effects) }}</textarea>
                    </div>

                    <!-- Pertanyaan 4: Kondisi Luka Operasi -->
                    <div>
                        <label class="form-label font-bold">
                            4. Bagaimana kondisi luka operasi saat ini? <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                                <input type="radio" name="response_wound_condition" value="baik_kering" {{ old('response_wound_condition', $followUp->response_wound_condition ?: 'baik_kering') === 'baik_kering' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-600">
                                <div>
                                    <p class="font-bold text-emerald-600 dark:text-emerald-400">Baik / Kering / Tenang</p>
                                    <p class="text-[10px] text-slate-400">Tidak ada tanda infeksi</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-rose-200 dark:ring-rose-900/50 bg-rose-50/40 dark:bg-rose-950/20 cursor-pointer hover:bg-rose-100/50 transition-colors">
                                <input type="radio" name="response_wound_condition" value="merah_bengkak" {{ old('response_wound_condition', $followUp->response_wound_condition) === 'merah_bengkak' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-600">
                                <div>
                                    <p class="font-bold text-rose-600 dark:text-rose-400">Kemerahan / Bengkak</p>
                                    <p class="text-[10px] text-slate-400">Ada tanda radang aktif</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-rose-200 dark:ring-rose-900/50 bg-rose-50/40 dark:bg-rose-950/20 cursor-pointer hover:bg-rose-100/50 transition-colors">
                                <input type="radio" name="response_wound_condition" value="keluar_cairan" {{ old('response_wound_condition', $followUp->response_wound_condition) === 'keluar_cairan' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-600">
                                <div>
                                    <p class="font-bold text-rose-600 dark:text-rose-400">Keluar Cairan / Sekret</p>
                                    <p class="text-[10px] text-slate-400">Ada cairan kotoran mata</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-rose-200 dark:ring-rose-900/50 bg-rose-50/40 dark:bg-rose-950/20 cursor-pointer hover:bg-rose-100/50 transition-colors">
                                <input type="radio" name="response_wound_condition" value="nyeri_hebat" {{ old('response_wound_condition', $followUp->response_wound_condition) === 'nyeri_hebat' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-600">
                                <div>
                                    <p class="font-bold text-rose-600 dark:text-rose-400">Nyeri Hebat / Cekot-Cekot</p>
                                    <p class="text-[10px] text-slate-400">Perlu evaluasi segera</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Pertanyaan 5: Perkembangan Penglihatan -->
                    <div>
                        <label class="form-label font-bold">
                            5. Apakah terdapat perubahan atau kemajuan pada penglihatan pasien? <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                                <input type="radio" name="response_vision_progress" value="membaik" {{ old('response_vision_progress', $followUp->response_vision_progress ?: 'membaik') === 'membaik' ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-600">
                                <div>
                                    <p class="font-bold text-emerald-600 dark:text-emerald-400">Membaik / Lebih Terang</p>
                                    <p class="text-[10px] text-slate-400">Ada kemajuan visual</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 bg-slate-50/50 dark:bg-slate-900/50 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-750 transition-colors">
                                <input type="radio" name="response_vision_progress" value="tetap" {{ old('response_vision_progress', $followUp->response_vision_progress) === 'tetap' ? 'checked' : '' }} class="text-slate-600 focus:ring-slate-600">
                                <div>
                                    <p class="font-bold text-slate-700 dark:text-slate-300">Masih Tetap / Stabil</p>
                                    <p class="text-[10px] text-slate-400">Belum ada perubahan</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl ring-1 ring-rose-200 dark:ring-rose-900/50 bg-rose-50/40 dark:bg-rose-950/20 cursor-pointer hover:bg-rose-100/50 transition-colors">
                                <input type="radio" name="response_vision_progress" value="menurun" {{ old('response_vision_progress', $followUp->response_vision_progress) === 'menurun' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-600">
                                <div>
                                    <p class="font-bold text-rose-600 dark:text-rose-400">Menurun / Buram</p>
                                    <p class="text-[10px] text-slate-400">Penurunan tajam visus</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label for="response_notes" class="form-label font-bold">
                            Catatan Tambahan Perawat
                        </label>
                        <textarea name="response_notes" id="response_notes" rows="2" placeholder="Catatan saran perawat, edukasi yang diberikan, atau rencana kontrol..." class="input-field">{{ old('response_notes', $followUp->response_notes) }}</textarea>
                    </div>

                    <!-- Flagging Dokter DPJP -->
                    <div class="rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 ring-1 ring-amber-500/30 p-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="needs_doctor_review" value="1" {{ old('needs_doctor_review', $followUp->needs_doctor_review) ? 'checked' : '' }} class="mt-0.5 rounded text-rose-600 focus:ring-rose-600">
                            <div>
                                <span class="font-bold text-amber-900 dark:text-amber-200 text-xs">Tandai Memerlukan Perhatian / Konsultasi Dokter DPJP Segera</span>
                                <p class="text-[11px] text-amber-700/80 dark:text-amber-400/80 mt-0.5">Centang ini jika pasien mengalami keluhan berat, tanda infeksi, atau penurunan tajam penglihatan agar masuk ke daftar atensi khusus dokter.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-end gap-x-2.5">
                        <a href="{{ route('follow-up.inpatient.index') }}" class="btn-secondary text-xs py-2.5 px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary text-xs py-2.5 px-6 gap-x-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan Hasil Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
