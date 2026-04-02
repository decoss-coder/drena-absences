@extends('layouts.app')
@section('title', 'Rapports & Statistiques')
@section('page-title', 'Rapports & Statistiques')
@section('page-subtitle', 'Analyse des absences — ' . ($periode ?? 'Ce mois'))
@section('content')
<div class="card-white p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        @hasrole('super_admin')<div class="w-48"><label class="text-[11px] font-semibold text-gray-400 uppercase">DRENA</label><select name="drena_id" class="glass-input mt-1"><option value="">Toutes</option>@foreach($drenas as $d)<option value="{{ $d->id }}" {{ request('drena_id')==$d->id?'selected':'' }}>{{ $d->nom }}</option>@endforeach</select></div>@endhasrole
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Période</label><select name="periode" class="glass-input mt-1"><option value="mois" {{ request('periode')==='mois'?'selected':'' }}>Ce mois</option><option value="trimestre" {{ request('periode')==='trimestre'?'selected':'' }}>Trimestre</option><option value="annee" {{ request('periode')==='annee'?'selected':'' }}>Année</option></select></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Du</label><input type="date" name="date_debut" value="{{ request('date_debut', $dateDebut) }}" class="glass-input mt-1"></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Au</label><input type="date" name="date_fin" value="{{ request('date_fin', $dateFin) }}" class="glass-input mt-1"></div>
        <button type="submit" class="btn-primary">Générer</button>
    </form>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Agents actifs</p><p class="text-2xl font-bold text-gray-800 mt-2">{{ number_format($tauxAbsenteisme['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Agents absents</p><p class="text-2xl font-bold text-red-500 mt-2">{{ $tauxAbsenteisme['agents_absents'] }}</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Taux absentéisme</p><p class="text-2xl font-bold {{ $tauxAbsenteisme['taux']>5?'text-red-500':'text-emerald-500' }} mt-2">{{ $tauxAbsenteisme['taux'] }}%</p></div>
    <div class="stat-card"><p class="text-[11px] font-semibold text-gray-400 uppercase">Jours perdus</p><p class="text-2xl font-bold text-amber-500 mt-2">{{ number_format($tauxAbsenteisme['total_jours']) }}</p></div>
</div>
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-4">Évolution mensuelle</h3><canvas id="chartEvo" height="220"></canvas></div>
    <div class="card-white p-6"><h3 class="text-sm font-semibold text-gray-700 mb-4">Par type d'absence</h3><canvas id="chartTypes" height="220"></canvas></div>
</div>
@if(isset($comparatifDrena) && $comparatifDrena->count() > 0)
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Comparatif entre DRENA</h3>
    <div class="table-container"><table class="table-elegant"><thead><tr><th>DRENA</th><th>Agents</th><th>Absences</th><th>Taux</th></tr></thead><tbody>
    @foreach($comparatifDrena as $d)<tr><td class="font-medium text-gray-800">{{ $d->nom }}</td><td>{{ $d->total_agents }}</td><td><span class="badge badge-red">{{ $d->total_absences }}</span></td><td>{{ $d->total_agents > 0 ? round(($d->total_absences / $d->total_agents) * 100, 1) : 0 }}%</td></tr>@endforeach
    </tbody></table></div>
</div>
@endif
<div class="flex gap-3"><a href="{{ route('rapports.export-pdf', request()->query()) }}" class="btn-primary">Export PDF</a><a href="{{ route('rapports.export-excel', request()->query()) }}" class="btn-secondary">Export Excel</a><a href="{{ route('rapports.export-csv', request()->query()) }}" class="btn-secondary">Export CSV</a></div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
Chart.defaults.color='#9ca3af';Chart.defaults.borderColor='#f0ecff';
document.addEventListener('DOMContentLoaded',function(){
    const eD=@json($evolutionMensuelle);new Chart(document.getElementById('chartEvo'),{type:'line',data:{labels:eD.map(d=>d.mois),datasets:[{label:'Absences',data:eD.map(d=>d.total),borderColor:'#7c3aed',backgroundColor:'rgba(124,58,237,0.06)',fill:true,tension:.4,pointRadius:4,pointBackgroundColor:'#7c3aed',pointBorderColor:'#fff',pointBorderWidth:2}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#f5f3ff'}},x:{grid:{display:false}}}}});
    const tD=@json($repartitionParType);new Chart(document.getElementById('chartTypes'),{type:'doughnut',data:{labels:tD.map(d=>d.type_absence?.libelle||'Autre'),datasets:[{data:tD.map(d=>d.total),backgroundColor:['#ef4444','#f59e0b','#7c3aed','#10b981','#a855f7','#06b6d4','#f97316','#6b7280'],borderWidth:3,borderColor:'#fff'}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{boxWidth:10,padding:14,font:{size:11}}}}}});
});
</script>
@endpush
