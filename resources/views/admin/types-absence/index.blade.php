@extends('layouts.app')
@section('title', 'Types d\'absence')
@section('page-title', 'Configuration — Types d\'absence')
@section('content')
<div class="table-container"><table class="table-elegant"><thead><tr><th>Code</th><th>Libellé</th><th>Couleur</th><th>Justif. obligatoire</th><th>Déductible congé</th><th>Niveau validation</th><th>Actif</th></tr></thead><tbody>
@forelse($types as $t)
<tr><td class="font-mono text-xs text-gray-400">{{ $t->code }}</td><td class="font-medium text-gray-800">{{ $t->libelle }}</td><td><span class="inline-flex items-center gap-1.5"><span class="w-4 h-4 rounded" style="background:{{ $t->couleur }}"></span>{{ $t->couleur }}</span></td><td>{{ $t->justificatif_obligatoire?'Oui':'Non' }}</td><td>{{ $t->deductible_conge?'Oui':'Non' }}</td><td><span class="badge badge-blue">{{ $t->niveau_validation_requis }}</span></td><td><span class="badge {{ $t->actif?'badge-green':'badge-gray' }}">{{ $t->actif?'Oui':'Non' }}</span></td></tr>
@empty<tr><td colspan="7" class="text-center text-gray-400 py-8">Aucun type</td></tr>@endforelse
</tbody></table></div>
@endsection
