<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — DRENA Absences</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *{font-family:'Inter',sans-serif}
        body{background:#f8f7ff;min-height:100vh}
        .bg-pattern{background-image:radial-gradient(circle at 20% 50%,rgba(120,100,255,0.06) 0%,transparent 50%),radial-gradient(circle at 80% 20%,rgba(167,139,250,0.08) 0%,transparent 40%),radial-gradient(circle at 50% 80%,rgba(99,102,241,0.05) 0%,transparent 50%)}
        .card{background:#fff;border-radius:24px;box-shadow:0 4px 40px rgba(100,80,200,0.06),0 1px 3px rgba(0,0,0,0.04);border:1px solid rgba(120,100,255,0.06)}
        .input-elegant{width:100%;padding:14px 16px 14px 48px;border:1.5px solid #e8e5f5;border-radius:14px;font-size:14px;color:#2d2640;background:#faf9ff;transition:all .3s}
        .input-elegant:focus{outline:none;border-color:#7c6aef;box-shadow:0 0 0 4px rgba(124,106,239,0.1);background:#fff}
        .input-elegant::placeholder{color:#b0a8cc}
        .btn-main{background:linear-gradient(135deg,#7c6aef 0%,#6246ea 100%);color:#fff;border-radius:14px;padding:14px;font-weight:600;font-size:14px;transition:all .3s;box-shadow:0 4px 15px rgba(98,70,234,0.25)}
        .btn-main:hover{transform:translateY(-1px);box-shadow:0 8px 25px rgba(98,70,234,0.35)}
        .btn-main:active{transform:translateY(0)}
        .floating-shape{position:absolute;border-radius:50%;opacity:.5}
        @keyframes drift{0%,100%{transform:translate(0,0) rotate(0deg)}33%{transform:translate(15px,-20px) rotate(5deg)}66%{transform:translate(-10px,15px) rotate(-3deg)}}
        .drift{animation:drift 12s ease-in-out infinite}
    </style>
</head>
<body class="bg-pattern flex items-center justify-center p-4 relative overflow-hidden">
    <div class="floating-shape w-72 h-72 bg-gradient-to-br from-violet-200/40 to-indigo-200/30 drift" style="top:-5%;right:-3%"></div>
    <div class="floating-shape w-56 h-56 bg-gradient-to-br from-purple-200/30 to-pink-100/20 drift" style="bottom:5%;left:-3%;animation-delay:4s"></div>
    <div class="floating-shape w-40 h-40 bg-gradient-to-br from-indigo-200/30 to-violet-100/20 drift" style="top:30%;left:10%;animation-delay:8s"></div>

    <div class="w-full max-w-[420px] relative z-10">
        <div class="text-center mb-8">
            <div class="w-[72px] h-[72px] rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center mx-auto mb-5 shadow-xl shadow-violet-500/20">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h1 class="text-[26px] font-bold text-gray-800 tracking-tight">DRENA Absences</h1>
            <p class="text-sm text-gray-400 mt-1.5">Gestion des absences du personnel — MENA</p>
        </div>

        <div class="card p-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-1">Bon retour !</h2>
            <p class="text-sm text-gray-400 mb-7">Connectez-vous à votre espace.</p>

            @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-600 mb-2">Matricule ou email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg class="w-5 h-5 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                        <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus class="input-elegant" placeholder="ENS-2024-00312 ou email@exemple.ci">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-600 mb-2">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg class="w-5 h-5 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
                        <input type="password" name="password" id="password" required class="input-elegant" placeholder="Votre mot de passe">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer"><input type="checkbox" name="remember" class="w-4 h-4 rounded-md border-gray-300 text-violet-600 focus:ring-violet-500"><span class="text-sm text-gray-500">Se souvenir</span></label>
                    <a href="{{ route('password.request') }}" class="text-sm text-violet-600 hover:text-violet-800 font-medium transition">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn-main w-full">Se connecter</button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">&copy; {{ date('Y') }} Ministère de l'Éducation Nationale — Côte d'Ivoire</p>
    </div>
</body>
</html>
