<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Afficher le formulaire de connexion
    public function login() {
        return view('auth.login');
    }

    // Traiter la connexion
    public function authenticate(Request $request) {
        // Validation des champs
        $credentials = $request->validate([
            'mail' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentative de connexion (Laravel mappe 'password' vers 'mot_de_passe_chiffre' grâce au modèle)
        if (Auth::attempt(['mail' => $credentials['mail'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            
            // Redirection vers la page voulue (ex: le panier ou la commande)
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'mail' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('mail');
    }

    // Déconnexion
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}