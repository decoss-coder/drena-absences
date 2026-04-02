@extends('layouts.app')
@section('title', 'Gestion des DRENA')
@section('page-title', 'Gestion des DRENA')
@section('page-subtitle', 'Administration des Directions Régionales')
@section('content')
<div class="flex items-center justify-between mb-6"><p class="text-sm text-gray-400">{{ $drenas->count() }} DRENA</p><a href="{{ route('admin.drenas.create') }}" class="btn-primary">Ajouter</a></div>
<div class="table-container"><table class="table-elegant"><thead><tr><th>Code</th><th>Nom</th><th>Région</th><th>Agents</th><th>Établ.</th><th>Statut</th><th></th></tr></thead><tbody>
@forelse($drenas as $d)
<tr><td class="font-mono text-xs text-gray-400">{{ $d->code }}</td><td class="font-medium text-gray-800">{{ $d->nom }}</td><td>{{ $d->region }}</td><td>{{ $d->users_count ?? 0 }}</td><td>{{ $d->etablissements_count ?? 0 }}</td><td><span class="badge {{ $d->actif?'badge-green':'badge-gray' }}">{{ $d->actif?'Active':'Inactive' }}</span></td><td class="flex gap-2"><a href="{{ route('admin.drenas.edit',$d) }}" class="text-sm text-violet-600 font-medium">Modifier</a><a href="{{ route('admin.drenas.create-admin',$d) }}" class="text-sm text-emerald-600 font-medium">Admin</a></td></tr>
@empty<tr><td colspan="7" class="text-center text-gray-400 py-8">Aucune DRENA</td></tr>@endforelse
</tbody></table></div>
@endsection
