@extends('layouts.app')

@section('title', 'Daftar Pemeriksaan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Daftar Pemeriksaan</h1>
                <p class="page-header-desc">Daftar riwayat pemeriksaan awal pasien yang digunakan sebagai dasar follow-up.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('follow-up.examinations.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Catat Pemeriksaan
                </a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Jenis</th>
                    <th>Detail Lensa</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examinations as $exam)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">
                            {{ $exam->examination_date->format('d M Y') }}
                        </td>
                        <td>
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $exam->patient->name }}</div>
                            <div class="text-xs text-slate-400">RM: {{ $exam->patient->medical_record_number }}</div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">{{ $exam->doctor->name ?? '-' }}</td>
                        <td>
                            <span class="badge-blue">Lensa Kontak</span>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">
                            {{ $exam->lens_type ?? '-' }} - {{ $exam->lens_brand ?? '-' }}
                        </td>
                        <td class="text-right">
                            <a href="{{ route('follow-up.examinations.show', $exam) }}" class="table-action-primary">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <h3 class="empty-state-title">Belum ada data pemeriksaan</h3>
                                <p class="empty-state-desc">Mulai dengan mencatat pemeriksaan baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $examinations->links() }}
    </div>
</div>
@endsection
