@extends('layouts.app')
@section('title', 'Gestion des DRENA')
@section('page-title', 'Gestion des DRENA')
@section('page-subtitle', 'Administration nationale — MENA')
@section('content')
<div class="flex justify-between mb-6">
    <p class="text-sm text-gray-500">{{ $drenas->total() }} DRENA enregistrées</p>
    <a href="{{ route('admin.drenas.create') }}" class="btn btn-primary text-sm">Créer une DRENA</a>
</div>
<div class="table-container"><table><thead><tr><th>Code</th><th>Nom</th><th>Région</th><th>IEPP</th><th>Établ.</th><th>Agents</th><th>Statut</th><th>Actions</th></tr></thead><tbody>
@foreach($drenas as $d)
<tr>
    <td class="font-mono text-xs">{{ $d->code }}</td>
    <td class="font-medium">{{ $d->nom }}</td>
    <td>{{ $d->region }}</td>
    <td>{{ $d->iepps_count }}</td>
    <td>{{ $d->etablissements_count }}</td>
    <td>{{ $d->users_count }}</td>
    <td><span class="badge {{ $d->actif ? 'badge-green' : 'badge-gray' }}">{{ $d->actif ? 'Active' : 'Inactive' }}</span></td>
    <td class="space-x-2">
        <a href="{{ route('admin.drenas.edit', $d) }}" class="text-sm text-blue-600 hover:underline">Modifier</a>
        <a href="{{ route('admin.drenas.create-admin', $d) }}" class="text-sm text-emerald-600 hover:underline">+ Admin</a>
    </td>
</tr>
@endforeach
</tbody></table></div>
<div class="mt-6">{{ $drenas->links() }}</div>
@endsection
