@extends('layouts.app')
@section('title', 'Modifier ' . $personnel->nom_complet)
@section('page-title', 'Modifier l\'agent')
@section('content')
<form method="POST" action="{{ route('personnel.update', $personnel) }}" enctype="multipart/form-data" class="max-w-3xl">@csrf @method('PUT')
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Nom *</label><input type="text" name="nom" class="input" required value="{{ old('nom', $personnel->nom) }}"></div><div><label class="label">Prénoms *</label><input type="text" name="prenoms" class="input" required value="{{ old('prenoms', $personnel->prenoms) }}"></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Email *</label><input type="email" name="email" class="input" required value="{{ old('email', $personnel->email) }}"></div><div><label class="label">Téléphone</label><input type="text" name="telephone" class="input" value="{{ old('telephone', $personnel->telephone) }}"></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Genre *</label><select name="genre" class="input"><option value="M" {{ $personnel->genre==='M'?'selected':'' }}>M</option><option value="F" {{ $personnel->genre==='F'?'selected':'' }}>F</option></select></div><div><label class="label">Statut *</label><select name="statut" class="input"><option value="actif" {{ $personnel->statut==='actif'?'selected':'' }}>Actif</option><option value="conge" {{ $personnel->statut==='conge'?'selected':'' }}>En congé</option><option value="suspendu" {{ $personnel->statut==='suspendu'?'selected':'' }}>Suspendu</option><option value="radie" {{ $personnel->statut==='radie'?'selected':'' }}>Radié</option></select></div></div>
    <div class="grid grid-cols-3 gap-4 mb-4"><div><label class="label">Grade</label><input type="text" name="grade" class="input" value="{{ old('grade', $personnel->grade) }}"></div><div><label class="label">Spécialité</label><input type="text" name="specialite" class="input" value="{{ old('specialite', $personnel->specialite) }}"></div><div><label class="label">Volume h/sem</label><input type="number" name="volume_horaire_hebdo" class="input" value="{{ old('volume_horaire_hebdo', $personnel->volume_horaire_hebdo) }}"></div></div>
    <div><label class="label">Établissement *</label><select name="etablissement_id" class="input"><option value="">Sélectionnez</option>@foreach($etablissements as $e)<option value="{{ $e->id }}" {{ $personnel->etablissement_id==$e->id?'selected':'' }}>{{ $e->nom }}</option>@endforeach</select></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn btn-primary">Enregistrer</button><a href="{{ route('personnel.show', $personnel) }}" class="btn btn-secondary">Annuler</a></div>
</form>
@endsection
