<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — DRENA Absences</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-[#1B4F72] flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-white text-2xl font-bold">D</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">DRENA Absences</h1>
            <p class="text-sm text-gray-500 mt-1">Gestion des absences du personnel — MENA</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Connexion</h2>
            <p class="text-sm text-gray-500 mb-6">Entrez votre matricule ou email pour vous connecter.</p>

            @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Matricule ou email</label>
                    <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="ENS-2024-00312 ou email@exemple.ci">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                           placeholder="Votre mot de passe">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Se souvenir de moi</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        Mot de passe oublié ?
                    </a>
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-[#1B4F72] text-white text-sm font-semibold rounded-xl hover:bg-[#1A5276] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                    Se connecter
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} Ministère de l'Éducation Nationale et de l'Alphabétisation — Côte d'Ivoire
        </p>
    </div>
</body>
</html>
