<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DRENA Absences') — Gestion des Absences</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1B4F72">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#EBF5FB', 100:'#D6EAF8', 200:'#AED6F1', 300:'#85C1E9', 400:'#5DADE2', 500:'#3498DB', 600:'#2E86C1', 700:'#2471A3', 800:'#1A5276', 900:'#1B4F72' },
                        accent: { 50:'#FEF9E7', 100:'#FCF3CF', 400:'#F4D03F', 500:'#F1C40F', 600:'#D4AC0D' },
                        drena: { 50:'#F0FFF4', 100:'#C6F6D5', 500:'#48BB78', 600:'#38A169', 700:'#2F855A' }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all duration-200; }
        .sidebar-link:hover { @apply bg-primary-50 text-primary-700; }
        .sidebar-link.active { @apply bg-primary-100 text-primary-800 font-medium; }
        .stat-card { @apply bg-white rounded-xl border border-gray-100 p-5 hover:shadow-md transition-shadow; }
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
        .badge-amber { @apply bg-amber-100 text-amber-800; }
        .badge-green { @apply bg-emerald-100 text-emerald-800; }
        .badge-red { @apply bg-red-100 text-red-800; }
        .badge-blue { @apply bg-blue-100 text-blue-800; }
        .badge-gray { @apply bg-gray-100 text-gray-700; }
        .btn { @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2; }
        .btn-primary { @apply bg-primary-700 text-white hover:bg-primary-800 focus:ring-primary-500; }
        .btn-secondary { @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-gray-300; }
        .btn-danger { @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500; }
        .btn-success { @apply bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500; }
        .input { @apply w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition; }
        .label { @apply block text-sm font-medium text-gray-700 mb-1; }
        .table-container { @apply overflow-x-auto rounded-xl border border-gray-200; }
        .table-container table { @apply w-full text-sm; }
        .table-container thead { @apply bg-gray-50; }
        .table-container th { @apply px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider; }
        .table-container td { @apply px-4 py-3.5 text-gray-700 border-t border-gray-100; }
        .table-container tbody tr:hover { @apply bg-gray-50; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    {{-- ═══════ SIDEBAR ═══════ --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex flex-col h-full">
            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <div class="w-10 h-10 rounded-xl bg-primary-700 flex items-center justify-center">
                    <span class="text-white text-lg font-bold">D</span>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-gray-900">DRENA Absences</h1>
                    <p class="text-xs text-gray-500">{{ auth()->user()->drena?->nom ?? 'MENA National' }}</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Tableau de bord
                </a>

                <p class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase">Absences</p>
                <a href="{{ route('absences.index') }}" class="sidebar-link {{ request()->routeIs('absences.index') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Liste des absences
                </a>
                <a href="{{ route('absences.create') }}" class="sidebar-link {{ request()->routeIs('absences.create') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                    Déclarer une absence
                </a>
                <a href="{{ route('absences.calendrier') }}" class="sidebar-link {{ request()->routeIs('absences.calendrier') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Calendrier
                </a>

                @hasanyrole('chef_etablissement|inspecteur|gestionnaire_rh|admin_drena|super_admin')
                <p class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase">Gestion</p>
                <a href="{{ route('personnel.index') }}" class="sidebar-link {{ request()->routeIs('personnel.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Personnel
                </a>
                @endhasanyrole

                @hasanyrole('inspecteur|gestionnaire_rh|admin_drena|super_admin')
                <a href="{{ route('rapports.index') }}" class="sidebar-link {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Rapports & Stats
                </a>
                @endhasanyrole

                @hasrole('super_admin')
                <p class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase">Administration</p>
                <a href="{{ route('admin.drenas.index') }}" class="sidebar-link {{ request()->routeIs('admin.drenas.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Gestion des DRENA
                </a>
                <a href="{{ route('admin.types-absence.index') }}" class="sidebar-link {{ request()->routeIs('admin.types-absence.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Configuration
                </a>
                <a href="{{ route('admin.audit') }}" class="sidebar-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Audit & Logs
                </a>
                @endhasrole

                @hasrole('admin_drena')
                <p class="px-4 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase">Ma DRENA</p>
                <a href="{{ route('gestion.etablissements.index') }}" class="sidebar-link {{ request()->routeIs('gestion.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Établissements
                </a>
                @endhasrole
            </nav>

            {{-- User --}}
            <div class="border-t border-gray-100 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-sm font-bold">
                        {{ auth()->user()->initiales }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->nom_complet }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->getRoleNames()->first() }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div class="fixed inset-0 bg-black/30 z-20 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak></div>

    {{-- ═══════ MAIN CONTENT ═══════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Tableau de bord')</h2>
                    <p class="text-xs text-gray-500">@yield('page-subtitle', 'Bienvenue, ' . auth()->user()->prenoms)</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('profil') }}" class="p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="mx-4 lg:mx-8 mt-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2" id="flash-success">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
            <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
        @endif
        @if($errors->any())
        <div class="mx-4 lg:mx-8 mt-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-8">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
