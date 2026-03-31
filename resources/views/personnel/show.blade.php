@extends('layouts.app')
@section('title', $personnel->nom_complet)
@section('page-title', $personnel->nom_complet)
@section('page-subtitle', $personnel->matricule)
@section('content')
<div class="max-w-4xl grid lg:grid-cols-3 gap-6">
<div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
    <div class="flex items-center gap-4 mb-6"><div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center text-xl font-bold text-primary-700">{{ $personnel->initiales }}</div><div><p class="text-lg font-semibold">{{ $personnel->nom_complet }}</p><p class="text-sm text-gray-500">{{ $personnel->grade ?? '' }}</p><p class="text-xs text-gray-500">{{ $personnel->email }}</p></div></div>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div><p class="text-gray-500 text-xs">Matricule</p><p class="font-medium font-mono mt-0.5">{{ $personnel->matricule }}</p></div>
        <div><p class="text-gray-500 text-xs">Téléphone</p><p class="font-medium mt-0.5">{{ $personnel->telephone ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Spécialité</p><p class="font-medium mt-0.5">{{ $personnel->specialite ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Volume horaire</p><p class="font-medium mt-0.5">{{ $personnel->volume_horaire_hebdo ?? 0 }}h/sem.</p></div>
        <div><p class="text-gray-500 text-xs">DRENA</p><p class="font-medium mt-0.5">{{ $personnel->drena?->nom ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Établissement</p><p class="font-medium mt-0.5">{{ $personnel->etablissement?->nom ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Ancienneté</p><p class="font-medium mt-0.5">{{ $personnel->anciennete ?? '—' }}</p></div>
        <div><p class="text-gray-500 text-xs">Statut</p><span class="badge {{ $personnel->statut==='actif'?'badge-green':'badge-gray' }}">{{ ucfirst($personnel->statut) }}</span></div>
    </div>
</div>
<div class="space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 p-5"><p class="text-xs text-gray-500 uppercase">Absences (année)</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $statsAbsences['total_annee'] ?? 0 }}j</p></div>
    <div class="bg-white rounded-xl border border-gray-200 p-5"><p class="text-xs text-gray-500 uppercase">Solde congés</p><p class="text-2xl font-bold text-emerald-600 mt-1">{{ $statsAbsences['solde_conge'] ?? 30 }}j</p></div>
</div></div>
@endsection
