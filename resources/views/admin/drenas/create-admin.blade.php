@extends('layouts.app')
@section('title', 'Créer Admin — ' . $drena->nom)
@section('page-title', 'Créer un Admin DRENA')
@section('page-subtitle', $drena->nom)
@section('content')
<form method="POST" action="{{ route('admin.drenas.store-admin', $drena) }}" class="max-w-2xl">@csrf
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Nouvel administrateur pour {{ $drena->nom }}</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Nom <span class="text-red-400">*</span></label><input type="text" name="nom" value="{{ old('nom') }}" class="glass-input" required></div><div><label class="label">Prénoms <span class="text-red-400">*</span></label><input type="text" name="prenoms" value="{{ old('prenoms') }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Email <span class="text-red-400">*</span></label><input type="email" name="email" value="{{ old('email') }}" class="glass-input" required></div><div><label class="label">Matricule <span class="text-red-400">*</span></label><input type="text" name="matricule" value="{{ old('matricule') }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Mot de passe <span class="text-red-400">*</span></label><input type="password" name="password" class="glass-input" required minlength="8"></div><div><label class="label">Confirmation</label><input type="password" name="password_confirmation" class="glass-input" required></div></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn-primary">Créer l'admin</button><a href="{{ route('admin.drenas.index') }}" class="btn-secondary">Annuler</a></div>
</form>
@endsection
