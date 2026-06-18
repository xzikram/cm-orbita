@extends('layouts.app')

@section('title', 'WhatsApp Templates')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">WhatsApp Templates</h1>
                <p class="page-header-desc">Kelola template pesan WhatsApp untuk pengiriman pengingat jadwal kontrol atau kustom komunikasi pasien.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('communication.whatsapp-templates.create') }}" class="btn-primary">
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
                    <th>Jenis</th>
                    <th>Variabel</th>
                    <th>Default</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr>
                        <td class="font-semibold text-slate-900 dark:text-white max-w-xs truncate">{{ $template->name }}</td>
                        <td>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 text-xs font-mono font-medium text-slate-600 dark:text-slate-300 ring-1 ring-slate-200/50 dark:ring-slate-600/50">{{ strtoupper($template->type) }}</span>
                        </td>
                        <td class="max-w-xs">
                            <div class="flex flex-wrap gap-1">
                                @if($template->variables)
                                    @foreach($template->variables as $var)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/20 px-1.5 py-0.5 text-xs font-mono text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/20">{{ '{' . $var . '}' }}</span>
                                    @endforeach
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($template->is_default)
                                <span class="badge-yellow">Default</span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
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
                                <a href="{{ route('communication.whatsapp-templates.edit', $template) }}" class="table-action-edit">Edit</a>
                                <form action="{{ route('communication.whatsapp-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Hapus template ini?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="table-action-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.255-3.653a1.122 1.122 0 01.865-.502c1.153-.086 2.294-.213 3.423-.379 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.269z" /></svg>
                                <h3 class="empty-state-title">Belum ada template WhatsApp</h3>
                                <p class="empty-state-desc">Tambahkan template baru untuk mulai mengirim pesan.</p>
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
