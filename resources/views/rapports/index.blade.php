@extends('layouts.app')
@section('title', 'Rapports & Statistiques')
@section('page-title', 'Rapports & Statistiques')
@section('page-subtitle', 'Analyse des absences — période sélectionnée')
@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        @if(auth()->user()->hasRole('super_admin'))
        <div class="w-48"><label class="text-xs font-medium text-gray-500">DRENA</label><select name="drena_id" class="input mt-1"><option value="">Toutes les DRENA</option>@foreach($drenas as $d)<option value="{{ $d->id }}" {{ $drenaId==$d->id?'selected':'' }}>{{ $d->nom }}</option>@endforeach</select></div>
        @endif
        <div class="w-36"><label class="text-xs font-medium text-gray-500">Période</label><select name="periode" class="input mt-1"><option value="semaine" {{ $periode==='semaine'?'selected':'' }}>Semaine</option><option value="mois" {{ $periode==='mois'?'selected':'' }}>Mois</option><option value="trimestre" {{ $periode==='trimestre'?'selected':'' }}>Trimestre</option><option value="annee" {{ $periode==='annee'?'selected':'' }}>Année</option></select></div>
        <div class="w-36"><label class="text-xs font-medium text-gray-500">Du</label><input type="date" name="date_debut" value="{{ $dateDebut }}" class="input mt-1"></div>
        <div class="w-36"><label class="text-xs font-medium text-gray-500">Au</label><input type="date" name="date_fin" value="{{ $dateFin }}" class="input mt-1"></div>
        <button type="submit" class="btn btn-primary">Actualiser</button>
        <a href="{{ route('rapports.export-pdf', request()->query()) }}" class="btn btn-secondary">Export PDF</a>
        <a href="{{ route('rapports.export-excel', request()->query()) }}" class="btn btn-secondary">Export Excel</a>
    </form>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Total agents</p><p class="text-2xl font-bold mt-1">{{ number_format($data['tauxAbsenteisme']['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Agents absents</p><p class="text-2xl font-bold text-red-600 mt-1">{{ $data['tauxAbsenteisme']['agents_absents'] }}</p></div>
    <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Taux absentéisme</p><p class="text-2xl font-bold {{ $data['tauxAbsenteisme']['taux'] > 5 ? 'text-red-600' : 'text-emerald-600' }} mt-1">{{ $data['tauxAbsenteisme']['taux'] }}%</p></div>
    <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Heures perdues</p><p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($data['heuresCoursPerdu']) }}h</p></div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl border border-gray-200 p-6"><h3 class="text-sm font-semibold mb-4">Évolution mensuelle (12 mois)</h3><canvas id="evoChart" height="220"></canvas></div>
    <div class="bg-white rounded-xl border border-gray-200 p-6"><h3 class="text-sm font-semibold mb-4">Répartition par type</h3><canvas id="typeChart" height="220"></canvas></div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-8">
    @if($data['topEtablissements'] && $data['topEtablissements']->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold mb-4">Top 10 — Établissements les plus touchés</h3>
        <div class="table-container"><table><thead><tr><th>Établissement</th><th>Absences</th><th>Jours</th></tr></thead><tbody>
            @foreach($data['topEtablissements'] as $e)
            <tr><td class="font-medium text-sm">{{ $e->nom }}</td><td><span class="badge badge-red">{{ $e->total_absences }}</span></td><td class="text-sm">{{ $e->total_jours }}j</td></tr>
            @endforeach
        </tbody></table></div>
    </div>
    @endif

    @if($data['topAgents'] && $data['topAgents']->count() > 0)
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold mb-4">Top 10 — Agents les plus absents</h3>
        <div class="table-container"><table><thead><tr><th>Agent</th><th>Matricule</th><th>Jours</th></tr></thead><tbody>
            @foreach($data['topAgents'] as $a)
            <tr><td class="font-medium text-sm">{{ $a->nom_complet }}</td><td class="font-mono text-xs">{{ $a->matricule }}</td><td><span class="badge {{ $a->total_jours > 20 ? 'badge-red' : 'badge-amber' }}">{{ $a->total_jours }}j</span></td></tr>
            @endforeach
        </tbody></table></div>
    </div>
    @endif
</div>

@if($data['comparatif'] && $data['comparatif']->count() > 0)
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-sm font-semibold mb-4">Comparatif inter-DRENA</h3>
    <canvas id="comparatifChart" height="200"></canvas>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const evo = @json($data['evolutionMensuelle']);
    new Chart(document.getElementById('evoChart'), {
        type: 'line',
        data: { labels: evo.map(d => d.mois), datasets: [
            { label: 'Nombre', data: evo.map(d => d.total), borderColor: '#2E86C1', backgroundColor: 'rgba(46,134,193,0.1)', fill: true, tension: 0.4, pointRadius: 3 },
            { label: 'Jours total', data: evo.map(d => d.total_jours), borderColor: '#E74C3C', backgroundColor: 'transparent', tension: 0.4, borderDash: [5,5], pointRadius: 3 }
        ]},
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }, scales: { y: { beginAtZero: true } } }
    });

    const types = @json($data['repartitionParType']);
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: { labels: types.map(d => d.type_absence?.libelle || 'Autre'), datasets: [{ data: types.map(d => d.total), backgroundColor: ['#E74C3C','#3498DB','#2ECC71','#F39C12','#9B59B6','#1ABC9C','#E67E22','#95A5A6','#C0392B','#27AE60'] }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } } } }
    });

    @if($data['comparatif'] && $data['comparatif']->count() > 0)
    const comp = @json($data['comparatif']);
    new Chart(document.getElementById('comparatifChart'), {
        type: 'bar',
        data: { labels: comp.map(d => d.nom.length > 18 ? d.nom.slice(0,18)+'…' : d.nom), datasets: [{ label: 'Absences', data: comp.map(d => d.total_absences), backgroundColor: '#2E86C1', borderRadius: 6 }] },
        options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } } }
    });
    @endif
});
</script>
@endpush
