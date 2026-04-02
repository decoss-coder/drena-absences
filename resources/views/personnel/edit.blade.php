@extends('layouts.app')
@section('title', 'Modifier — ' . $agent->nom_complet)
@section('page-title', 'Modifier l\'agent')
@section('page-subtitle', $agent->matricule . ' — ' . $agent->nom_complet)
@section('content')
<form method="POST" action="{{ route('personnel.update', $agent) }}" class="max-w-3xl">@csrf @method('PUT')
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Identité</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Nom <span class="text-red-400">*</span></label><input type="text" name="nom" value="{{ old('nom', $agent->nom) }}" class="glass-input" required></div><div><label class="label">Prénoms <span class="text-red-400">*</span></label><input type="text" name="prenoms" value="{{ old('prenoms', $agent->prenoms) }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Genre</label><select name="genre" class="glass-input"><option value="M" {{ $agent->genre==='M'?'selected':'' }}>Masculin</option><option value="F" {{ $agent->genre==='F'?'selected':'' }}>Féminin</option></select></div><div><label class="label">Matricule</label><input type="text" name="matricule" value="{{ $agent->matricule }}" class="glass-input bg-gray-50" readonly></div></div>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Email</label><input type="email" name="email" value="{{ old('email', $agent->email) }}" class="glass-input" required></div><div><label class="label">Téléphone</label><input type="text" name="telephone" value="{{ old('telephone', $agent->telephone) }}" class="glass-input"></div></div>
</div>
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Affectation</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">DRENA</label><select name="drena_id" class="glass-input" required>@foreach($drenas as $d)<option value="{{ $d->id }}" {{ $agent->drena_id==$d->id?'selected':'' }}>{{ $d->nom }}</option>@endforeach</select></div><div><label class="label">IEPP</label><select name="iepp_id" class="glass-input"><option value="">— Aucun —</option>@foreach($iepps as $i)<option value="{{ $i->id }}" {{ $agent->iepp_id==$i->id?'selected':'' }}>{{ $i->nom }}</option>@endforeach</select></div></div>
    <div class="mb-4"><label class="label">Établissement</label><select name="etablissement_id" class="glass-input">@foreach($etablissements as $e)<option value="{{ $e->id }}" {{ $agent->etablissement_id==$e->id?'selected':'' }}>{{ $e->nom }} ({{ ucfirst($e->ordre_enseignement ?? 'primaire') }})</option>@endforeach</select></div>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Statut</label><select name="statut" class="glass-input"><option value="actif" {{ $agent->statut==='actif'?'selected':'' }}>Actif</option><option value="conge" {{ $agent->statut==='conge'?'selected':'' }}>En congé</option><option value="mutation" {{ $agent->statut==='mutation'?'selected':'' }}>Mutation</option><option value="radie" {{ $agent->statut==='radie'?'selected':'' }}>Radié</option></select></div><div><label class="label">Compte actif</label><select name="actif" class="glass-input"><option value="1" {{ $agent->actif?'selected':'' }}>Oui</option><option value="0" {{ !$agent->actif?'selected':'' }}>Non</option></select></div></div>
</div>
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Professionnel</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Grade</label><input type="text" name="grade" value="{{ old('grade', $agent->grade) }}" class="glass-input"></div><div><label class="label">Échelon</label><input type="text" name="echelon" value="{{ old('echelon', $agent->echelon) }}" class="glass-input"></div></div>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Spécialité</label><input type="text" name="specialite" value="{{ old('specialite', $agent->specialite) }}" class="glass-input"></div><div><label class="label">Volume horaire</label><input type="number" name="volume_horaire_hebdo" value="{{ old('volume_horaire_hebdo', $agent->volume_horaire_hebdo) }}" class="glass-input"></div></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn-primary">Enregistrer</button><a href="{{ route('personnel.show', $agent) }}" class="btn-secondary">Annuler</a></div>
</form>
@endsection
