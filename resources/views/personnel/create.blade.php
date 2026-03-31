@extends('layouts.app')
@section('title', 'Ajouter un agent')
@section('page-title', 'Ajouter un agent')
@section('content')
<form method="POST" action="{{ route('personnel.store') }}" enctype="multipart/form-data" class="max-w-3xl">@csrf
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Identité</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Matricule *</label><input type="text" name="matricule" class="input" required value="{{ old('matricule') }}"></div><div><label class="label">Genre *</label><select name="genre" class="input" required><option value="M">Masculin</option><option value="F">Féminin</option></select></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Nom *</label><input type="text" name="nom" class="input" required value="{{ old('nom') }}"></div><div><label class="label">Prénoms *</label><input type="text" name="prenoms" class="input" required value="{{ old('prenoms') }}"></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Email *</label><input type="email" name="email" class="input" required value="{{ old('email') }}"></div><div><label class="label">Téléphone</label><input type="text" name="telephone" class="input" value="{{ old('telephone') }}"></div></div>
    <div><label class="label">Date de naissance</label><input type="date" name="date_naissance" class="input w-48" value="{{ old('date_naissance') }}"></div>
</div>
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Informations professionnelles</h3>
    <div class="grid grid-cols-3 gap-4 mb-4"><div><label class="label">Grade</label><input type="text" name="grade" class="input" value="{{ old('grade') }}"></div><div><label class="label">Échelon</label><input type="text" name="echelon" class="input" value="{{ old('echelon') }}"></div><div><label class="label">Spécialité</label><input type="text" name="specialite" class="input" value="{{ old('specialite') }}"></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Volume horaire/sem.</label><input type="number" name="volume_horaire_hebdo" class="input" value="{{ old('volume_horaire_hebdo', 20) }}" min="0" max="40"></div><div><label class="label">Date d'intégration</label><input type="date" name="date_integration" class="input" value="{{ old('date_integration') }}"></div></div>
    <div><label class="label">Établissement *</label><select name="etablissement_id" class="input" required><option value="">Sélectionnez</option>@foreach($etablissements as $e)<option value="{{ $e->id }}" {{ old('etablissement_id')==$e->id?'selected':'' }}>{{ $e->nom }}</option>@endforeach</select></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn btn-primary">Créer l'agent</button><a href="{{ route('personnel.index') }}" class="btn btn-secondary">Annuler</a></div>
</form>
@endsection
