@extends('layouts.app')
@section('title', 'Dashboard Gestionnaire RH')
@section('page-title', 'Gestion des Ressources Humaines')
@section('page-subtitle', auth()->user()->drena?->nom ?? '')

@section('content')
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Agents actifs</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_agents']) }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Absences en cours</p><p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['absences_en_cours'] }}</p></div>
    <div class="stat-card"><p class="text-xs font-medium text-gray-500 uppercase">Congés en cours</p><p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['conges_en_cours'] }}</p></div>
</div>
<div class="flex gap-3">
    <a href="{{ route('personnel.index') }}" class="btn btn-primary">Gérer le personnel</a>
    <a href="{{ route('absences.index') }}" class="btn btn-secondary">Voir les absences</a>
    <a href="{{ route('rapports.index') }}" class="btn btn-secondary">Rapports</a>
</div>
@endsection
