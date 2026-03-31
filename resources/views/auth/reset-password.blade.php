<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Réinitialiser — DRENA</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8"><div class="w-16 h-16 rounded-2xl bg-[#1B4F72] flex items-center justify-center mx-auto mb-4"><span class="text-white text-2xl font-bold">D</span></div><h1 class="text-xl font-bold text-gray-900">Nouveau mot de passe</h1></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        @if($errors->any())<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>@endif
        <form method="POST" action="{{ route('password.update') }}">@csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" required class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></div>
            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label><input type="password" name="password" required class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"><p class="text-xs text-gray-500 mt-1">Min. 8 caractères, 1 majuscule, 1 chiffre, 1 caractère spécial</p></div>
            <div class="mb-5"><label class="block text-sm font-medium text-gray-700 mb-1">Confirmer</label><input type="password" name="password_confirmation" required class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></div>
            <button type="submit" class="w-full py-3 bg-[#1B4F72] text-white text-sm font-semibold rounded-xl hover:bg-[#1A5276]">Réinitialiser</button>
        </form>
    </div>
</div>
</body></html>
