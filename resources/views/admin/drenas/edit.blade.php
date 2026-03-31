@extends('layouts.app')
@section('title', 'Modifier ' . $drena->nom)
@section('page-title', 'Modifier la DRENA')
@section('content')
<form method="POST" action="{{ route('admin.drenas.update', $drena) }}" class="max-w-2xl">
    @csrf @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Code</label><input type="text" class="input bg-gray-50" value="{{ $drena->code }}" disabled></div>
            <div><label class="label">Région *</label><input type="text" name="region" class="input" required value="{{ old('region', $drena->region) }}"></div>
        </div>
        <div><label class="label">Nom complet *</label><input type="text" name="nom" class="input" required value="{{ old('nom', $drena->nom) }}"></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Chef-lieu</label><input type="text" name="chef_lieu" class="input" value="{{ old('chef_lieu', $drena->chef_lieu) }}"></div>
            <div><label class="label">Téléphone</label><input type="text" name="telephone" class="input" value="{{ old('telephone', $drena->telephone) }}"></div>
        </div>
        <div><label class="label">Email</label><input type="email" name="email" class="input" value="{{ old('email', $drena->email) }}"></div>
        <div><label class="label">Adresse</label><textarea name="adresse" class="input" rows="2">{{ old('adresse', $drena->adresse) }}</textarea></div>
        <label class="flex items-center gap-2"><input type="checkbox" name="actif" value="1" {{ $drena->actif ? 'checked' : '' }} class="w-4 h-4 rounded"><span class="text-sm">DRENA active</span></label>
    </div>
    <div class="mt-4 flex gap-3">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.drenas.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
@endsection
