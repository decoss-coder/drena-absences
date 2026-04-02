@extends('layouts.app')
@section('title', 'Établissements')
@section('page-title', 'Établissements')
@section('page-subtitle', 'Liste des établissements de la DRENA')
@section('content')
<div class="card-white p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]"><label class="text-[11px] font-semibold text-gray-400 uppercase">Recherche</label><input type="text" name="search" value="{{ request('search') }}" class="glass-input mt-1" placeholder="Nom, code..."></div>
        <div class="w-40"><label class="text-[11px] font-semibold text-gray-400 uppercase">Ordre</label><select name="ordre" class="glass-input mt-1"><option value="">Tous</option><option value="primaire" {{ request('ordre')==='primaire'?'selected':'' }}>Primaire</option><option value="secondaire" {{ request('ordre')==='secondaire'?'selected':'' }}>Secondaire</option></select></div>
        <button type="submit" class="btn-primary">Filtrer</button>
    </form>
</div>
<div class="flex items-center justify-between mb-4"><p class="text-sm text-gray-400">{{ $etablissements->total() }} établissement(s)</p></div>
<div class="table-container"><table class="table-elegant"><thead><tr><th>Code</th><th>Nom</th><th>Ordre</th><th>Circuit</th><th>IEPP</th><th>Localité</th><th>Enseignants</th><th>Élèves</th><th></th></tr></thead><tbody>
@forelse($etablissements as $e)
<tr>
    <td class="font-mono text-xs text-gray-400">{{ $e->code }}</td>
    <td class="font-medium text-gray-800">{{ $e->nom }}</td>
    <td><span class="badge {{ $e->ordre_enseignement==='primaire'?'badge-blue':'badge-amber' }}">{{ ucfirst($e->ordre_enseignement ?? 'primaire') }}</span></td>
    <td class="text-xs text-gray-500">{{ $e->circuit_validation }}</td>
    <td class="text-xs text-gray-400">{{ $e->iepp?->nom ?? '—' }}</td>
    <td class="text-xs text-gray-400">{{ $e->localite ?? '—' }}</td>
    <td class="text-center">{{ $e->effectif_enseignants ?? 0 }}</td>
    <td class="text-center">{{ $e->effectif_eleves ?? 0 }}</td>
    <td><a href="#" class="text-sm text-violet-600 font-medium">Détail</a></td>
</tr>
@empty<tr><td colspan="9" class="text-center text-gray-400 py-8">Aucun établissement</td></tr>@endforelse
</tbody></table></div>
<div class="mt-6">{{ $etablissements->links() }}</div>
@endsection
