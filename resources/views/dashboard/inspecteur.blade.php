@extends('layouts.app')
@section('title', 'Dashboard Inspecteur')
@section('page-title', 'Tableau de bord — Inspection')
@section('page-subtitle', auth()->user()->iepp?->nom ?? 'Mon IEPP')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Établissements</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_etablissements'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Agents</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_agents'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Absences en cours</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card border-violet-200 bg-violet-50/50"><p class="text-[11px] font-semibold text-violet-600 uppercase">À valider</p><p class="text-2xl font-bold text-violet-700 mt-2">{{ $stats['a_valider'] }}</p></div>
</div>

<div class="mb-4 px-4 py-3 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 text-sm">
    <strong>Circuit primaire :</strong> En tant qu'inspecteur, vous validez les absences des établissements <span class="font-semibold">primaires</span> de votre IEPP. Les établissements secondaires vont directement à la DRENA.
</div>

@if($demandesAValider->count()>0)
<div class="card-white p-6 mb-8 border-l-4 border-l-violet-500">
    <h3 class="text-sm font-semibold text-violet-700 mb-4">Demandes à valider ({{ $demandesAValider->count() }})</h3>
    <div class="table-container"><table class="table-elegant"><thead><tr><th>Réf.</th><th>Agent</th><th>Établissement</th><th>Type</th><th>Période</th><th>Jours</th><th></th></tr></thead><tbody>
    @foreach($demandesAValider as $d)
    <tr><td class="font-medium text-gray-800">{{ $d->reference }}</td><td>{{ $d->user->nom_complet }}</td><td class="text-xs">{{ $d->etablissement->nom }} <span class="badge badge-blue text-[10px] ml-1">Primaire</span></td><td><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $d->typeAbsence->couleur }}"></span>{{ $d->typeAbsence->libelle }}</span></td><td class="text-xs">{{ $d->date_debut->format('d/m') }}—{{ $d->date_fin->format('d/m') }}</td><td>{{ $d->nombre_jours }}j</td><td><a href="{{ route('absences.show',$d) }}" class="btn-primary text-xs py-1.5 px-3">Traiter</a></td></tr>
    @endforeach</tbody></table></div>
</div>
@else
<div class="card-white p-12 text-center mb-8"><p class="text-gray-400">Aucune demande en attente de validation</p></div>
@endif

<div class="flex gap-3"><a href="{{ route('absences.index') }}" class="btn-secondary">Toutes les absences</a><a href="{{ route('personnel.index') }}" class="btn-secondary">Personnel</a><a href="{{ route('rapports.index') }}" class="btn-primary">Rapports</a></div>
@endsection
