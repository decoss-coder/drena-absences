<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — DRENA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>*{font-family:'Inter',sans-serif}body{background:#f8f7ff;min-height:100vh}.bg-pattern{background-image:radial-gradient(circle at 20% 50%,rgba(120,100,255,0.06) 0%,transparent 50%),radial-gradient(circle at 80% 20%,rgba(167,139,250,0.08) 0%,transparent 40%)}.card{background:#fff;border-radius:24px;box-shadow:0 4px 40px rgba(100,80,200,0.06),0 1px 3px rgba(0,0,0,0.04);border:1px solid rgba(120,100,255,0.06)}.input-elegant{width:100%;padding:14px 16px;border:1.5px solid #e8e5f5;border-radius:14px;font-size:14px;color:#2d2640;background:#faf9ff;transition:all .3s}.input-elegant:focus{outline:none;border-color:#7c6aef;box-shadow:0 0 0 4px rgba(124,106,239,0.1);background:#fff}.input-elegant::placeholder{color:#b0a8cc}.btn-main{background:linear-gradient(135deg,#7c6aef 0%,#6246ea 100%);color:#fff;border-radius:14px;padding:14px;font-weight:600;font-size:14px;transition:all .3s;box-shadow:0 4px 15px rgba(98,70,234,0.25)}.btn-main:hover{transform:translateY(-1px);box-shadow:0 8px 25px rgba(98,70,234,0.35)}</style>
</head>
<body class="bg-pattern flex items-center justify-center p-4">
<div class="w-full max-w-[420px]">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-violet-500/20"><svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
        <h1 class="text-xl font-bold text-gray-800">Mot de passe oublié</h1>
        <p class="text-sm text-gray-400 mt-1">Recevez un lien de réinitialisation</p>
    </div>
    <div class="card p-8">
        @if(session('success'))<div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">@csrf
            <div><label for="email" class="block text-sm font-medium text-gray-600 mb-2">Adresse email</label><input type="email" name="email" id="email" required class="input-elegant" placeholder="email@exemple.ci" value="{{ old('email') }}"></div>
            <button type="submit" class="btn-main w-full">Envoyer le lien</button>
        </form>
        <a href="{{ route('login') }}" class="block text-center text-sm text-violet-600 hover:text-violet-800 font-medium mt-5">Retour à la connexion</a>
    </div>
</div>
</body>
</html>
