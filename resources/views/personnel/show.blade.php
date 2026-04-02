@extends('layouts.app')
@section('title', $agent->nom_complet)
@section('page-title', $agent->nom_complet)
@section('page-subtitle', $agent->matricule . ' — ' . ($agent->etablissement?->nom ?? 'Non affecté'))
@section('content')
<div class="max-w-4xl">
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 card-white p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-violet-500/15">{{ $agent->initiales }}</div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $agent->nom_complet }}</h2>
                <p class="text-sm text-gray-400">{{ $agent->grade ?? 'N/A' }} — Échelon {{ $agent->echelon ?? 'N/A' }}</p>
                <span class="badge {{ $agent->statut==='actif'?'badge-green':'badge-gray' }} mt-1">{{ ucfirst($agent->statut) }}</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-gray-400 text-xs">Matricule</p><p class="font-mono font-medium text-gray-700 mt-1">{{ $agent->matricule }}</p></div>
            <div><p class="text-gray-400 text-xs">Genre</p><p class="font-medium text-gray-700 mt-1">{{ $agent->genre === 'M' ? 'Masculin' : 'Féminin' }}</p></div>
            <div><p class="text-gray-400 text-xs">Email</p><p class="font-medium text-gray-700 mt-1">{{ $agent->email }}</p></div>
            <div><p class="text-gray-400 text-xs">Téléphone</p><p class="font-medium text-gray-700 mt-1">{{ $agent->telephone ?? '—' }}</p></div>
            <div><p class="text-gray-400 text-xs">Spécialité</p><p class="font-medium text-gray-700 mt-1">{{ $agent->specialite ?? '—' }}</p></div>
            <div><p class="text-gray-400 text-xs">Volume horaire</p><p class="font-medium text-gray-700 mt-1">{{ $agent->volume_horaire_hebdo }}h/semaine</p></div>
            <div><p class="text-gray-400 text-xs">Date d'intégration</p><p class="font-medium text-gray-700 mt-1">{{ $agent->date_integration?->format('d/m/Y') ?? '—' }}</p></div>
            <div><p class="text-gray-400 text-xs">Rôle</p><p class="font-medium text-violet-600 mt-1">{{ $agent->getRoleNames()->first() ?? '—' }}</p></div>
        </div>
        <div class="border-t border-violet-50 mt-5 pt-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-400 text-xs">DRENA</p><p class="font-medium text-gray-700 mt-1">{{ $agent->drena?->nom ?? '—' }}</p></div>
                <div><p class="text-gray-400 text-xs">IEPP</p><p class="font-medium text-gray-700 mt-1">{{ $agent->iepp?->nom ?? '—' }}</p></div>
                <div><p class="text-gray-400 text-xs">Établissement</p><p class="font-medium text-gray-700 mt-1">{{ $agent->etablissement?->nom ?? '—' }}</p></div>
                <div><p class="text-gray-400 text-xs">Circuit validation</p><p class="font-medium text-gray-700 mt-1">{{ $agent->etablissement?->circuit_validation ?? '—' }}</p></div>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Absences cette année</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ $statsAgent['total'] ?? 0 }}</p></div>
        <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Jours d'absence</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $statsAgent['jours'] ?? 0 }}j</p></div>
        <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Solde congés</p><p class="text-2xl font-bold text-emerald-500 mt-2">{{ $statsAgent['solde'] ?? 30 }}j</p></div>
        @hasanyrole('admin_drena|super_admin|gestionnaire_rh')
        <a href="{{ route('personnel.edit', $agent) }}" class="btn-primary w-full justify-center">Modifier</a>
        @endhasanyrole
    </div>
</div>
<div class="card-white p-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Historique des absences</h3>
    <div class="table-container"><table class="table-elegant"><thead><tr><th>Réf.</th><th>Type</th><th>Période</th><th>Jours</th><th>Circuit</th><th>Statut</th><th></th></tr></thead><tbody>
    @forelse($absences as $a)
    <tr><td class="font-medium text-gray-800">{{ $a->reference }}</td><td><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $a->typeAbsence->couleur }}"></span>{{ $a->typeAbsence->libelle }}</span></td><td class="text-xs text-gray-400">{{ $a->date_debut->format('d/m/Y') }} — {{ $a->date_fin->format('d/m/Y') }}</td><td>{{ $a->nombre_jours }}j</td><td><span class="badge {{ $a->circuit_validation==='primaire'?'badge-blue':'badge-amber' }} text-[10px]">{{ ucfirst($a->circuit_validation ?? '—') }}</span></td><td>@php $b=$a->statut_badge; @endphp<span class="badge badge-{{ $b['color'] }}">{{ $b['label'] }}</span></td><td><a href="{{ route('absences.show',$a) }}" class="text-sm text-violet-600 font-medium">Voir</a></td></tr>
    @empty<tr><td colspan="7" class="text-center text-gray-400 py-8">Aucune absence</td></tr>@endforelse
    </tbody></table></div>
</div>
</div>
@endsection
