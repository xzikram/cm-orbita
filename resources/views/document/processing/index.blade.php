@extends('layouts.app')

@section('title', 'Document Processing Center')

@section('content')
<style>
    /* Override whitespace-nowrap for this specific table so columns can breathe */
    .dpc-table tbody td {
        white-space: normal !important;
    }
    .dpc-table {
        table-layout: fixed;
        width: 100%;
    }
</style>
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Document Processing Center</h1>
                <p class="page-header-desc">Daftar dokumen medis yang telah diproses dan diberi Cover + QR Code.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                @if($documents->total() > 0)
                    <button type="submit" form="bulk-send-form" id="bulk-send-btn" class="btn-secondary bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900 opacity-50 cursor-not-allowed" disabled>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                        Kirim Terpilih
                    </button>
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

    <form id="bulk-send-form" action="{{ route('communication.deliveries.create') }}" method="GET">
        <div class="table-container">
            <table class="premium-table dpc-table">
                <colgroup>
                    <col style="width: 40px;">   {{-- Checkbox --}}
                    <col style="width: 15%;">    {{-- No. Dokumen --}}
                    <col style="width: 18%;">    {{-- Nama File --}}
                    <col style="width: 18%;">    {{-- Pasien --}}
                    <col style="width: 14%;">    {{-- Tipe Dokumen --}}
                    <col style="width: 13%;">    {{-- Tanggal --}}
                    <col style="width: 10%;">    {{-- Status --}}
                    <col style="width: 12%;">    {{-- Aksi --}}
                </colgroup>
                <thead>
                    <tr>
                        <th class="!pl-3 !pr-1 text-center">
                            <input type="checkbox" id="select-all-docs" class="rounded border-slate-300 text-primary-600 focus:ring-primary-600">
                        </th>
                        <th>No. Dokumen</th>
                        <th>Nama File</th>
                        <th>Pasien</th>
                        <th>Tipe Dokumen</th>
                        <th>
                            <a href="{{ route('dpc.processing.index', ['sort' => $sortDirection === 'asc' ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200">
                                Tanggal
                                @if($sortDirection === 'asc')
                                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                @else
                                    <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td class="!pl-3 !pr-1 text-center">
                                <input type="checkbox" name="processed_document_ids[]" value="{{ $doc->id }}" class="doc-checkbox rounded border-slate-300 text-primary-600 focus:ring-primary-600">
                            </td>
                            <td>
                                <span class="font-semibold text-slate-900 dark:text-white font-mono text-xs">{{ $doc->document_number }}</span>
                            </td>
                            <td>
                                <div class="truncate text-slate-500 dark:text-slate-400 text-xs" title="{{ $doc->original_filename }}">{{ $doc->original_filename ?? '-' }}</div>
                            </td>
                            <td>
                                @php
                                    $patient = $doc->patient ?? $doc->deliveries->first()?->patient;
                                @endphp
                                @if($patient)
                                    <div class="font-semibold text-slate-900 dark:text-white text-sm truncate">{{ $patient->name }}</div>
                                    <div class="text-xs text-slate-400 truncate">RM: {{ $patient->medical_record_number }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $docType = $doc->documentType ?? $doc->deliveries->first()?->documentType;
                                @endphp
                                <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $docType?->name ?? '-' }}</span>
                            </td>
                            <td class="text-slate-500 dark:text-slate-400 text-xs whitespace-nowrap">{{ $doc->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                @php
                                    $isSent = $doc->deliveries->where('status', 'sent')->isNotEmpty();
                                @endphp
                                @if($isSent)
                                    <span class="badge-green">TERKIRIM</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-50 dark:bg-slate-500/10 px-2 py-0.5 text-[10px] font-semibold text-slate-500 dark:text-slate-400 ring-1 ring-inset ring-slate-500/10 dark:ring-slate-500/20 whitespace-nowrap">BELUM KIRIM</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-x-1.5">
                                    <a href="{{ route('dpc.processing.show', $doc) }}" class="table-action-primary !px-2 !py-1 text-[11px]">Lihat</a>
                                    <form action="{{ route('dpc.processing.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="table-action-danger !px-2 !py-1 text-[11px]" @if(!auth()->user()->hasAnyRole(['super-admin', 'admin-klinik'])) disabled style="opacity: 0.5; cursor: not-allowed;" title="Hanya Admin yang dapat menghapus" @endif>Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
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
    </form>

    <div class="mt-2">{{ $documents->links() }}</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all-docs');
        const checkboxes = document.querySelectorAll('.doc-checkbox');
        const bulkSendBtn = document.getElementById('bulk-send-btn');

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.doc-checkbox:checked').length;
            if (bulkSendBtn) {
                bulkSendBtn.disabled = checkedCount === 0;
                if (checkedCount === 0) {
                    bulkSendBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    bulkSendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateButtonState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const totalChecked = document.querySelectorAll('.doc-checkbox:checked').length;
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = totalChecked === checkboxes.length;
                }
                updateButtonState();
            });
        });

        updateButtonState();
    });
</script>
@endsection
