@extends('layouts.app')
@section('title', 'Calendrier des absences')
@section('page-title', 'Calendrier des absences')
@section('page-subtitle', \Carbon\Carbon::create($annee, $mois)->translatedFormat('F Y'))

@push('styles')
<style>
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:6px;min-width:420px}
.cal-header-cell{text-align:center;font-size:11px;font-weight:700;color:#8b7fad;padding:6px 0;text-transform:uppercase;letter-spacing:.04em}
.cal-header-cell.weekend{color:#e2d9f3}
.cal-day{min-height:72px;border-radius:12px;padding:8px;border:1px solid #f0ecff;background:#fff;transition:all .2s}
.cal-day:hover{border-color:#ddd6fe;background:#faf9ff}
.cal-day.today{border-color:#7c3aed;background:#faf5ff}
.cal-day.other-month{opacity:.45;pointer-events:none}
.cal-day.weekend{background:#fafafa}
.cal-day-num{font-size:12px;font-weight:600;color:#6b7280;margin-bottom:4px}
.today .cal-day-num{width:22px;height:22px;background:#ede9fe;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#7c3aed;font-weight:700}
.absence-chip{font-size:10px;padding:2px 7px;border-radius:6px;margin-bottom:2px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;display:block}
.avatar-circle{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0}
</style>
@endpush

@section('content')
<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <a href="{{ route('absences.calendrier',['mois'=>$mois==1?12:$mois-1,'annee'=>$mois==1?$annee-1:$annee]) }}" class="btn-secondary"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Précédent</a>
    <div class="text-center"><h3 class="text-xl font-bold text-gray-800 tracking-tight">{{ \Carbon\Carbon::create($annee,$mois)->translatedFormat('F Y') }}</h3><p class="text-xs text-gray-400 mt-0.5">{{ auth()->user()->drena?->nom ?? 'MENA National' }}</p></div>
    <a href="{{ route('absences.calendrier',['mois'=>$mois==12?1:$mois+1,'annee'=>$mois==12?$annee+1:$annee]) }}" class="btn-secondary">Suivant<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
</div>

@php
    $totalJours=$absences->sum('nombre_jours');
    $nonJustif=$absences->whereIn('statut',[\App\Models\Absence::STATUT_EN_VALIDATION_CHEF,\App\Models\Absence::STATUT_EN_VALIDATION_INSPECTEUR,\App\Models\Absence::STATUT_EN_VALIDATION_DRENA])->count();
    $agentsCount=$absences->pluck('user_id')->unique()->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="stat-card"><div class="flex items-center gap-2.5 mb-2.5"><div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center"><svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><span class="text-xs text-gray-400 font-medium">Total absences</span></div><p class="text-3xl font-bold text-gray-800">{{ $absences->count() }}</p><p class="text-xs text-violet-400 mt-0.5">ce mois-ci</p></div>
    <div class="stat-card"><div class="flex items-center gap-2.5 mb-2.5"><div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center"><svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><span class="text-xs text-gray-400 font-medium">Jours cumulés</span></div><p class="text-3xl font-bold text-gray-800">{{ $totalJours }}</p><p class="text-xs text-amber-400 mt-0.5">jours ouvrables</p></div>
    <div class="stat-card"><div class="flex items-center gap-2.5 mb-2.5"><div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center"><svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div><span class="text-xs text-gray-400 font-medium">En attente</span></div><p class="text-3xl font-bold text-gray-800">{{ $nonJustif }}</p><p class="text-xs text-red-400 mt-0.5">à traiter</p></div>
    <div class="stat-card"><div class="flex items-center gap-2.5 mb-2.5"><div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center"><svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><span class="text-xs text-gray-400 font-medium">Agents concernés</span></div><p class="text-3xl font-bold text-gray-800">{{ $agentsCount }}</p><p class="text-xs text-emerald-400 mt-0.5">agents distincts</p></div>
</div>

<div class="card-white p-5 mb-6">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <h4 class="text-sm font-semibold text-gray-700">Vue mensuelle</h4>
        <div class="flex flex-wrap gap-3">@foreach($typesAbsence as $type)<span class="flex items-center gap-1.5 text-xs text-gray-500"><span class="w-2 h-2 rounded-sm inline-block border" style="background:{{ $type->couleur }}20;border-color:{{ $type->couleur }}60"></span>{{ $type->libelle }}</span>@endforeach</div>
    </div>
    @php
        $premierJour=\Carbon\Carbon::create($annee,$mois,1);$dernierJour=$premierJour->copy()->endOfMonth();
        $debutGrille=$premierJour->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
        $finGrille=$dernierJour->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
        $absencesParJour=collect();
        foreach($absences as $absence){$current=$absence->date_debut->copy();while($current<=$absence->date_fin){$key=$current->format('Y-m-d');if(!isset($absencesParJour[$key]))$absencesParJour[$key]=collect();$absencesParJour[$key]->push($absence);$current->addDay();}}
    @endphp
    <div class="overflow-x-auto"><div class="cal-grid">
        @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $i=>$jour)<div class="cal-header-cell {{ $i>=5?'weekend':'' }}">{{ $jour }}</div>@endforeach
        @for($day=$debutGrille->copy();$day<=$finGrille;$day->addDay())
        @php $key=$day->format('Y-m-d');$isWeekend=$day->isWeekend();$isToday=$day->isToday();$isOther=$day->month!==$mois;$dayAbsences=$absencesParJour[$key]??collect(); @endphp
        <div class="cal-day {{ $isToday?'today':'' }} {{ $isOther?'other-month':'' }} {{ $isWeekend?'weekend':'' }}">
            <div class="cal-day-num">{{ $day->day }}</div>
            @foreach($dayAbsences->take(2) as $abs)<span class="absence-chip" style="background:{{ $abs->typeAbsence->couleur }}15;color:{{ $abs->typeAbsence->couleur }}">{{ \Illuminate\Support\Str::limit($abs->user->nom_complet,12) }}</span>@endforeach
            @if($dayAbsences->count()>2)<span class="absence-chip" style="background:#f3f4f6;color:#6b7280">+{{ $dayAbsences->count()-2 }} autres</span>@endif
        </div>
        @endfor
    </div></div>
</div>

@if($absences->count()>0)
<div class="card-white overflow-hidden">
    <div class="flex gap-2 p-4">
        <a href="{{ route('rapports.export-excel',['mois'=>$mois,'annee'=>$annee]) }}" class="btn-secondary text-xs py-2 px-4">Excel</a>
        <a href="{{ route('rapports.export-pdf',['mois'=>$mois,'annee'=>$annee]) }}" class="btn-primary text-xs py-2 px-4">PDF</a>
    </div>
    <div class="table-container" style="border-radius:0;border:none"><table class="table-elegant"><thead><tr><th>Agent</th><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Circuit</th><th>Statut</th><th></th></tr></thead><tbody>
    @foreach($absences->sortBy('date_debut') as $absence)
    <tr><td><div class="flex items-center gap-2.5"><div class="avatar-circle" style="background:linear-gradient(135deg,{{ $absence->typeAbsence->couleur }},{{ $absence->typeAbsence->couleur }}aa)">{{ $absence->user->initiales }}</div><div><p class="font-semibold text-gray-800 text-sm">{{ $absence->user->nom_complet }}</p><p class="text-xs text-gray-400">{{ $absence->user->getRoleNames()->first() }}</p></div></div></td><td><span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:{{ $absence->typeAbsence->couleur }}"></span>{{ $absence->typeAbsence->libelle }}</span></td><td class="text-xs text-gray-500">{{ $absence->date_debut->format('d/m/Y') }}</td><td class="text-xs text-gray-500">{{ $absence->date_fin->format('d/m/Y') }}</td><td><span class="badge badge-blue">{{ $absence->nombre_jours }}j</span></td><td><span class="badge {{ $absence->circuit_validation==='primaire'?'badge-blue':'badge-amber' }} text-[10px]">{{ ucfirst($absence->circuit_validation ?? '—') }}</span></td><td>@php $b=$absence->statut_badge; @endphp<span class="badge badge-{{ $b['color'] }}">{{ $b['label'] }}</span></td><td><a href="{{ route('absences.show',$absence) }}" class="text-xs text-violet-500 hover:text-violet-700 font-medium">Détails</a></td></tr>
    @endforeach</tbody></table></div>
</div>
@else
<div class="card-white p-16 text-center"><div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><p class="text-gray-500 font-medium">Aucune absence ce mois-ci</p><a href="{{ route('absences.create') }}" class="btn-primary mt-4 mx-auto w-fit">Déclarer une absence</a></div>
@endif
@endsection
