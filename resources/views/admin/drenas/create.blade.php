@extends('layouts.app')
@section('title', 'Créer une DRENA')
@section('page-title', 'Créer une DRENA')
@section('content')
<form method="POST" action="{{ route('admin.drenas.store') }}" class="max-w-2xl">
    @csrf
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Code *</label><input type="text" name="code" class="input" required value="{{ old('code') }}" placeholder="ABJ5"></div>
            <div><label class="label">Région *</label><input type="text" name="region" class="input" required value="{{ old('region') }}"></div>
        </div>
        <div><label class="label">Nom complet *</label><input type="text" name="nom" class="input" required value="{{ old('nom') }}" placeholder="DRENA de..."></div>
        <div class="grid grid-cols-2 gap-4">
            <div><label class="label">Chef-lieu</label><input type="text" name="chef_lieu" class="input" value="{{ old('chef_lieu') }}"></div>
            <div><label class="label">Téléphone</label><input type="text" name="telephone" class="input" value="{{ old('telephone') }}"></div>
        </div>
        <div><label class="label">Email</label><input type="email" name="email" class="input" value="{{ old('email') }}"></div>
        <div><label class="label">Adresse</label><textarea name="adresse" class="input" rows="2">{{ old('adresse') }}</textarea></div>
    </div>
    <div class="mt-4 flex gap-3">
        <button type="submit" class="btn btn-primary">Créer la DRENA</button>
        <a href="{{ route('admin.drenas.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
@endsection
