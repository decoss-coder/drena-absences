@extends('layouts.app')
@section('title', 'Liste des absences')
@section('page-title', 'Liste des absences')
@section('page-subtitle', 'Toutes les absences de votre périmètre')
@section('content')
<div class="card-white p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]"><label class="text-[11px] font-semibold text-gray-400 uppercase">Recherche</label><input type="text" name="search" value="{{ request('search') }}" class="glass-input mt-1" placeholder="Nom, matricule, référence..."></div>
        <div class="w-40"><label class="text-[11px] font-semibold text-gray-400 uppercase">Statut</label><select name="statut" class="glass-input mt-1"><option value="">Tous</option><option value="soumise" {{ request('statut')==='soumise'?'selected':'' }}>En attente</option><option value="approuvee" {{ request('statut')==='approuvee'?'selected':'' }}>Approuvée</option><option value="refusee" {{ request('statut')==='refusee'?'selected':'' }}>Refusée</option></select></div>
        <div class="w-44"><label class="text-[11px] font-semibold text-gray-400 uppercase">Type</label><select name="type_absence_id" class="glass-input mt-1"><option value="">Tous</option>@foreach($typesAbsence as $t)<option value="{{ $t->id }}" {{ request('type_absence_id')==$t->id?'selected':'' }}>{{ $t->libelle }}</option>@endforeach</select></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Du</label><input type="date" name="date_debut" value="{{ request('date_debut') }}" class="glass-input mt-1"></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Au</label><input type="date" name="date_fin" value="{{ request('date_fin') }}" class="glass-input mt-1"></div>
        <button type="submit" class="btn-primary">Filtrer</button>
        <a href="{{ route('absences.index') }}" class="btn-secondary">Reset</a>
    </form>
</div>
<div class="flex items-center justify-between mb-4"><p class="text-sm text-gray-400">{{ $absences->total() }} absence(s)</p><a href="{{ route('absences.create') }}" class="btn-primary text-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Nouvelle</a></div>
<div class="table-container"><table class="table-elegant"><thead><tr><th>Référence</th><th>Agent</th>@hasanyrole('admin_drena|super_admin|inspecteur')<th>Établissement</th>@endhasanyrole<th>Type</th><th>Période</th><th>Jours</th><th>Statut</th><th></th></tr></thead><tbody>
@forelse($absences as $a)
<tr><td class="font-medium text-gray-800">{{ $a->reference }}</td><td><div class="flex items-center gap-2.5"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-[10px] font-bold text-white">{{ strtoupper(substr($a->user->nom,0,1).substr($a->user->prenoms,0,1)) }}</div><div><p class="text-sm font-medium text-gray-800">{{ $a->user->nom_complet }}</p><p class="text-[11px] text-gray-400">{{ $a->user->matricule }}</p></div></div></td>@hasanyrole('admin_drena|super_admin|inspecteur')<td class="text-xs text-gray-400">{{ $a->etablissement->nom }}</td>@endhasanyrole<td><span class="inline-flex items-center gap-1.5 text-sm"><span class="w-2 h-2 rounded-full" style="background:{{ $a->typeAbsence->couleur }}"></span>{{ $a->typeAbsence->libelle }}</span></td><td class="text-xs text-gray-400 whitespace-nowrap">{{ $a->date_debut->format('d/m/Y') }} — {{ $a->date_fin->format('d/m/Y') }}</td><td class="font-medium">{{ $a->nombre_jours }}j</td><td>@php $b=$a->statut_badge; @endphp<span class="badge badge-{{ $b['color'] }}">{{ $b['label'] }}</span></td><td><a href="{{ route('absences.show',$a) }}" class="text-sm text-violet-600 hover:text-violet-800 font-medium">Voir</a></td></tr>
@empty<tr><td colspan="8" class="text-center text-gray-400 py-12">Aucune absence</td></tr>@endforelse
</tbody></table></div>
<div class="mt-6">{{ $absences->links() }}</div>
@endsection
