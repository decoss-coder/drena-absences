@extends('layouts.app')
@section('title', 'Années scolaires')
@section('page-title', 'Années scolaires')
@section('content')
<div class="table-container"><table class="table-elegant"><thead><tr><th>Libellé</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead><tbody>
@forelse($annees as $a)
<tr><td class="font-medium text-gray-800">{{ $a->libelle }}</td><td>{{ $a->date_debut->format('d/m/Y') }}</td><td>{{ $a->date_fin->format('d/m/Y') }}</td><td><span class="badge {{ $a->en_cours?'badge-green':'badge-gray' }}">{{ $a->en_cours?'En cours':'Terminée' }}</span></td></tr>
@empty<tr><td colspan="4" class="text-center text-gray-400 py-8">Aucune année</td></tr>@endforelse
</tbody></table></div>
@endsection
