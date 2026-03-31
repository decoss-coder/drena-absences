@extends('layouts.app')
@section('title', 'Types d\'absences')
@section('page-title', 'Configuration — Types d\'absences')
@section('page-subtitle', 'Paramètres nationaux MENA')
@section('content')
<div class="table-container mb-6">
    <table>
        <thead><tr><th>Code</th><th>Libellé</th><th>Couleur</th><th>Justif. obligatoire</th><th>Niveau validation</th><th>Durée max</th><th>Déductible congé</th><th>Actif</th></tr></thead>
        <tbody>
            @foreach($types as $t)
            <tr>
                <td class="font-mono text-xs">{{ $t->code }}</td>
                <td class="font-medium">{{ $t->libelle }}</td>
                <td><span class="inline-flex items-center gap-2"><span class="w-4 h-4 rounded" style="background:{{ $t->couleur }}"></span><span class="text-xs text-gray-500">{{ $t->couleur }}</span></span></td>
                <td>{!! $t->justificatif_obligatoire ? '<span class="badge badge-red">Oui</span>' : '<span class="badge badge-gray">Non</span>' !!}</td>
                <td>Niveau {{ $t->niveau_validation_requis }}</td>
                <td>{{ $t->duree_max_jours ? $t->duree_max_jours . 'j' : 'Illimité' }}</td>
                <td>{!! $t->deductible_conge ? '<span class="badge badge-amber">Oui</span>' : '<span class="badge badge-gray">Non</span>' !!}</td>
                <td><span class="badge {{ $t->actif ? 'badge-green' : 'badge-gray' }}">{{ $t->actif ? 'Actif' : 'Inactif' }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
