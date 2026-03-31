@extends('layouts.app')
@section('title', 'Dashboard DRENA')
@section('page-title', 'Tableau de bord — ' . (auth()->user()->drena?->nom ?? ''))
@section('page-subtitle', 'Pilotage régional des absences')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Agents</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Établissements</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_etablissements'] }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Absences en cours</p><p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">En attente</p><p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['absences_en_attente'] }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Taux absentéisme</p><p class="text-2xl font-bold {{ $stats['taux_absenteisme'] > 5 ? 'text-red-600' : 'text-emerald-600' }} mt-1">{{ $stats['taux_absenteisme'] }}%</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Heures perdues</p><p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($stats['heures_perdues'], 0) }}h</p></div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Absences par IEPP</h3>
        <canvas id="chartIepp" height="220"></canvas>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Évolution mensuelle</h3>
        <canvas id="chartEvo" height="220"></canvas>
    </div>
</div>

{{-- Dernières demandes en attente --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-900">Dernières demandes en attente</h3>
        <a href="{{ route('absences.index', ['statut' => 'en_validation_n3']) }}" class="text-xs text-blue-600 hover:underline">Voir tout</a>
    </div>
    <div class="table-container">
        <table>
            <thead><tr><th>Réf.</th><th>Agent</th><th>Établissement</th><th>Type</th><th>Période</th><th>Statut</th><th></th></tr></thead>
            <tbody>
                @forelse($demandesEnAttente as $d)
                <tr>
                    <td class="font-medium text-gray-900">{{ $d->reference }}</td>
                    <td>{{ $d->user->nom_complet }}</td>
                    <td class="text-xs">{{ $d->etablissement->nom }}</td>
                    <td><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $d->typeAbsence->couleur }}"></span>{{ $d->typeAbsence->libelle }}</span></td>
                    <td class="text-xs">{{ $d->date_debut->format('d/m') }} — {{ $d->date_fin->format('d/m') }}</td>
                    <td>@php $b = $d->statut_badge; @endphp <span class="badge badge-{{ $b['color'] }}">{{ $b['label'] }}</span></td>
                    <td><a href="{{ route('absences.show', $d) }}" class="text-sm text-blue-600 hover:underline">Voir</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-gray-500 py-8">Aucune demande en attente</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Top Absents --}}
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Top 10 — Agents les plus absents (année en cours)</h3>
    <div class="table-container">
        <table>
            <thead><tr><th>#</th><th>Agent</th><th>Matricule</th><th>Spécialité</th><th>Jours d'absence</th></tr></thead>
            <tbody>
                @foreach($topAbsents as $i => $agent)
                <tr>
                    <td class="font-bold text-gray-400">{{ $i + 1 }}</td>
                    <td class="font-medium text-gray-900">{{ $agent->nom_complet }}</td>
                    <td class="text-xs text-gray-500">{{ $agent->matricule }}</td>
                    <td>{{ $agent->specialite ?? '—' }}</td>
                    <td><span class="badge {{ $agent->total_jours > 20 ? 'badge-red' : 'badge-amber' }}">{{ $agent->total_jours }} jours</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="flex gap-3 mt-6">
    <a href="{{ route('rapports.index') }}" class="btn btn-primary">Rapport détaillé</a>
    <a href="{{ route('rapports.export-pdf') }}" class="btn btn-secondary">Exporter PDF</a>
    <a href="{{ route('rapports.export-excel') }}" class="btn btn-secondary">Exporter Excel</a>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ieppData = @json($absencesParIepp);
    new Chart(document.getElementById('chartIepp'), {
        type: 'bar',
        data: {
            labels: ieppData.map(d => d.nom.length > 15 ? d.nom.slice(0,15)+'…' : d.nom),
            datasets: [{ label: 'Absences', data: ieppData.map(d => d.total), backgroundColor: '#2E86C1', borderRadius: 6, barThickness: 24 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    const evoData = @json($evolutionMensuelle);
    new Chart(document.getElementById('chartEvo'), {
        type: 'line',
        data: {
            labels: evoData.map(d => d.mois),
            datasets: [
                { label: 'Absences', data: evoData.map(d => d.total), borderColor: '#2E86C1', backgroundColor: 'rgba(46,134,193,0.1)', fill: true, tension: 0.4 },
                { label: 'Jours', data: evoData.map(d => d.total_jours), borderColor: '#E74C3C', backgroundColor: 'transparent', tension: 0.4, borderDash: [5,5] }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush
