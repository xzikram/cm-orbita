@extends('layouts.app')

@section('title', 'Email SMTP Accounts')

@section('content')
<div class="space-y-6">
    <div class="page-header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="page-header-title">Email SMTP Accounts</h1>
                <p class="page-header-desc">Kelola akun email yang akan digunakan untuk mengirim dokumen ke pasien.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route('communication.email-accounts.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Akun SMTP
                </a>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Account Name</th>
                    <th>Email Address</th>
                    <th>SMTP Host</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $account->name }}</span>
                                @if($account->is_default)
                                    <span class="badge-blue">Default</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs">{{ $account->email_address }}</td>
                        <td class="text-slate-500 dark:text-slate-400 font-mono text-xs whitespace-nowrap">{{ $account->smtp_host }}:{{ $account->smtp_port }}</td>
                        <td>
                            @if($account->is_active)
                                <span class="badge-green"><svg class="h-1.5 w-1.5 fill-emerald-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Active</span>
                            @else
                                <span class="badge-red"><svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg> Inactive</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('communication.email-accounts.edit', $account) }}" class="table-action-edit">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg class="empty-state-icon" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" /></svg>
                                <h3 class="empty-state-title">Belum ada akun SMTP</h3>
                                <p class="empty-state-desc">Konfigurasi akun SMTP untuk mulai mengirim email.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">{{ $accounts->links() }}</div>
</div>
@endsection
