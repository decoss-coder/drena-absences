@extends('layouts.app')
@section('title', 'Dashboard Chef d\'établissement')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', auth()->user()->etablissement?->nom ?? 'Mon établissement')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Enseignants</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_enseignants'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Absents aujourd'hui</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $stats['absences_du_jour'] }}</p></div>
    <div class="stat-card border-violet-200 bg-violet-50/50"><p class="text-[11px] font-semibold text-violet-600 uppercase">À valider</p><p class="text-2xl font-bold text-violet-700 mt-2">{{ $stats['a_valider'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Suppléances</p><p class="text-2xl font-bold text-blue-500 mt-2">{{ $stats['suppleances_en_cours'] }}</p></div>
</div>
@if($demandesAValider->count()>0)
<div class="card-white p-6 mb-8 border-l-4 border-l-violet-500">
    <h3 class="text-sm font-semibold text-violet-700 mb-4 flex items-center gap-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Demandes en attente ({{ $demandesAValider->count() }})</h3>
    <div class="space-y-3">@foreach($demandesAValider as $d)
    <div class="flex items-center justify-between p-4 rounded-2xl bg-violet-50/60 border border-violet-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-sm font-bold text-white shadow-md">{{ strtoupper(substr($d->user->nom,0,1).substr($d->user->prenoms,0,1)) }}</div>
            <div><p class="text-sm font-medium text-gray-800">{{ $d->user->nom_complet }}</p><p class="text-xs text-gray-400">{{ $d->typeAbsence->libelle }} — {{ $d->date_debut->format('d/m') }} au {{ $d->date_fin->format('d/m/Y') }} ({{ $d->nombre_jours }}j)</p></div>
        </div>
        <a href="{{ route('absences.show',$d) }}" class="btn-primary text-xs py-2 px-3">Traiter</a>
    </div>@endforeach</div>
</div>
@endif
<div class="card-white p-6 mb-8">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Personnel absent aujourd'hui</h3>
    @if($personnelAbsentAujourdhui->count()>0)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">@foreach($personnelAbsentAujourdhui as $abs)
    <div class="flex items-center gap-3 p-3 rounded-2xl bg-red-50/60 border border-red-100">
        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-600">{{ $abs->user->initiales }}</div>
        <div><p class="text-sm font-medium text-gray-800">{{ $abs->user->nom_complet }}</p><p class="text-xs text-gray-400">{{ $abs->user->specialite ?? 'N/A' }} — retour {{ $abs->date_fin->format('d/m') }}</p></div>
    </div>@endforeach</div>
    @else<p class="text-sm text-gray-400 text-center py-6">Tout le personnel est présent</p>@endif
</div>
<div class="flex gap-3"><a href="{{ route('absences.create') }}" class="btn-primary">Déclarer pour un agent</a><a href="{{ route('personnel.index') }}" class="btn-secondary">Personnel</a><a href="{{ route('absences.calendrier') }}" class="btn-secondary">Calendrier</a></div>
@endsection
