@extends('layouts.app')
@section('title', 'Modifier — ' . $drena->nom)
@section('page-title', 'Modifier la DRENA')
@section('page-subtitle', $drena->nom)
@section('content')
<form method="POST" action="{{ route('admin.drenas.update', $drena) }}" class="max-w-2xl">@csrf @method('PUT')
<div class="card-white p-6 mb-6">
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Code</label><input type="text" name="code" value="{{ $drena->code }}" class="glass-input" required></div><div><label class="label">Nom</label><input type="text" name="nom" value="{{ $drena->nom }}" class="glass-input" required></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Région</label><input type="text" name="region" value="{{ $drena->region }}" class="glass-input" required></div><div><label class="label">Chef-lieu</label><input type="text" name="chef_lieu" value="{{ $drena->chef_lieu }}" class="glass-input"></div></div>
    <div class="grid grid-cols-2 gap-4 mb-4"><div><label class="label">Téléphone</label><input type="text" name="telephone" value="{{ $drena->telephone }}" class="glass-input"></div><div><label class="label">Email</label><input type="email" name="email" value="{{ $drena->email }}" class="glass-input"></div></div>
    <div><label class="label">Statut</label><select name="actif" class="glass-input"><option value="1" {{ $drena->actif?'selected':'' }}>Active</option><option value="0" {{ !$drena->actif?'selected':'' }}>Inactive</option></select></div>
</div>
<div class="flex gap-3"><button type="submit" class="btn-primary">Enregistrer</button><a href="{{ route('admin.drenas.index') }}" class="btn-secondary">Annuler</a></div>
</form>
@endsection
