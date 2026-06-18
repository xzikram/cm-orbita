<div class="flex grow flex-col gap-y-4 overflow-y-auto bg-white px-5 pb-4 border-r border-slate-200/80 shadow-sm">
    <!-- Corporate Header/Logo Section -->
    <div class="flex h-20 shrink-0 items-center border-b border-slate-100 px-2 mb-2">
        <div class="flex items-center gap-x-3.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg shadow-emerald-500/20 text-white font-bold">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <span class="text-base font-bold text-slate-900 tracking-tight leading-none block">CFMS</span>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest block mt-1">Clinical Follow-Up</span>
            </div>
        </div>
    </div>

    <!-- Navigation List -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-6">
            <!-- Dashboard Menu Group -->
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    @can('dashboard.view')
                    @php $active = request()->routeIs('dashboard'); @endphp
                    <li>
                        <a href="{{ route('dashboard') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                <div class="px-1 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Modul Follow-Up</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @can('patients.view')
                    @php $active = request()->routeIs('follow-up.patients.*'); @endphp
                    <li>
                        <a href="{{ route('follow-up.patients.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Pasien
                        </a>
                    </li>
                    @endcan
                    
                    @can('examinations.view')
                    @php $active = request()->routeIs('follow-up.examinations.*'); @endphp
                    <li>
                        <a href="{{ route('follow-up.examinations.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Pemeriksaan
                        </a>
                    </li>
                    @endcan
                    
                    @php $active = request()->routeIs('follow-up.schedules.*'); @endphp
                    <li>
                        <a href="{{ route('follow-up.schedules.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                <div class="px-1 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Reminder Engine</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @php $active = request()->routeIs('reminders.index'); @endphp
                    <li>
                        <a href="{{ route('reminders.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            Monitor Reminder
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('reminders.logs'); @endphp
                    <li>
                        <a href="{{ route('reminders.logs') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                <div class="px-1 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Doc Processing (DPC)</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @php $active = request()->routeIs('dpc.processing.*'); @endphp
                    <li>
                        <a href="{{ route('dpc.processing.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Process Document
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('dpc.templates.*'); @endphp
                    <li>
                        <a href="{{ route('dpc.templates.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                            PDF Wrapper Templates
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Communication Center Section -->
            <li>
                <div class="px-1 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Communication Center</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @php $active = request()->routeIs('communication.deliveries.*'); @endphp
                    <li>
                        <a href="{{ route('communication.deliveries.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            Document Delivery
                        </a>
                    </li>

                    @php $active = request()->routeIs('communication.whatsapp.status'); @endphp
                    <li>
                        <a href="{{ route('communication.whatsapp.status') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                            </svg>
                            WhatsApp Gateway
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('communication.email-templates.*'); @endphp
                    <li>
                        <a href="{{ route('communication.email-templates.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Email Templates
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('communication.whatsapp-templates.*'); @endphp
                    <li>
                        <a href="{{ route('communication.whatsapp-templates.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.255-3.653a1.122 1.122 0 01.865-.502c1.153-.086 2.294-.213 3.423-.379 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.269z" />
                            </svg>
                            WhatsApp Templates
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('communication.document-types.*'); @endphp
                    <li>
                        <a href="{{ route('communication.document-types.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Document Types
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('communication.email-accounts.*'); @endphp
                    <li>
                        <a href="{{ route('communication.email-accounts.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3m16.5 0h.008v.008h-.008v-.008zm-3 0h.008v.008h-.008v-.008z" />
                            </svg>
                            Email SMTP Accounts
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Master Data Section -->
            @can('doctors.view')
            <li>
                <div class="px-1 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Master Data</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @php $active = request()->routeIs('master-data.doctors.*'); @endphp
                    <li>
                        <a href="{{ route('master-data.doctors.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Dokter
                        </a>
                    </li>
                    
                    @php $active = request()->routeIs('master-data.clinics.*'); @endphp
                    <li>
                        <a href="{{ route('master-data.clinics.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                            </svg>
                            Cabang / Klinik
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            <!-- Administration Section -->
            @can('audit.view')
            <li>
                <div class="px-1 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Administration</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    @php $active = request()->routeIs('audit.*'); @endphp
                    <li>
                        <a href="{{ route('audit.index') }}" class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-[13px] leading-6 font-medium transition-all duration-150 {{ $active ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1 rounded-r-md bg-emerald-600"></span>
                            @endif
                            <svg class="h-5 w-5 shrink-0 {{ $active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            Audit Logs
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            <!-- User Profile Section -->
            <li class="mt-auto -mx-2 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-x-3 rounded-xl p-2 bg-slate-50 border border-slate-100 hover:bg-slate-100/70 transition-all duration-200">
                    <div class="flex-shrink-0 h-9 w-9 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-xs font-bold ring-2 ring-emerald-500/10">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0 flex items-center">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors p-1.5 rounded-lg hover:bg-white hover:shadow-sm" title="Logout">
                            <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>
</div>
