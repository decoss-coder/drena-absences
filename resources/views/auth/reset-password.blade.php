<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser — DRENA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>*{font-family:'Inter',sans-serif}body{background:#f8f7ff;min-height:100vh}.bg-pattern{background-image:radial-gradient(circle at 20% 50%,rgba(120,100,255,0.06) 0%,transparent 50%),radial-gradient(circle at 80% 20%,rgba(167,139,250,0.08) 0%,transparent 40%)}.card{background:#fff;border-radius:24px;box-shadow:0 4px 40px rgba(100,80,200,0.06),0 1px 3px rgba(0,0,0,0.04);border:1px solid rgba(120,100,255,0.06)}.input-elegant{width:100%;padding:14px 16px;border:1.5px solid #e8e5f5;border-radius:14px;font-size:14px;color:#2d2640;background:#faf9ff;transition:all .3s}.input-elegant:focus{outline:none;border-color:#7c6aef;box-shadow:0 0 0 4px rgba(124,106,239,0.1);background:#fff}.input-elegant::placeholder{color:#b0a8cc}.btn-main{background:linear-gradient(135deg,#7c6aef 0%,#6246ea 100%);color:#fff;border-radius:14px;padding:14px;font-weight:600;font-size:14px;transition:all .3s;box-shadow:0 4px 15px rgba(98,70,234,0.25)}.btn-main:hover{transform:translateY(-1px);box-shadow:0 8px 25px rgba(98,70,234,0.35)}</style>
</head>
<body class="bg-pattern flex items-center justify-center p-4">
<div class="w-full max-w-[420px]">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-violet-500/20"><svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></div>
        <h1 class="text-xl font-bold text-gray-800">Nouveau mot de passe</h1>
        <p class="text-sm text-gray-400 mt-1">Choisissez un mot de passe sécurisé</p>
    </div>
    <div class="card p-8">
        @if($errors->any())<div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">@csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div><label class="block text-sm font-medium text-gray-600 mb-2">Email</label><input type="email" name="email" value="{{ old('email', request('email')) }}" class="input-elegant" required></div>
            <div><label class="block text-sm font-medium text-gray-600 mb-2">Nouveau mot de passe</label><input type="password" name="password" class="input-elegant" required minlength="8" placeholder="Min. 8 caractères"></div>
            <div><label class="block text-sm font-medium text-gray-600 mb-2">Confirmation</label><input type="password" name="password_confirmation" class="input-elegant" required></div>
            <button type="submit" class="btn-main w-full">Réinitialiser</button>
        </form>
    </div>
</div>
</body>
</html>
