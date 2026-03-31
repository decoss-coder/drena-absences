@extends('layouts.app')
@section('title', 'Personnel')
@section('page-title', 'Gestion du personnel')
@section('page-subtitle', 'Liste des agents et enseignants')
@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]"><label class="text-xs font-medium text-gray-500">Recherche</label><input type="text" name="search" value="{{ request('search') }}" class="input mt-1" placeholder="Nom, matricule..."></div>
        <div class="w-36"><label class="text-xs font-medium text-gray-500">Statut</label><select name="statut" class="input mt-1"><option value="">Tous</option><option value="actif" {{ request('statut')==='actif'?'selected':'' }}>Actif</option><option value="conge" {{ request('statut')==='conge'?'selected':'' }}>En congé</option></select></div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>
</div>
<div class="flex justify-between mb-4"><p class="text-sm text-gray-500">{{ $personnel->total() }} agent(s)</p>
@hasanyrole('admin_drena|super_admin|gestionnaire_rh')<a href="{{ route('personnel.create') }}" class="btn btn-primary text-sm">Ajouter un agent</a>@endhasanyrole</div>
<div class="table-container"><table><thead><tr><th>Matricule</th><th>Agent</th><th>Spécialité</th><th>Établissement</th><th>Statut</th><th></th></tr></thead><tbody>
@forelse($personnel as $p)
<tr><td class="font-mono text-xs">{{ $p->matricule }}</td><td><div class="flex items-center gap-2"><div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700">{{ $p->initiales }}</div><div><p class="text-sm font-medium">{{ $p->nom_complet }}</p><p class="text-xs text-gray-500">{{ $p->grade ?? '' }}</p></div></div></td><td class="text-sm">{{ $p->specialite ?? '—' }}</td><td class="text-xs text-gray-600">{{ $p->etablissement?->nom ?? '—' }}</td><td><span class="badge {{ $p->statut==='actif'?'badge-green':'badge-gray' }}">{{ ucfirst($p->statut) }}</span></td><td><a href="{{ route('personnel.show', $p) }}" class="text-sm text-blue-600 hover:underline">Voir</a></td></tr>
@empty<tr><td colspan="6" class="text-center text-gray-500 py-8">Aucun agent</td></tr>@endforelse
</tbody></table></div>
<div class="mt-6">{{ $personnel->links() }}</div>
@endsection
