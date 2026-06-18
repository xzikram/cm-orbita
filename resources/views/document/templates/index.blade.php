@extends('layouts.app')

@section('title', 'PDF Wrapper Templates')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">PDF Wrapper Templates</h1>
                <p class="page-header-desc">Kelola template desain Cover (Header, Footer, Margin) untuk DPC.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                @if($templates->total() > 0)
                    <form action="{{ route('dpc.templates.deleteAll') }}" method="POST" onsubmit="return confirm('Hapus semua template?');">
                        @csrf
                        <button type="submit" class="btn-danger">Hapus Semua</button>
                    </form>
                @endif
                <a href="{{ route('dpc.templates.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Template
                </a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Template</th>
                    <th>Margin (Atas/Bawah)</th>
                    <th>Header/Footer Logo</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">{{ $template->name }}</td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs whitespace-nowrap">{{ $template->margin_top }}mm / {{ $template->margin_bottom }}mm</td>
                        <td>
                            <div class="flex gap-1.5">
                                @if($template->header_logo_path)
                                    <span class="badge-green">Header ✓</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-700/50 px-2 py-0.5 text-[10px] font-medium text-slate-500 dark:text-slate-400">No Header</span>
                                @endif
                                @if($template->footer_logo_path)
                                    <span class="badge-green">Footer ✓</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($template->is_active)
                                <span class="badge-green"><svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Aktif</span>
                            @else
                                <span class="badge-red"><svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('dpc.templates.edit', $template) }}" class="table-action-edit">Edit</a>
                                <form action="{{ route('dpc.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Hapus template ini?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="table-action-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                                <h3 class="empty-state-title">Belum ada template</h3>
                                <p class="empty-state-desc">Tambahkan template PDF wrapper baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $templates->links() }}</div>
</div>
@endsection
