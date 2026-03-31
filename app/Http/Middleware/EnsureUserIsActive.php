<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->actif || $user->statut === 'radie' || $user->statut === 'decede') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['login' => 'Votre compte a été désactivé. Contactez votre administrateur.']);
            }

            if ($user->isLocked()) {
                Auth::logout();
                $request->session()->invalidate();

                return redirect()->route('login')
                    ->withErrors(['login' => 'Votre compte est temporairement verrouillé.']);
            }
        }

        return $next($request);
    }
}
