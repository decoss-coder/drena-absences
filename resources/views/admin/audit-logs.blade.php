@extends('layouts.app')
@section('title', 'Audit & Logs')
@section('page-title', 'Audit & Logs')
@section('page-subtitle', 'Traçabilité des actions')
@section('content')
<div class="card-white p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]"><label class="text-[11px] font-semibold text-gray-400 uppercase">Recherche</label><input type="text" name="search" value="{{ request('search') }}" class="glass-input mt-1" placeholder="Action, utilisateur..."></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Du</label><input type="date" name="date_debut" value="{{ request('date_debut') }}" class="glass-input mt-1"></div>
        <div class="w-36"><label class="text-[11px] font-semibold text-gray-400 uppercase">Au</label><input type="date" name="date_fin" value="{{ request('date_fin') }}" class="glass-input mt-1"></div>
        <button type="submit" class="btn-primary">Filtrer</button>
    </form>
</div>
<div class="table-container"><table class="table-elegant"><thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Description</th><th>IP</th></tr></thead><tbody>
@forelse($logs as $l)
<tr><td class="text-xs text-gray-400 whitespace-nowrap">{{ $l->created_at->format('d/m/Y H:i') }}</td><td class="font-medium text-gray-700">{{ $l->causer?->nom_complet ?? 'Système' }}</td><td><span class="badge badge-blue">{{ $l->event }}</span></td><td class="text-sm text-gray-600">{{ $l->description }}</td><td class="font-mono text-xs text-gray-400">{{ $l->properties['ip'] ?? '—' }}</td></tr>
@empty<tr><td colspan="5" class="text-center text-gray-400 py-8">Aucun log</td></tr>@endforelse
</tbody></table></div>
<div class="mt-6">{{ $logs->links() }}</div>
@endsection
