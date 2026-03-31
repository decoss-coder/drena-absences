@extends('layouts.app')
@section('title', 'Créer Admin — ' . $drena->nom)
@section('page-title', 'Créer un administrateur')
@section('page-subtitle', $drena->nom)
@section('content')
<form method="POST" action="{{ route('admin.drenas.store-admin', $drena) }}" class="max-w-2xl">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <div class="px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-800 mb-2">
            Cet utilisateur aura le rôle <strong>Admin DRENA</strong> et pourra gérer tout le périmètre de {{ $drena->nom }}.
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Matricule *</label><input type="text" name="matricule" class="input" required value="{{ old('matricule') }}"></div>
            <div><label class="label">Genre *</label><select name="genre" class="input" required><option value="M">Masculin</option><option value="F">Féminin</option></select></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Nom *</label><input type="text" name="nom" class="input" required value="{{ old('nom') }}"></div>
            <div><label class="label">Prénoms *</label><input type="text" name="prenoms" class="input" required value="{{ old('prenoms') }}"></div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Email *</label><input type="email" name="email" class="input" required value="{{ old('email') }}"></div>
            <div><label class="label">Téléphone</label><input type="text" name="telephone" class="input" value="{{ old('telephone') }}"></div>
        </div>
        <p class="text-xs text-gray-500">Mot de passe par défaut : Drena@Admin{{ date('Y') }}</p>
    </div>
    <div class="mt-4 flex gap-3">
        <button type="submit" class="btn btn-primary">Créer l'administrateur</button>
        <a href="{{ route('admin.drenas.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
@endsection
