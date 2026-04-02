@extends('layouts.app')
@section('title', 'Personnel')
@section('page-title', 'Gestion du personnel')
@section('page-subtitle', 'Liste des agents et enseignants')
@section('content')
<div class="card-white p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]"><label class="text-[11px] font-semibold text-gray-400 uppercase">Recherche</label><input type="text" name="search" value="{{ request('search') }}" class="glass-input mt-1" placeholder="Nom, matricule..."></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Statut</label><select name="statut" class="glass-input mt-1"><option value="">Tous</option><option value="actif" {{ request('statut')==='actif'?'selected':'' }}>Actif</option><option value="conge" {{ request('statut')==='conge'?'selected':'' }}>En congé</option></select></div>
        <button type="submit" class="btn-primary">Filtrer</button>
    </form>
</div>
<div class="flex justify-between mb-4"><p class="text-sm text-gray-400">{{ $personnel->total() }} agent(s)</p>@hasanyrole('admin_drena|super_admin|gestionnaire_rh')<a href="{{ route('personnel.create') }}" class="btn-primary text-sm">Ajouter un agent</a>@endhasanyrole</div>
<div class="table-container"><table class="table-elegant"><thead><tr><th>Matricule</th><th>Agent</th><th>Spécialité</th><th>Établissement</th><th>Ordre</th><th>Statut</th><th></th></tr></thead><tbody>
@forelse($personnel as $p)
<tr><td class="font-mono text-xs text-gray-400">{{ $p->matricule }}</td><td><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-[10px] font-bold text-white">{{ $p->initiales }}</div><div><p class="text-sm font-medium text-gray-800">{{ $p->nom_complet }}</p><p class="text-[11px] text-gray-400">{{ $p->grade ?? '' }}</p></div></div></td><td class="text-sm text-gray-500">{{ $p->specialite ?? '—' }}</td><td class="text-xs text-gray-400">{{ $p->etablissement?->nom ?? '—' }}</td><td><span class="badge {{ ($p->etablissement?->ordre_enseignement ?? 'primaire')==='primaire'?'badge-blue':'badge-amber' }} text-[10px]">{{ ucfirst($p->etablissement?->ordre_enseignement ?? '—') }}</span></td><td><span class="badge {{ $p->statut==='actif'?'badge-green':'badge-gray' }}">{{ ucfirst($p->statut) }}</span></td><td><a href="{{ route('personnel.show',$p) }}" class="text-sm text-violet-600 hover:text-violet-800 font-medium">Voir</a></td></tr>
@empty<tr><td colspan="7" class="text-center text-gray-400 py-8">Aucun agent</td></tr>@endforelse
</tbody></table></div>
<div class="mt-6">{{ $personnel->links() }}</div>
@endsection
