<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DRENA Absences')</title>
    <link rel="manifest" href="/manifest.json">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{v:{50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',400:'#a78bfa',500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95'}}}}}</script>
    <style>
        *{font-family:'Inter',sans-serif}
        [x-cloak]{display:none!important}
        body{background:#f8f7ff}
        .sidebar-link{display:flex;align-items:center;gap:12px;padding:10px 16px;font-size:13.5px;border-radius:12px;transition:all .2s;color:#6b7280;font-weight:450}
        .sidebar-link:hover{background:#f3f0ff;color:#6d28d9}
        .sidebar-link.active{background:linear-gradient(135deg,#7c3aed,#6246ea);color:#fff;box-shadow:0 4px 12px rgba(124,58,237,0.2)}
        .sidebar-link.active svg{color:#fff}
        .stat-card{background:#fff;border-radius:18px;padding:22px;border:1px solid #f0ecff;box-shadow:0 2px 12px rgba(100,80,200,0.04);transition:all .3s}
        .stat-card:hover{box-shadow:0 8px 30px rgba(100,80,200,0.08);transform:translateY(-2px)}
        .card-white{background:#fff;border-radius:18px;border:1px solid #f0ecff;box-shadow:0 2px 12px rgba(100,80,200,0.04)}
        .glass-input{width:100%;padding:10px 14px;border:1.5px solid #e8e5f5;border-radius:12px;font-size:13.5px;color:#2d2640;background:#faf9ff;transition:all .3s}
        .glass-input:focus{outline:none;border-color:#7c6aef;box-shadow:0 0 0 4px rgba(124,106,239,0.08);background:#fff}
        .glass-input::placeholder{color:#b0a8cc}
        .glass-input option{background:#fff;color:#2d2640}
        select.glass-input{-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%239ca3af' d='M6 8L1 3h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:36px}
        .btn-primary{background:linear-gradient(135deg,#7c3aed,#6246ea);color:#fff;padding:10px 22px;border-radius:12px;font-size:13.5px;font-weight:600;transition:all .3s;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(98,70,234,0.2)}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(98,70,234,0.3)}
        .btn-secondary{background:#fff;border:1.5px solid #e8e5f5;color:#5b21b6;padding:10px 22px;border-radius:12px;font-size:13.5px;font-weight:500;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
        .btn-secondary:hover{background:#f5f3ff;border-color:#c4b5fd}
        .btn-danger{background:#fff0f0;border:1.5px solid #fecaca;color:#dc2626;padding:10px 22px;border-radius:12px;font-size:13.5px;font-weight:500;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
        .btn-danger:hover{background:#fee2e2}
        .btn-success{background:#ecfdf5;border:1.5px solid #a7f3d0;color:#059669;padding:10px 22px;border-radius:12px;font-size:13.5px;font-weight:500;transition:all .2s;display:inline-flex;align-items:center;gap:8px}
        .btn-success:hover{background:#d1fae5}
        .table-container{overflow-x:auto;border-radius:16px;border:1px solid #f0ecff;background:#fff}
        .table-elegant{width:100%;font-size:13.5px}
        .table-elegant thead{background:#faf9ff}
        .table-elegant th{padding:12px 16px;text-align:left;font-size:11px;font-weight:600;color:#8b7fad;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #f0ecff}
        .table-elegant td{padding:14px 16px;color:#4a4363;border-bottom:1px solid #faf7ff}
        .table-elegant tbody tr{transition:background .2s}
        .table-elegant tbody tr:hover{background:#faf9ff}
        .badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
        .badge-amber{background:#fffbeb;color:#d97706;border:1px solid #fde68a}
        .badge-green,.badge-emerald{background:#ecfdf5;color:#059669;border:1px solid #a7f3d0}
        .badge-red{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}
        .badge-blue{background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe}
        .badge-gray{background:#f9fafb;color:#6b7280;border:1px solid #e5e7eb}
        .label{display:block;font-size:13.5px;font-weight:500;color:#6b7280;margin-bottom:6px}
        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#e0dcf0;border-radius:3px}
    </style>
    @stack('styles')
</head>
<body class="text-gray-700 antialiased">
<div class="flex h-screen overflow-hidden" x-data="{sidebarOpen:window.innerWidth>=1024}">

    <aside class="fixed inset-y-0 left-0 z-30 w-[260px] bg-white border-r border-violet-100/60 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-sm"
           :class="sidebarOpen?'translate-x-0':'-translate-x-full'">

        <div class="flex items-center gap-3 px-5 py-5 border-b border-violet-50">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shadow-md shadow-violet-500/15">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div><h1 class="text-[14px] font-bold text-gray-800">DRENA Absences</h1><p class="text-[11px] text-gray-400">{{ auth()->user()->drena?->nom ?? 'MENA National' }}</p></div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('dashboard')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>Tableau de bord</a>

            <p class="px-4 pt-5 pb-2 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Absences</p>
            <a href="{{ route('absences.index') }}" class="sidebar-link {{ request()->routeIs('absences.index')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('absences.index')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Liste des absences</a>
            <a href="{{ route('absences.create') }}" class="sidebar-link {{ request()->routeIs('absences.create')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('absences.create')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>Déclarer une absence</a>
            <a href="{{ route('absences.calendrier') }}" class="sidebar-link {{ request()->routeIs('absences.calendrier')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('absences.calendrier')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Calendrier</a>

            @hasanyrole('chef_etablissement|inspecteur|gestionnaire_rh|admin_drena|super_admin')
            <p class="px-4 pt-5 pb-2 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Gestion</p>
            <a href="{{ route('personnel.index') }}" class="sidebar-link {{ request()->routeIs('personnel.*')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('personnel.*')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Personnel</a>
            @endhasanyrole

            @hasanyrole('inspecteur|gestionnaire_rh|admin_drena|super_admin')
            <a href="{{ route('rapports.index') }}" class="sidebar-link {{ request()->routeIs('rapports.*')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('rapports.*')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>Rapports</a>
            @endhasanyrole

            @hasrole('super_admin')
            <p class="px-4 pt-5 pb-2 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Administration</p>
            <a href="{{ route('admin.drenas.index') }}" class="sidebar-link {{ request()->routeIs('admin.drenas.*')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('admin.drenas.*')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Gestion DRENA</a>
            <a href="{{ route('admin.types-absence.index') }}" class="sidebar-link {{ request()->routeIs('admin.types-absence.*')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('admin.types-absence.*')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Configuration</a>
            <a href="{{ route('admin.audit') }}" class="sidebar-link {{ request()->routeIs('admin.audit')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('admin.audit')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>Audit & Logs</a>
            @endhasrole

            @hasrole('admin_drena')
            <p class="px-4 pt-5 pb-2 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Ma DRENA</p>
            <a href="{{ route('gestion.etablissements.index') }}" class="sidebar-link {{ request()->routeIs('gestion.*')?'active':'' }}"><svg class="w-[18px] h-[18px] {{ request()->routeIs('gestion.*')?'text-white':'text-violet-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>Établissements</a>
            @endhasrole
        </nav>

        <div class="border-t border-violet-50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-violet-500/15">{{ auth()->user()->initiales }}</div>
                <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->nom_complet }}</p><p class="text-[11px] text-gray-400 truncate">{{ auth()->user()->getRoleNames()->first() }}</p></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="p-1.5 text-gray-300 hover:text-red-500 rounded-lg hover:bg-red-50 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></button></form>
            </div>
        </div>
    </aside>

    <div class="fixed inset-0 bg-black/20 backdrop-blur-sm z-20 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen=false" x-cloak></div>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white/70 backdrop-blur-md border-b border-violet-100/40 px-4 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen=!sidebarOpen" class="lg:hidden p-2 rounded-xl hover:bg-violet-50"><svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                <div><h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Tableau de bord')</h2><p class="text-xs text-gray-400">@yield('page-subtitle', 'Bienvenue, ' . auth()->user()->prenoms)</p></div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="relative p-2.5 rounded-xl hover:bg-violet-50 transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if(auth()->user()->unreadNotifications->count()>0)<span class="absolute top-1 right-1 w-4.5 h-4.5 bg-gradient-to-r from-violet-500 to-pink-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">{{ auth()->user()->unreadNotifications->count() }}</span>@endif
                </a>
                <a href="{{ route('profil') }}" class="p-2.5 rounded-xl hover:bg-violet-50 transition"><svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></a>
            </div>
        </header>

        @if(session('success'))
        <div class="mx-4 lg:mx-8 mt-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2" id="flash-success">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ session('success') }}
            <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-500 hover:text-emerald-700 text-lg">&times;</button>
        </div>
        @endif
        @if($errors->any())
        <div class="mx-4 lg:mx-8 mt-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <main class="flex-1 overflow-y-auto p-4 lg:p-8">@yield('content')</main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
