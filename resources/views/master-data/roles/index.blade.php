@extends('layouts.app')

@section('title', 'Group Akses')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Group Akses</h1>
                <p class="page-header-desc">Kelola hak akses dan peran pengguna dalam sistem.</p>
            </div>
            @can('roles.create')
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('administration.roles.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Group
                </a>
            </div>
            @endcan
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Nama Peran / Group Akses</th>
                    <th>Guard</th>
                    <th>Jumlah User</th>
                    <th>Jumlah Permission</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    @php $isBuiltin = in_array($role->name, ['super-admin', 'admin-klinik', 'dokter', 'med-ass', 'ro', 'petugas-follow-up']); @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-900 dark:text-white">{{ ucwords(str_replace('-', ' ', $role->name)) }}</span>
                                @if($isBuiltin)
                                    <span class="badge-blue">Bawaan</span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-400 font-mono">{{ $role->name }}</span>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs">{{ $role->guard_name }}</td>
                        <td>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 dark:bg-slate-700/50 px-2.5 py-1 text-xs font-bold text-slate-600 dark:text-slate-300">{{ $role->users_count }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">{{ $role->permissions_count }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2 items-center">
                                @can('roles.edit')
                                <a href="{{ route('administration.roles.edit', $role) }}" class="table-action-edit">Edit</a>
                                @endcan
                                @can('roles.delete')
                                    @if(!$isBuiltin)
                                    <form action="{{ route('administration.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus group akses ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="table-action-danger">Hapus</button>
                                    </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                <h3 class="empty-state-title">Belum ada group akses</h3>
                                <p class="empty-state-desc">Tambahkan group akses baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
