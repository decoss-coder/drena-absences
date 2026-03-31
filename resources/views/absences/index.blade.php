@extends('layouts.app')
@section('title', 'Liste des absences')
@section('page-title', 'Liste des absences')
@section('page-subtitle', 'Toutes les absences de votre périmètre')

@section('content')
{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('absences.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="text-xs font-medium text-gray-500">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}" class="input mt-1" placeholder="Nom, matricule, référence...">
        </div>
        <div class="w-40">
            <label class="text-xs font-medium text-gray-500">Statut</label>
            <select name="statut" class="input mt-1">
                <option value="">Tous</option>
                <option value="soumise" {{ request('statut') === 'soumise' ? 'selected' : '' }}>En attente</option>
                <option value="approuvee" {{ request('statut') === 'approuvee' ? 'selected' : '' }}>Approuvée</option>
                <option value="refusee" {{ request('statut') === 'refusee' ? 'selected' : '' }}>Refusée</option>
                <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>
        <div class="w-44">
            <label class="text-xs font-medium text-gray-500">Type</label>
            <select name="type_absence_id" class="input mt-1">
                <option value="">Tous</option>
                @foreach($typesAbsence as $t)
                    <option value="{{ $t->id }}" {{ request('type_absence_id') == $t->id ? 'selected' : '' }}>{{ $t->libelle }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="text-xs font-medium text-gray-500">Du</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input mt-1">
        </div>
        <div class="w-36">
            <label class="text-xs font-medium text-gray-500">Au</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input mt-1">
        </div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="{{ route('absences.index') }}" class="btn btn-secondary">Réinitialiser</a>
    </form>
</div>

{{-- Actions --}}
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">{{ $absences->total() }} absence(s) trouvée(s)</p>
    <a href="{{ route('absences.create') }}" class="btn btn-primary text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvelle absence
    </a>
</div>

{{-- Table --}}
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Agent</th>
                @hasanyrole('admin_drena|super_admin|inspecteur')
                <th>Établissement</th>
                @endhasanyrole
                <th>Type</th>
                <th>Période</th>
                <th>Jours</th>
                <th>Statut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($absences as $absence)
            <tr>
                <td class="font-medium text-gray-900">{{ $absence->reference }}</td>
                <td>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-xs font-bold text-primary-700">
                            {{ strtoupper(substr($absence->user->nom, 0, 1) . substr($absence->user->prenoms, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $absence->user->nom_complet }}</p>
                            <p class="text-xs text-gray-500">{{ $absence->user->matricule }}</p>
                        </div>
                    </div>
                </td>
                @hasanyrole('admin_drena|super_admin|inspecteur')
                <td class="text-xs text-gray-600">{{ $absence->etablissement->nom }}</td>
                @endhasanyrole
                <td>
                    <span class="inline-flex items-center gap-1.5 text-sm">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background:{{ $absence->typeAbsence->couleur }}"></span>
                        {{ $absence->typeAbsence->libelle }}
                    </span>
                </td>
                <td class="text-xs text-gray-600 whitespace-nowrap">{{ $absence->date_debut->format('d/m/Y') }} — {{ $absence->date_fin->format('d/m/Y') }}</td>
                <td class="text-sm">{{ $absence->nombre_jours }}j</td>
                <td>@php $b = $absence->statut_badge; @endphp <span class="badge badge-{{ $b['color'] }}">{{ $b['label'] }}</span></td>
                <td><a href="{{ route('absences.show', $absence) }}" class="text-sm text-blue-600 hover:underline">Voir</a></td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-gray-500 py-12">Aucune absence trouvée</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-6">{{ $absences->links() }}</div>
@endsection
