@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', auth()->user()->unreadNotifications->count() . ' non lue(s)')

@section('content')
@if(auth()->user()->unreadNotifications->count() > 0)
<div class="mb-4">
    <form method="POST" action="{{ route('notifications.tout-lire') }}">@csrf
        <button type="submit" class="btn btn-secondary text-sm">Tout marquer comme lu</button>
    </form>
</div>
@endif

<div class="space-y-2">
    @forelse($notifications as $notif)
    <div class="flex items-start gap-3 p-4 rounded-xl border {{ $notif->read_at ? 'bg-white border-gray-200' : 'bg-blue-50 border-blue-200' }}">
        <div class="w-10 h-10 rounded-full {{ $notif->read_at ? 'bg-gray-100' : 'bg-blue-100' }} flex items-center justify-center shrink-0">
            @if(str_contains($notif->data['type'] ?? '', 'validee'))
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            @elseif(str_contains($notif->data['type'] ?? '', 'refusee'))
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            @else
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            @endif
        </div>
        <div class="flex-1">
            <p class="text-sm text-gray-900 {{ $notif->read_at ? '' : 'font-medium' }}">{{ $notif->data['message'] ?? 'Notification' }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if(isset($notif->data['absence_id']))
                <a href="{{ route('absences.show', $notif->data['absence_id']) }}" class="text-xs text-blue-600 hover:underline">Voir</a>
            @endif
            @if(!$notif->read_at)
                <form method="POST" action="{{ route('notifications.lire', $notif->id) }}">@csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">Marquer lu</button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-500">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <p>Aucune notification</p>
    </div>
    @endforelse
</div>
<div class="mt-6">{{ $notifications->links() }}</div>
@endsection
