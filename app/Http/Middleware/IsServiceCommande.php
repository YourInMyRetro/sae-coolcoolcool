<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsServiceCommande
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur est connecté et a le bon rôle
        if (Auth::check() && Auth::user()->isServiceCommande()) {
            return $next($request);
        }

        // Sinon, retour à l'accueil ou erreur 403
        return redirect('/')->with('error', 'Accès non autorisé.');
    }
}