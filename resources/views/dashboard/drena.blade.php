@extends('layouts.app')
@section('title', 'Dashboard DRENA')
@section('page-title', 'Tableau de bord — ' . (auth()->user()->drena?->nom ?? ''))
@section('page-subtitle', 'Pilotage régional des absences')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Agents</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Établissements</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_etablissements'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Absences en cours</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">En attente</p><p class="text-2xl font-bold text-amber-500 mt-2">{{ $stats['absences_en_attente'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Taux</p><p class="text-2xl font-bold {{ $stats['taux_absenteisme']>5?'text-red-500':'text-emerald-500' }} mt-2">{{ $stats['taux_absenteisme'] }}%</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Heures perdues</p><p class="text-2xl font-bold text-orange-500 mt-2">{{ number_format($stats['heures_perdues'],0) }}h</p></div>
</div>
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-4">Absences par IEPP</h3><canvas id="chartIepp" height="220"></canvas></div>
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-4">Évolution mensuelle</h3><canvas id="chartEvo" height="220"></canvas></div>
</div>
<div class="card-white p-6 mb-8">
    <div class="flex items-center justify-between mb-4"><h3 class="text-sm font-semibold text-gray-700">Demandes en attente</h3><a href="{{ route('absences.index',['statut'=>'en_validation_n3']) }}" class="text-xs text-violet-600 font-medium">Voir tout</a></div>
    <div class="table-container"><table class="table-elegant"><thead><tr><th>Réf.</th><th>Agent</th><th>Établ.</th><th>Type</th><th>Période</th><th>Statut</th><th></th></tr></thead><tbody>
    @forelse($demandesEnAttente as $d)
    <tr><td class="font-medium text-gray-800">{{ $d->reference }}</td><td>{{ $d->user->nom_complet }}</td><td class="text-xs">{{ $d->etablissement->nom }}</td><td><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $d->typeAbsence->couleur }}"></span>{{ $d->typeAbsence->libelle }}</span></td><td class="text-xs">{{ $d->date_debut->format('d/m') }}—{{ $d->date_fin->format('d/m') }}</td><td>@php $b=$d->statut_badge; @endphp<span class="badge badge-{{ $b['color'] }}">{{ $b['label'] }}</span></td><td><a href="{{ route('absences.show',$d) }}" class="text-sm text-violet-600 font-medium">Voir</a></td></tr>
    @empty<tr><td colspan="7" class="text-center text-gray-400 py-8">Aucune demande</td></tr>@endforelse
    </tbody></table></div>
</div>
<div class="flex gap-3"><a href="{{ route('rapports.index') }}" class="btn-primary">Rapport détaillé</a><a href="{{ route('rapports.export-pdf') }}" class="btn-secondary">PDF</a><a href="{{ route('rapports.export-excel') }}" class="btn-secondary">Excel</a></div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
Chart.defaults.color='#9ca3af';Chart.defaults.borderColor='#f0ecff';
document.addEventListener('DOMContentLoaded',function(){
    const iD=@json($absencesParIepp);new Chart(document.getElementById('chartIepp'),{type:'bar',data:{labels:iD.map(d=>d.nom.length>15?d.nom.slice(0,15)+'…':d.nom),datasets:[{data:iD.map(d=>d.total),backgroundColor:'rgba(124,58,237,0.7)',borderRadius:8,barThickness:20}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#f5f3ff'}},x:{grid:{display:false}}}}});
    const eD=@json($evolutionMensuelle);new Chart(document.getElementById('chartEvo'),{type:'line',data:{labels:eD.map(d=>d.mois),datasets:[{label:'Absences',data:eD.map(d=>d.total),borderColor:'#7c3aed',backgroundColor:'rgba(124,58,237,0.06)',fill:true,tension:.4,pointRadius:4,pointBackgroundColor:'#7c3aed',pointBorderColor:'#fff',pointBorderWidth:2},{label:'Jours',data:eD.map(d=>d.total_jours),borderColor:'#ef4444',backgroundColor:'transparent',tension:.4,borderDash:[5,5]}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:10,font:{size:11}}}},scales:{y:{beginAtZero:true,grid:{color:'#f5f3ff'}},x:{grid:{display:false}}}}});
});
</script>
@endpush
