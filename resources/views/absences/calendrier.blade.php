@extends('layouts.app')
@section('title', 'Calendrier des absences')
@section('page-title', 'Calendrier des absences')
@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('absences.calendrier', ['mois' => $mois == 1 ? 12 : $mois - 1, 'annee' => $mois == 1 ? $annee - 1 : $annee]) }}" class="btn btn-secondary text-sm">&larr; Précédent</a>
        <h3 class="text-lg font-semibold">{{ \Carbon\Carbon::create($annee, $mois)->translatedFormat('F Y') }}</h3>
        <a href="{{ route('absences.calendrier', ['mois' => $mois == 12 ? 1 : $mois + 1, 'annee' => $mois == 12 ? $annee + 1 : $annee]) }}" class="btn btn-secondary text-sm">Suivant &rarr;</a>
    </div>
    @if($absences->count() > 0)
    <div class="table-container"><table><thead><tr><th>Agent</th><th>Type</th><th>Du</th><th>Au</th><th>Jours</th></tr></thead><tbody>
    @foreach($absences as $a)
    <tr><td class="font-medium">{{ $a->user->nom_complet }}</td><td><span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full" style="background:{{ $a->typeAbsence->couleur }}"></span>{{ $a->typeAbsence->libelle }}</span></td><td class="text-xs">{{ $a->date_debut->format('d/m') }}</td><td class="text-xs">{{ $a->date_fin->format('d/m') }}</td><td>{{ $a->nombre_jours }}j</td></tr>
    @endforeach</tbody></table></div>
    @else<p class="text-center text-gray-500 py-12">Aucune absence ce mois-ci</p>@endif
</div>
@endsection
