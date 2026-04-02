@extends('layouts.app')
@section('title', 'Dashboard Gestionnaire RH')
@section('page-title', 'Tableau de bord RH')
@section('page-subtitle', auth()->user()->drena?->nom ?? 'Ma DRENA')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Agents actifs</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Absences en cours</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Congés en cours</p><p class="text-2xl font-bold text-amber-500 mt-2">{{ $stats['conges_en_cours'] }}</p></div>
</div>
<div class="grid lg:grid-cols-2 gap-6">
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-3">Actions rapides</h3>
        <div class="space-y-2">
            <a href="{{ route('personnel.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-violet-50 transition"><div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/></svg></div><div><p class="text-sm font-medium text-gray-700">Gestion du personnel</p><p class="text-xs text-gray-400">Consulter, ajouter, modifier</p></div></a>
            <a href="{{ route('rapports.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-violet-50 transition"><div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/></svg></div><div><p class="text-sm font-medium text-gray-700">Rapports & Statistiques</p><p class="text-xs text-gray-400">Exporter PDF, Excel</p></div></a>
            <a href="{{ route('absences.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-violet-50 transition"><div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center"><svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg></div><div><p class="text-sm font-medium text-gray-700">Absences</p><p class="text-xs text-gray-400">Suivi des absences</p></div></a>
        </div>
    </div>
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-3">Informations</h3>
        <div class="space-y-3 text-sm text-gray-500">
            <p>Les circuits de validation dépendent du type d'établissement :</p>
            <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 text-xs"><strong>Primaire :</strong> Chef → Inspecteur → DRENA</div>
            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 text-xs"><strong>Secondaire :</strong> Chef → DRENA (pas d'inspecteur)</div>
        </div>
    </div>
</div>
@endsection
