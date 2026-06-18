@extends('layouts.app')

@section('title', 'Tipe Dokumen')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Tipe Dokumen</h1>
                <p class="page-header-desc">Kelola master data jenis dokumen untuk Communication Center.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                @if($types->total() > 0)
                    <form action="{{ route('communication.document-types.deleteAll') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua tipe dokumen secara permanen?');">
                        @csrf
                        <button type="submit" class="btn-danger">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            Hapus Semua
                        </button>
                    </form>
                @endif
                <a href="{{ route('communication.document-types.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Tipe
                </a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Tipe Dokumen</th>
                    <th>Format Kode</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">{{ $type->name }}</td>
                        <td>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 text-xs font-mono font-medium text-slate-700 dark:text-slate-300 ring-1 ring-slate-200/50 dark:ring-slate-600/50">{{ $type->code }}</span>
                        </td>
                        <td>
                            @if($type->is_active)
                                <span class="badge-green"><svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Aktif</span>
                            @else
                                <span class="badge-red"><svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('communication.document-types.edit', $type) }}" class="table-action-edit">Edit</a>
                                <form action="{{ route('communication.document-types.destroy', $type) }}" method="POST" onsubmit="return confirm('Hapus tipe dokumen ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="table-action-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                <h3 class="empty-state-title">Belum ada tipe dokumen</h3>
                                <p class="empty-state-desc">Tambahkan tipe dokumen baru untuk mulai mengirim.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $types->links() }}</div>
</div>
@endsection
