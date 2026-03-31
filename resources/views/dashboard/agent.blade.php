@extends('layouts.app')
@section('title', 'Mon tableau de bord')
@section('page-title', 'Mon tableau de bord')
@section('page-subtitle', 'Bienvenue, ' . auth()->user()->prenoms . ' — ' . (auth()->user()->etablissement?->nom ?? ''))

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Absences cette année</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_absences'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jours d'absence</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['jours_absences'] }} j</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En attente</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['en_attente'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Solde congés</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['solde_conge'] }} j</p>
    </div>
</div>

<div class="flex gap-3 mb-8">
    <a href="{{ route('absences.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Déclarer une absence
    </a>
    <a href="{{ route('absences.calendrier') }}" class="btn btn-secondary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Calendrier
    </a>
</div>

{{-- Absence en cours --}}
@if($absenceEnCours)
<div class="bg-amber-50 rounded-xl border border-amber-200 p-5 mb-8">
    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-900">Absence en cours — {{ $absenceEnCours->typeAbsence->libelle }}</p>
            <p class="text-sm text-amber-800 mt-1">
                Du {{ $absenceEnCours->date_debut->format('d/m/Y') }} au {{ $absenceEnCours->date_fin->format('d/m/Y') }}
                ({{ $absenceEnCours->nombre_jours }} jour(s))
            </p>
            @if($absenceEnCours->suppleance)
            <p class="text-xs text-amber-700 mt-2">
                Suppléant : {{ $absenceEnCours->suppleance->suppleant->nom_complet }}
            </p>
            @endif
        </div>
    </div>
</div>
@endif

{{-- Historique --}}
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Mes dernières absences</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Jours</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($mesAbsences as $absence)
                <tr>
                    <td class="font-medium text-gray-900">{{ $absence->reference }}</td>
                    <td>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $absence->typeAbsence->couleur }}"></span>
                            {{ $absence->typeAbsence->libelle }}
                        </span>
                    </td>
                    <td class="text-gray-600">{{ $absence->date_debut->format('d/m/Y') }} — {{ $absence->date_fin->format('d/m/Y') }}</td>
                    <td>{{ $absence->nombre_jours }}j</td>
                    <td>
                        @php $badge = $absence->statut_badge; @endphp
                        <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                    </td>
                    <td>
                        <a href="{{ route('absences.show', $absence) }}" class="text-sm text-blue-600 hover:underline">Voir</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-gray-500 py-8">Aucune absence déclarée</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
