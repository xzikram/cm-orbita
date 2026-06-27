@extends('layouts.app')

@section('title', 'Document Processing Center')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Document Processing Center</h1>
                <p class="page-header-desc">Daftar dokumen medis yang telah diproses dan diberi Cover + QR Code.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                @if($documents->total() > 0)
                    <form action="{{ route('dpc.processing.deleteAll') }}" method="POST" onsubmit="return confirm('Hapus semua data dokumen?');">
                        @csrf
                        <button type="submit" class="btn-danger" @if(!auth()->user()->hasAnyRole(['super-admin', 'admin-klinik'])) disabled style="opacity: 0.5; cursor: not-allowed;" title="Hanya Admin yang dapat menghapus" @endif>Hapus Semua</button>
                    </form>
                @endif
                <a href="{{ route('dpc.processing.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Process Document
                </a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No. Dokumen</th>
                    <th>Pasien</th>
                    <th>Tipe Dokumen</th>
                    <th>Tanggal Diproses</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white font-mono whitespace-nowrap">{{ $doc->document_number }}</td>
                        <td>
                            @if($doc->patient)
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $doc->patient->name }}</div>
                                <div class="text-xs text-slate-400">RM: {{ $doc->patient->medical_record_number }}</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="text-slate-500 dark:text-slate-400">{{ $doc->documentType?->name ?? '-' }}</td>
                        <td class="text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $doc->created_at->format('d M Y, H:i') }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('dpc.processing.show', $doc) }}" class="table-action-primary">Lihat / Kirim</a>
                                <form action="{{ route('dpc.processing.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="table-action-danger" @if(!auth()->user()->hasAnyRole(['super-admin', 'admin-klinik'])) disabled style="opacity: 0.5; cursor: not-allowed;" title="Hanya Admin yang dapat menghapus" @endif>Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                <h3 class="empty-state-title">Belum ada dokumen diproses</h3>
                                <p class="empty-state-desc">Mulai memproses dokumen baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $documents->links() }}</div>
</div>
@endsection
