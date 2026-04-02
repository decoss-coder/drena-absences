@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('page-subtitle', $notifications->total() . ' notification(s)')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex gap-2">
        <a href="{{ route('notifications.index') }}" class="{{ !request('type')?'btn-primary':'btn-secondary' }} text-sm">Toutes</a>
        <a href="{{ route('notifications.index', ['type'=>'unread']) }}" class="{{ request('type')==='unread'?'btn-primary':'btn-secondary' }} text-sm">Non lues</a>
    </div>
    @if(auth()->user()->unreadNotifications->count() > 0)
    <form method="POST" action="{{ route('notifications.mark-all-read') }}">@csrf<button type="submit" class="btn-secondary text-sm">Tout marquer lu</button></form>
    @endif
</div>
<div class="space-y-2">
@forelse($notifications as $n)
<div class="card-white p-4 flex items-start gap-4 {{ $n->read_at ? '' : 'border-l-4 border-l-violet-400' }}">
    <div class="w-10 h-10 rounded-xl {{ $n->read_at ? 'bg-gray-100' : 'bg-violet-100' }} flex items-center justify-center shrink-0">
        @if(str_contains($n->type, 'Soumise'))<svg class="w-5 h-5 {{ $n->read_at?'text-gray-400':'text-violet-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
        @elseif(str_contains($n->type, 'Validee'))<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @elseif(str_contains($n->type, 'Refusee'))<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @else<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>@endif
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm {{ $n->read_at ? 'text-gray-500' : 'text-gray-800 font-medium' }}">{{ $n->data['message'] ?? 'Notification' }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
    </div>
    @if(!$n->read_at)
    <form method="POST" action="{{ route('notifications.mark-read', $n->id) }}">@csrf<button type="submit" class="text-xs text-violet-500 hover:text-violet-700 font-medium whitespace-nowrap">Marquer lu</button></form>
    @endif
</div>
@empty
<div class="card-white p-16 text-center"><div class="w-14 h-14 rounded-2xl bg-violet-50 flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg></div><p class="text-gray-500 font-medium">Aucune notification</p></div>
@endforelse
</div>
<div class="mt-6">{{ $notifications->links() }}</div>
@endsection
