@extends('layouts.app')
@section('title', 'Dashboard Chef d\'établissement')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', auth()->user()->etablissement?->nom ?? 'Mon établissement')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Enseignants</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_enseignants'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Absents aujourd'hui</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['absences_du_jour'] }}</p>
    </div>
    <div class="stat-card border-2 border-amber-200 bg-amber-50">
        <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">À valider</p>
        <p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['a_valider'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Suppléances en cours</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['suppleances_en_cours'] }}</p>
    </div>
</div>

{{-- Demandes à valider --}}
@if($demandesAValider->count() > 0)
<div class="bg-white rounded-xl border-2 border-amber-200 p-6 mb-8">
    <h3 class="text-sm font-semibold text-amber-800 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Demandes en attente de validation ({{ $demandesAValider->count() }})
    </h3>
    <div class="space-y-3">
        @foreach($demandesAValider as $demande)
        <div class="flex items-center justify-between p-4 bg-amber-50 rounded-lg border border-amber-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-sm font-bold text-amber-700">
                    {{ strtoupper(substr($demande->user->nom, 0, 1) . substr($demande->user->prenoms, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $demande->user->nom_complet }}</p>
                    <p class="text-xs text-gray-600">
                        {{ $demande->typeAbsence->libelle }} —
                        {{ $demande->date_debut->format('d/m') }} au {{ $demande->date_fin->format('d/m/Y') }}
                        ({{ $demande->nombre_jours }}j)
                    </p>
                </div>
            </div>
            <a href="{{ route('absences.show', $demande) }}" class="btn btn-primary text-xs py-1.5 px-3">Traiter</a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Personnel absent aujourd'hui --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Personnel absent aujourd'hui</h3>
    @if($personnelAbsentAujourdhui->count() > 0)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($personnelAbsentAujourdhui as $abs)
        <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-100">
            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-700">
                {{ $abs->user->initiales }}
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $abs->user->nom_complet }}</p>
                <p class="text-xs text-gray-500">{{ $abs->user->specialite ?? 'N/A' }} — retour {{ $abs->date_fin->format('d/m') }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-sm text-gray-500 text-center py-6">Tout le personnel est présent aujourd'hui.</p>
    @endif
</div>

{{-- Quick Actions --}}
<div class="flex gap-3">
    <a href="{{ route('absences.create') }}" class="btn btn-primary">Déclarer une absence pour un agent</a>
    <a href="{{ route('personnel.index') }}" class="btn btn-secondary">Voir le personnel</a>
    <a href="{{ route('absences.calendrier') }}" class="btn btn-secondary">Calendrier du mois</a>
</div>
@endsection
