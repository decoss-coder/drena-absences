<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mot de passe oublié — DRENA</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8"><div class="w-16 h-16 rounded-2xl bg-[#1B4F72] flex items-center justify-center mx-auto mb-4"><span class="text-white text-2xl font-bold">D</span></div><h1 class="text-xl font-bold text-gray-900">Mot de passe oublié</h1></div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        @if(session('success'))<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm">{{ $errors->first() }}</div>@endif
        <p class="text-sm text-gray-600 mb-6">Entrez votre email pour recevoir un lien de réinitialisation.</p>
        <form method="POST" action="{{ route('password.email') }}">@csrf
            <div class="mb-5"><label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" id="email" required class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></div>
            <button type="submit" class="w-full py-3 bg-[#1B4F72] text-white text-sm font-semibold rounded-xl hover:bg-[#1A5276]">Envoyer le lien</button>
        </form>
        <a href="{{ route('login') }}" class="block text-center text-sm text-blue-600 hover:underline mt-4">Retour à la connexion</a>
    </div>
</div>
</body></html>
