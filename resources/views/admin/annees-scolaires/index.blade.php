@extends('layouts.app')
@section('title', 'Années scolaires')
@section('page-title', 'Configuration — Années scolaires')
@section('content')
<div class="table-container">
    <table>
        <thead><tr><th>Libellé</th><th>Début</th><th>Fin</th><th>T1</th><th>T2</th><th>T3</th><th>En cours</th></tr></thead>
        <tbody>
            @foreach($annees as $a)
            <tr>
                <td class="font-medium">{{ $a->libelle }}</td>
                <td class="text-sm">{{ $a->date_debut->format('d/m/Y') }}</td>
                <td class="text-sm">{{ $a->date_fin->format('d/m/Y') }}</td>
                <td class="text-xs text-gray-500">{{ $a->trimestre1_debut?->format('d/m') }} — {{ $a->trimestre1_fin?->format('d/m') }}</td>
                <td class="text-xs text-gray-500">{{ $a->trimestre2_debut?->format('d/m') }} — {{ $a->trimestre2_fin?->format('d/m') }}</td>
                <td class="text-xs text-gray-500">{{ $a->trimestre3_debut?->format('d/m') }} — {{ $a->trimestre3_fin?->format('d/m') }}</td>
                <td>{!! $a->en_cours ? '<span class="badge badge-green">En cours</span>' : '<span class="badge badge-gray">Terminée</span>' !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
