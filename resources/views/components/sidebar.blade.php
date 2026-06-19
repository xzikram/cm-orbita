<div class="sidebar-scroll-container flex grow flex-col gap-y-4 overflow-y-auto bg-white/90 dark:bg-slate-900/95 backdrop-blur-xl px-5 pb-4 border-r border-slate-200/60 dark:border-slate-700/50 shadow-xl shadow-slate-900/[0.03]">
    <!-- Corporate Header/Logo Section -->
    <div class="flex h-20 shrink-0 items-center px-2 mb-1">
        <div class="flex items-center gap-x-3.5">
            <div class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 shadow-lg shadow-primary-500/30 text-white font-bold ring-2 ring-primary-400/20">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-400 ring-2 ring-white dark:ring-slate-900"></div>
            </div>
            <div>
                <span class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-none block">CFMS</span>
                <span class="text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-[0.15em] block mt-1">Clinical Follow-Up</span>
            </div>
        </div>
    </div>

    <!-- Divider -->
    <div class="h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-700 to-transparent"></div>

    <!-- Navigation List -->
    <nav class="flex flex-1 flex-col -mt-2">
        <ul role="list" class="flex flex-1 flex-col gap-y-5">
            <!-- Dashboard Menu Group -->
            <li>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @can('dashboard.view')
                    @php $active = request()->routeIs('dashboard'); @endphp
                    <li>
                        <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>

            <!-- Follow-Up Modul Section -->
            @can('follow-up.view')
            <li>
                <div class="flex items-center gap-x-2 px-1 mb-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-slate-200 dark:from-slate-700 to-transparent"></div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] whitespace-nowrap">Follow-Up</span>
                    <div class="h-px flex-1 bg-gradient-to-l from-slate-200 dark:from-slate-700 to-transparent"></div>
                </div>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @can('patients.view')
                    @php $active = request()->routeIs('follow-up.patients.*'); @endphp
                    <li>
                        <a href="{{ route('follow-up.patients.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Pasien
                        </a>
                    </li>
                    @endcan

                    @can('examinations.view')
                    @php $active = request()->routeIs('follow-up.examinations.*'); @endphp
                    <li>
                        <a href="{{ route('follow-up.examinations.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Pemeriksaan
                        </a>
                    </li>
                    @endcan

                    @php $active = request()->routeIs('follow-up.schedules.*'); @endphp
                    <li>
                        <a href="{{ route('follow-up.schedules.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            Jadwal Kontrol
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            <!-- Reminder Engine Section -->
            @can('reminders.view')
            <li>
                <div class="flex items-center gap-x-2 px-1 mb-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-slate-200 dark:from-slate-700 to-transparent"></div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] whitespace-nowrap">Reminder</span>
                    <div class="h-px flex-1 bg-gradient-to-l from-slate-200 dark:from-slate-700 to-transparent"></div>
                </div>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @php $active = request()->routeIs('reminders.index'); @endphp
                    <li>
                        <a href="{{ route('reminders.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            Monitor Reminder
                        </a>
                    </li>

                    @php $active = request()->routeIs('reminders.logs'); @endphp
                    <li>
                        <a href="{{ route('reminders.logs') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Log Pengiriman
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            <!-- Doc Processing Section -->
            <li>
                <div class="flex items-center gap-x-2 px-1 mb-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-slate-200 dark:from-slate-700 to-transparent"></div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] whitespace-nowrap">Dokumen</span>
                    <div class="h-px flex-1 bg-gradient-to-l from-slate-200 dark:from-slate-700 to-transparent"></div>
                </div>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @php $active = request()->routeIs('dpc.processing.*'); @endphp
                    <li>
                        <a href="{{ route('dpc.processing.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Process Document
                        </a>
                    </li>

                    @php $active = request()->routeIs('dpc.templates.*'); @endphp
                    <li>
                        <a href="{{ route('dpc.templates.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                            PDF Templates
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Communication Center Section -->
            @if(Auth::user()->hasAnyPermission([
                'communication.deliveries.manage',
                'communication.whatsapp.manage',
                'communication.email-templates.manage',
                'communication.whatsapp-templates.manage',
                'communication.document-types.manage',
                'communication.email-accounts.manage'
            ]))
            <li>
                <div class="flex items-center gap-x-2 px-1 mb-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-slate-200 dark:from-slate-700 to-transparent"></div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] whitespace-nowrap">Komunikasi</span>
                    <div class="h-px flex-1 bg-gradient-to-l from-slate-200 dark:from-slate-700 to-transparent"></div>
                </div>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @can('communication.deliveries.manage')
                    @php $active = request()->routeIs('communication.deliveries.*'); @endphp
                    <li>
                        <a href="{{ route('communication.deliveries.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            Pengiriman Dokumen
                        </a>
                    </li>
                    @endcan

                    @can('communication.whatsapp.manage')
                    @php $active = request()->routeIs('communication.whatsapp.status'); @endphp
                    <li>
                        <a href="{{ route('communication.whatsapp.status') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp Gateway
                        </a>
                    </li>
                    @endcan

                    @can('communication.email-templates.manage')
                    @php $active = request()->routeIs('communication.email-templates.*'); @endphp
                    <li>
                        <a href="{{ route('communication.email-templates.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Email Templates
                        </a>
                    </li>
                    @endcan

                    @can('communication.whatsapp-templates.manage')
                    @php $active = request()->routeIs('communication.whatsapp-templates.*'); @endphp
                    <li>
                        <a href="{{ route('communication.whatsapp-templates.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.255-3.653a1.122 1.122 0 01.865-.502c1.153-.086 2.294-.213 3.423-.379 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.269z" />
                            </svg>
                            WA Templates
                        </a>
                    </li>
                    @endcan

                    @can('communication.document-types.manage')
                    @php $active = request()->routeIs('communication.document-types.*'); @endphp
                    <li>
                        <a href="{{ route('communication.document-types.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Tipe Dokumen
                        </a>
                    </li>
                    @endcan

                    @can('communication.email-accounts.manage')
                    @php $active = request()->routeIs('communication.email-accounts.*'); @endphp
                    <li>
                        <a href="{{ route('communication.email-accounts.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" />
                            </svg>
                            Email SMTP
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif

            <!-- Master Data Section -->
            @can('doctors.view')
            <li>
                <div class="flex items-center gap-x-2 px-1 mb-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-slate-200 dark:from-slate-700 to-transparent"></div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] whitespace-nowrap">Master Data</span>
                    <div class="h-px flex-1 bg-gradient-to-l from-slate-200 dark:from-slate-700 to-transparent"></div>
                </div>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @php $active = request()->routeIs('master-data.doctors.*'); @endphp
                    <li>
                        <a href="{{ route('master-data.doctors.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Dokter
                        </a>
                    </li>

                    @php $active = request()->routeIs('master-data.clinics.*'); @endphp
                    <li>
                        <a href="{{ route('master-data.clinics.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                            </svg>
                            Cabang / Klinik
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            <!-- Administration Section -->
            @if(Auth::user()->can('users.view') || Auth::user()->can('roles.view') || Auth::user()->can('audit.view'))
            <li>
                <div class="flex items-center gap-x-2 px-1 mb-2">
                    <div class="h-px flex-1 bg-gradient-to-r from-slate-200 dark:from-slate-700 to-transparent"></div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] whitespace-nowrap">Admin</span>
                    <div class="h-px flex-1 bg-gradient-to-l from-slate-200 dark:from-slate-700 to-transparent"></div>
                </div>
                <ul role="list" class="-mx-2 space-y-0.5">
                    @can('users.view')
                    @php $active = request()->routeIs('administration.users.*'); @endphp
                    <li>
                        <a href="{{ route('administration.users.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21.75c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.97 5.97 0 00-.75-2.95m-9.337 3.197L8 18.72c0-1.218.469-2.329 1.25-3.17m-6.241 3.197A9.09 9.09 0 012.25 18c0-2.83 2.29-5.12 5.12-5.12m0 0a3.375 3.375 0 100-6.75 3.375 3.375 0 000 6.75zM12 11.25a3.375 3.375 0 100-6.75 3.375 3.375 0 000 6.75z" />
                            </svg>
                            Master User
                        </a>
                    </li>
                    @endcan

                    @can('roles.view')
                    @php $active = request()->routeIs('administration.roles.*'); @endphp
                    <li>
                        <a href="{{ route('administration.roles.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            Group Akses
                        </a>
                    </li>
                    @endcan

                    @can('audit.view')
                    @php $active = request()->routeIs('audit.*'); @endphp
                    <li>
                        <a href="{{ route('audit.index') }}" class="group relative flex items-center gap-x-3 rounded-xl px-3 py-2.5 text-[13px] leading-6 font-semibold transition-all duration-200 {{ $active ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 shadow-sm shadow-primary-500/5' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-gradient-to-b from-primary-500 to-primary-600 shadow-sm shadow-primary-500/30"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            Audit Logs
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endif


            <!-- User Profile Section (bottom) -->
            <li class="mt-auto -mx-2 pt-4">
                <div class="h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-700 to-transparent mb-4"></div>
                <div class="flex items-center gap-x-3 rounded-2xl p-3 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-800/80 dark:to-slate-800/40 ring-1 ring-slate-200/50 dark:ring-slate-700/30 hover:shadow-md transition-all duration-300">
                    <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-primary-500/20 ring-2 ring-primary-400/10">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0 flex items-center">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-500 transition-all duration-200 p-2 rounded-xl hover:bg-white dark:hover:bg-slate-700 hover:shadow-sm" title="Logout">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
</div>

<script>
    (function() {
        if (window.sidebarScrollInitialized) return;
        window.sidebarScrollInitialized = true;

        const initSidebarScroll = () => {
            const scrollContainers = document.querySelectorAll('.sidebar-scroll-container');
            const savedScrollPos = sessionStorage.getItem('sidebar-scroll-position');
            
            scrollContainers.forEach(container => {
                if (savedScrollPos !== null) {
                    container.scrollTop = parseInt(savedScrollPos, 10);
                } else {
                    // Fallback: scroll active item into view
                    const activeSpan = container.querySelector('span.absolute.bg-gradient-to-b');
                    const activeLink = activeSpan ? activeSpan.closest('a') : null;
                    if (activeLink) {
                        activeLink.scrollIntoView({ block: 'nearest', behavior: 'auto' });
                    }
                }
                
                // Save scroll position on scroll
                let scrollTimeout;
                container.addEventListener('scroll', () => {
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        if (container.offsetHeight > 0) {
                            sessionStorage.setItem('sidebar-scroll-position', container.scrollTop);
                        }
                    }, 100);
                });
                
                // Save scroll position on link click
                container.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        if (container.offsetHeight > 0) {
                            sessionStorage.setItem('sidebar-scroll-position', container.scrollTop);
                        }
                    });
                });
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebarScroll);
        } else {
            initSidebarScroll();
        }
    })();
</script>
