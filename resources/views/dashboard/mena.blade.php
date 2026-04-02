@extends('layouts.app')
@section('title', 'Dashboard MENA')
@section('page-title', 'Dashboard National')
@section('page-subtitle', 'Vue consolidée des 41 DRENA — Année scolaire en cours')
@section('content')
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">DRENA actives</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['total_drena'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Agents</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Établissements</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($stats['total_etablissements']) }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Absences en cours</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">En attente</p><p class="text-2xl font-bold text-amber-500 mt-2">{{ $stats['absences_en_attente'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Taux absentéisme</p><p class="text-2xl font-bold {{ $stats['taux_absenteisme']>5?'text-red-500':'text-emerald-500' }} mt-2">{{ $stats['taux_absenteisme'] }}%</p></div>
</div>
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-4">Évolution mensuelle</h3><canvas id="chartEvolution" height="200"></canvas></div>
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-4">Répartition par type</h3><canvas id="chartTypes" height="200"></canvas></div>
</div>
<div class="card-white p-6 mb-8">
    <div class="flex items-center justify-between mb-4"><h3 class="text-sm font-semibold text-gray-700">DRENA les plus touchées</h3><a href="{{ route('rapports.index') }}" class="text-xs text-violet-600 hover:text-violet-800 font-medium">Rapport complet</a></div>
    <div class="table-container"><table class="table-elegant"><thead><tr><th>DRENA</th><th>Région</th><th>Absences en cours</th><th>Actions</th></tr></thead><tbody>
    @forelse($absencesParDrena as $drena)
    <tr><td class="font-medium text-gray-800">{{ $drena->nom }}</td><td>{{ $drena->region }}</td><td><span class="badge {{ $drena->absences_en_cours_count>15?'badge-red':($drena->absences_en_cours_count>5?'badge-amber':'badge-green') }}">{{ $drena->absences_en_cours_count }}</span></td><td><a href="{{ route('rapports.index',['drena_id'=>$drena->id]) }}" class="text-sm text-violet-600 hover:text-violet-800 font-medium">Détail</a></td></tr>
    @empty<tr><td colspan="4" class="text-center text-gray-400 py-8">Aucune donnée</td></tr>@endforelse
    </tbody></table></div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
Chart.defaults.color='#9ca3af';Chart.defaults.borderColor='#f0ecff';
document.addEventListener('DOMContentLoaded',function(){
    const evo=@json($evolutionMensuelle);
    new Chart(document.getElementById('chartEvolution'),{type:'line',data:{labels:evo.map(d=>d.mois),datasets:[{label:'Absences',data:evo.map(d=>d.total),borderColor:'#7c3aed',backgroundColor:'rgba(124,58,237,0.06)',fill:true,tension:.4,pointRadius:4,pointBackgroundColor:'#7c3aed',pointBorderColor:'#fff',pointBorderWidth:2}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#f5f3ff'}},x:{grid:{display:false}}}}});
    const types=@json($repartitionParType);
    new Chart(document.getElementById('chartTypes'),{type:'doughnut',data:{labels:types.map(d=>d.type_absence?.libelle||'Autre'),datasets:[{data:types.map(d=>d.total),backgroundColor:['#ef4444','#f59e0b','#7c3aed','#10b981','#a855f7','#06b6d4','#f97316','#6b7280'],borderWidth:3,borderColor:'#fff'}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:10,padding:14,font:{size:11}}}}}});
});
</script>
@endpush
