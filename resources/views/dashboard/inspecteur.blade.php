@extends('layouts.app')
@section('title', 'Dashboard Inspecteur')
@section('page-title', 'Tableau de bord — Inspection')
@section('page-subtitle', auth()->user()->iepp?->nom ?? 'Ma circonscription')

@section('content')
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Établissements</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_etablissements'] }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Absences en cours</p><p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card border-2 border-amber-200 bg-amber-50"><p class="text-xs font-medium text-amber-700 uppercase">À valider (N2)</p><p class="text-2xl font-bold text-amber-700 mt-1">{{ $stats['a_valider'] }}</p></div>
</div>

@if($demandesAValider->count() > 0)
<div class="bg-white rounded-xl border-2 border-amber-200 p-6 mb-8">
    <h3 class="text-sm font-semibold text-amber-800 mb-4">Demandes à valider — Niveau 2</h3>
    <div class="table-container">
        <table>
            <thead><tr><th>Réf.</th><th>Agent</th><th>Établissement</th><th>Type</th><th>Période</th><th>Jours</th><th></th></tr></thead>
            <tbody>
                @foreach($demandesAValider as $d)
                <tr>
                    <td class="font-medium">{{ $d->reference }}</td>
                    <td>{{ $d->user->nom_complet }}</td>
                    <td class="text-xs">{{ $d->etablissement->nom }}</td>
                    <td>{{ $d->typeAbsence->libelle }}</td>
                    <td class="text-xs">{{ $d->date_debut->format('d/m') }} — {{ $d->date_fin->format('d/m') }}</td>
                    <td>{{ $d->nombre_jours }}j</td>
                    <td><a href="{{ route('absences.show', $d) }}" class="btn btn-primary text-xs py-1 px-2.5">Traiter</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Absences par établissement</h3>
    <div class="table-container">
        <table>
            <thead><tr><th>Établissement</th><th>Type</th><th>Absences en cours</th></tr></thead>
            <tbody>
                @foreach($absencesParEtablissement as $etab)
                <tr>
                    <td class="font-medium">{{ $etab->nom }}</td>
                    <td class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $etab->type)) }}</td>
                    <td><span class="badge {{ $etab->absences_count > 3 ? 'badge-red' : ($etab->absences_count > 0 ? 'badge-amber' : 'badge-green') }}">{{ $etab->absences_count }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
