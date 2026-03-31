@extends('layouts.app')
@section('title', 'Audit & Logs')
@section('page-title', 'Journal d\'audit')
@section('page-subtitle', 'Traçabilité complète — Les logs ne peuvent être modifiés ni supprimés')
@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="w-48"><label class="text-xs font-medium text-gray-500">Utilisateur ID</label><input type="text" name="causer_id" value="{{ request('causer_id') }}" class="input mt-1" placeholder="ID"></div>
        <div class="w-40"><label class="text-xs font-medium text-gray-500">Modèle</label><input type="text" name="subject_type" value="{{ request('subject_type') }}" class="input mt-1" placeholder="Absence, User..."></div>
        <div class="w-40"><label class="text-xs font-medium text-gray-500">Date</label><input type="date" name="date" value="{{ request('date') }}" class="input mt-1"></div>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <a href="{{ route('admin.audit') }}" class="btn btn-secondary">Réinitialiser</a>
    </form>
</div>
<div class="table-container">
    <table>
        <thead><tr><th>Date/Heure</th><th>Utilisateur</th><th>Action</th><th>Modèle</th><th>Description</th><th>Changements</th></tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="text-xs whitespace-nowrap text-gray-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                <td class="text-sm">
                    @if($log->causer)
                        <span class="font-medium">{{ $log->causer->nom_complet }}</span>
                        <span class="text-xs text-gray-500 block">{{ $log->causer->matricule }}</span>
                    @else
                        <span class="text-gray-400">Système</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $log->event === 'created' ? 'badge-green' : ($log->event === 'deleted' ? 'badge-red' : 'badge-blue') }}">
                        {{ $log->event }}
                    </span>
                </td>
                <td class="text-xs font-mono text-gray-600">{{ class_basename($log->subject_type ?? 'N/A') }} #{{ $log->subject_id }}</td>
                <td class="text-xs text-gray-700 max-w-xs">{{ $log->description }}</td>
                <td class="text-xs text-gray-500 max-w-xs">
                    @if($log->properties && $log->properties->count() > 0)
                        @if($log->properties->has('old'))
                            <span class="text-red-500">{{ json_encode($log->properties['old'], JSON_UNESCAPED_UNICODE) }}</span>
                            <br>→
                            <span class="text-emerald-600">{{ json_encode($log->properties['attributes'] ?? [], JSON_UNESCAPED_UNICODE) }}</span>
                        @endif
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-gray-500 py-8">Aucun log d'audit</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $logs->links() }}</div>
@endsection
