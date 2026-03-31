@extends('layouts.app')
@section('title', 'Établissements')
@section('page-title', 'Établissements')
@section('page-subtitle', 'Gestion des écoles de votre DRENA')
@section('content')
<div class="table-container">
    <table>
        <thead><tr><th>Code</th><th>Nom</th><th>Type</th><th>IEPP</th><th>Statut jur.</th><th>Agents</th><th>Statut</th></tr></thead>
        <tbody>
            @foreach($etablissements as $e)
            <tr>
                <td class="font-mono text-xs">{{ $e->code }}</td>
                <td class="font-medium">{{ $e->nom }}</td>
                <td class="text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $e->type)) }}</td>
                <td class="text-xs">{{ $e->iepp->nom }}</td>
                <td class="text-xs">{{ ucfirst(str_replace('_', ' ', $e->statut_juridique)) }}</td>
                <td>{{ $e->users_count }}</td>
                <td><span class="badge {{ $e->actif ? 'badge-green' : 'badge-gray' }}">{{ $e->actif ? 'Actif' : 'Inactif' }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $etablissements->links() }}</div>
@endsection
