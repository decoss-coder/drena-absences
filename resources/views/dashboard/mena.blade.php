@extends('layouts.app')
@section('title', 'Dashboard MENA')
@section('page-title', 'Dashboard National')
@section('page-subtitle', 'Vue consolidée de toutes les DRENA — Année scolaire en cours')

@section('content')
{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">DRENA actives</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_drena'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Agents</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_agents']) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Établissements</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_etablissements']) }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Absences en cours</p>
        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['absences_en_cours'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En attente</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['absences_en_attente'] }}</p>
    </div>
    <div class="stat-card">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Taux absentéisme</p>
        <p class="text-2xl font-bold {{ $stats['taux_absenteisme'] > 5 ? 'text-red-600' : 'text-emerald-600' }} mt-1">{{ $stats['taux_absenteisme'] }}%</p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    {{-- Évolution mensuelle --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Évolution mensuelle des absences</h3>
        <canvas id="chartEvolution" height="200"></canvas>
    </div>

    {{-- Répartition par type --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Répartition par type d'absence</h3>
        <canvas id="chartTypes" height="200"></canvas>
    </div>
</div>

{{-- Top DRENA --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-900">DRENA les plus touchées</h3>
        <a href="{{ route('rapports.index') }}" class="text-xs text-blue-600 hover:underline">Voir le rapport complet</a>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>DRENA</th>
                    <th>Région</th>
                    <th>Absences en cours</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absencesParDrena as $drena)
                <tr>
                    <td class="font-medium text-gray-900">{{ $drena->nom }}</td>
                    <td>{{ $drena->region }}</td>
                    <td>
                        <span class="badge {{ $drena->absences_en_cours_count > 15 ? 'badge-red' : ($drena->absences_en_cours_count > 5 ? 'badge-amber' : 'badge-green') }}">
                            {{ $drena->absences_en_cours_count }} absence(s)
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('rapports.index', ['drena_id' => $drena->id]) }}" class="text-sm text-blue-600 hover:underline">Détail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-gray-500 py-8">Aucune donnée disponible</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Alertes --}}
@if($alertes->count() > 0)
<div class="bg-red-50 rounded-xl border border-red-200 p-6">
    <h3 class="text-sm font-semibold text-red-800 mb-3">Alertes — DRENA en situation critique</h3>
    <div class="space-y-2">
        @foreach($alertes as $alerte)
        <div class="flex items-center justify-between px-4 py-2 bg-white rounded-lg border border-red-100">
            <span class="text-sm font-medium text-gray-900">{{ $alerte->nom }}</span>
            <span class="badge badge-red">{{ $alerte->absences_en_cours_count }} absences en cours</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Évolution mensuelle
    const evoData = @json($evolutionMensuelle);
    new Chart(document.getElementById('chartEvolution'), {
        type: 'line',
        data: {
            labels: evoData.map(d => d.mois),
            datasets: [{
                label: 'Absences',
                data: evoData.map(d => d.total),
                borderColor: '#2E86C1',
                backgroundColor: 'rgba(46,134,193,0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Répartition par type
    const typeData = @json($repartitionParType);
    new Chart(document.getElementById('chartTypes'), {
        type: 'doughnut',
        data: {
            labels: typeData.map(d => d.type_absence?.libelle || 'Autre'),
            datasets: [{
                data: typeData.map(d => d.total),
                backgroundColor: ['#E74C3C', '#F39C12', '#3498DB', '#2ECC71', '#9B59B6', '#1ABC9C', '#E67E22', '#95A5A6']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } } }
        }
    });
});
</script>
@endpush
