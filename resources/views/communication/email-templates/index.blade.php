@extends('layouts.app')

@section('title', 'Email Templates')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Email Templates</h1>
                <p class="page-header-desc">Kelola template email untuk berbagai kebutuhan komunikasi pasien.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-3">
                @if($templates->total() > 0)
                    <form action="{{ route('communication.email-templates.deleteAll') }}" method="POST" onsubmit="return confirm('Hapus semua template email?');">
                        @csrf
                        <button type="submit" class="btn-danger">Hapus Semua</button>
                    </form>
                @endif
                <a href="{{ route('communication.email-templates.create') }}" class="btn-primary">
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
                    <th>Subjek Email</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white whitespace-nowrap">{{ $template->name }}</td>
                        <td class="text-slate-500 dark:text-slate-400">{{ $template->subject_template }}</td>
                        <td>
                            @if($template->is_active)
                                <span class="badge-green"><svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Aktif</span>
                            @else
                                <span class="badge-red"><svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-x-2">
                                <a href="{{ route('communication.email-templates.edit', $template) }}" class="table-action-edit">Edit</a>
                                <form action="{{ route('communication.email-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Hapus template ini?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="table-action-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                <h3 class="empty-state-title">Belum ada template email</h3>
                                <p class="empty-state-desc">Tambahkan template baru untuk mulai mengirim email.</p>
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
