@extends('layouts.app')
@section('title', 'Ajouter un agent')
@section('page-title', 'Ajouter un agent')
@section('page-subtitle', 'Créer un nouveau compte personnel')
@section('content')
<form method="POST" action="{{ route('personnel.store') }}" class="max-w-3xl">@csrf
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Identité</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Nom <span class="text-red-400">*</span></label><input type="text" name="nom" value="{{ old('nom') }}" class="glass-input" required></div><div><label class="label">Prénoms <span class="text-red-400">*</span></label><input type="text" name="prenoms" value="{{ old('prenoms') }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Genre <span class="text-red-400">*</span></label><select name="genre" class="glass-input" required><option value="M" {{ old('genre')==='M'?'selected':'' }}>Masculin</option><option value="F" {{ old('genre')==='F'?'selected':'' }}>Féminin</option></select></div><div><label class="label">Matricule <span class="text-red-400">*</span></label><input type="text" name="matricule" value="{{ old('matricule') }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Email <span class="text-red-400">*</span></label><input type="email" name="email" value="{{ old('email') }}" class="glass-input" required></div><div><label class="label">Téléphone</label><input type="text" name="telephone" value="{{ old('telephone') }}" class="glass-input" placeholder="+225 07..."></div></div>
</div>
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Affectation</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">DRENA <span class="text-red-400">*</span></label><select name="drena_id" class="glass-input" required>@foreach($drenas as $d)<option value="{{ $d->id }}" {{ old('drena_id')==$d->id?'selected':'' }}>{{ $d->nom }}</option>@endforeach</select></div><div><label class="label">IEPP</label><select name="iepp_id" class="glass-input"><option value="">— Aucun (secondaire) —</option>@foreach($iepps as $i)<option value="{{ $i->id }}" {{ old('iepp_id')==$i->id?'selected':'' }}>{{ $i->nom }}</option>@endforeach</select></div></div>
    <div class="mb-4"><label class="label">Établissement <span class="text-red-400">*</span></label><select name="etablissement_id" class="glass-input" required>@foreach($etablissements as $e)<option value="{{ $e->id }}" {{ old('etablissement_id')==$e->id?'selected':'' }}>{{ $e->nom }} ({{ ucfirst($e->ordre_enseignement ?? 'primaire') }})</option>@endforeach</select></div>
    <div><label class="label">Rôle <span class="text-red-400">*</span></label><select name="role" class="glass-input" required>@foreach($roles as $r)<option value="{{ $r->name }}" {{ old('role')===$r->name?'selected':'' }}>{{ $r->name }}</option>@endforeach</select></div>
</div>
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Informations professionnelles</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Grade</label><input type="text" name="grade" value="{{ old('grade') }}" class="glass-input"></div><div><label class="label">Échelon</label><input type="text" name="echelon" value="{{ old('echelon') }}" class="glass-input"></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Spécialité</label><input type="text" name="specialite" value="{{ old('specialite') }}" class="glass-input" placeholder="Mathématiques, Français..."></div><div><label class="label">Volume horaire (h/sem)</label><input type="number" name="volume_horaire_hebdo" value="{{ old('volume_horaire_hebdo', 24) }}" class="glass-input" min="0" max="40"></div></div>
    <div><label class="label">Date d'intégration</label><input type="date" name="date_integration" value="{{ old('date_integration') }}" class="glass-input"></div>
</div>
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Mot de passe</h3>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Mot de passe <span class="text-red-400">*</span></label><input type="password" name="password" class="glass-input" required minlength="8"></div><div><label class="label">Confirmation <span class="text-red-400">*</span></label><input type="password" name="password_confirmation" class="glass-input" required></div></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn-primary">Créer l'agent</button><a href="{{ route('personnel.index') }}" class="btn-secondary">Annuler</a></div>
</form>
@endsection
