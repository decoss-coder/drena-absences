@extends('layouts.app')
@section('title', 'Ajouter une DRENA')
@section('page-title', 'Ajouter une DRENA')
@section('content')
<form method="POST" action="{{ route('admin.drenas.store') }}" class="max-w-2xl">@csrf
<div class="card-white p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 mb-5">Informations</h3>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Code <span class="text-red-400">*</span></label><input type="text" name="code" value="{{ old('code') }}" class="glass-input" required></div><div><label class="label">Nom <span class="text-red-400">*</span></label><input type="text" name="nom" value="{{ old('nom') }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Région <span class="text-red-400">*</span></label><input type="text" name="region" value="{{ old('region') }}" class="glass-input" required></div><div><label class="label">Chef-lieu</label><input type="text" name="chef_lieu" value="{{ old('chef_lieu') }}" class="glass-input"></div></div>
    <div class="grid grid-cols-2 gap-4"><div><label class="label">Téléphone</label><input type="text" name="telephone" value="{{ old('telephone') }}" class="glass-input"></div><div><label class="label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="glass-input"></div></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn-primary">Créer</button><a href="{{ route('admin.drenas.index') }}" class="btn-secondary">Annuler</a></div>
</form>
@endsection
