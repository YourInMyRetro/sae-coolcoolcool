<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Professionel;
use App\Models\Panier;
use App\Models\StockArticle; 

class AuthController extends Controller
{
    // --- LOGIN ---
    public function login() {
        return view('auth.login');
    }

    public function authenticate(Request $request) {
        // 1. Validation des entrées
        $credentials = $request->validate([
            'mail' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // 2. Tentative de connexion (Pour TOUT LE MONDE)
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $this->restoreCartFromDatabase(Auth::user());

            // --- LOGIQUE DE DÉRIVATION ---
            
            // CAS A : C'est le Directeur
            if (Auth::user()->isDirector()) {
                return redirect()->route('directeur.dashboard');
            }

            // CAS B : C'est un utilisateur normal (Client ou Pro)
            return redirect()->intended(route('home'));
        }

        // 3. Echec de connexion
        return back()->withErrors(['mail' => 'Mauvais identifiant ou mot de passe.'])->onlyInput('mail');
    }

    // --- LOGOUT ---
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // --- INSCRIPTION PARTICULIER ---
    public function showRegisterForm() { return view('auth.register'); }

    public function register(Request $request) {
        // 1. Validation avec les nouvelles règles RGPD et COHÉRENCE DATE
        $request->validate([
            'nom' => 'required|max:50', 
            'prenom' => 'required|max:50',
            'mail' => 'required|email|unique:utilisateur', 
            // CORRECTION ID 5 : Cohérence date de naissance (max 120 ans)
            'date_naissance' => 'required|date|before:today|after:-120 years',
            'pays_naissance' => 'required|max:50', 
            'langue' => 'required|max:50',
            'password' => 'required|min:8|confirmed',
            'cgu_consent' => 'accepted',
            'newsletter_optin' => 'nullable',
        ], [
            'cgu_consent.accepted' => 'Vous devez accepter les conditions d\'utilisation et la politique de confidentialité.',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères pour votre sécurité.',
            'date_naissance.after' => 'Veuillez saisir une date de naissance valide (moins de 120 ans).',
            'date_naissance.before' => 'La date de naissance doit être dans le passé.'
        ]);

        // 2. Création de l'utilisateur
        $user = new User();
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->mail = $request->input('mail');
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance');
        $user->langue = $request->input('langue');
        $user->surnom = $request->input('surnom') ?? $request->input('prenom');
        $user->mot_de_passe_chiffre = Hash::make($request->input('password'));
        
        $user->newsletter_optin = $request->has('newsletter_optin'); 

        $user->save();

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Bienvenue sur FIFA Store !');
    }

    // --- INSCRIPTION PRO ---
    public function showRegisterProForm() { return view('auth.register_pro'); }

    public function registerPro(Request $request) {
        $request->validate([
            'nom' => 'required|max:50', 
            'prenom' => 'required|max:50',
            'mail' => 'required|email|unique:utilisateur', 
            'password' => 'required|min:8|confirmed',
            'nom_societe' => 'required|max:100', 
            'numero_tva' => 'required|unique:professionel,numero_tva_intracommunautaire',
            'activite' => 'required|max:100', 
            // CORRECTION ID 5 : Cohérence date de naissance Pro
            'date_naissance' => 'required|date|before:today|after:-120 years', 
            'pays_naissance' => 'required|max:50', 
            'langue' => 'required|max:50',
            'cgu_consent' => 'accepted',
        ], [
            'date_naissance.after' => 'Veuillez saisir une date de naissance valide (moins de 120 ans).'
        ]);

        $user = new User();
        $user->nom = $request->input('nom'); 
        $user->prenom = $request->input('prenom');
        $user->mail = $request->input('mail'); 
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance'); 
        $user->langue = $request->input('langue');
        $user->mot_de_passe_chiffre = Hash::make($request->input('password'));
        
        $user->newsletter_optin = $request->has('newsletter_optin');
        
        $user->save();

        $pro = new Professionel();
        $pro->id_utilisateur = $user->id_utilisateur;
        $pro->nom_societe = $request->input('nom_societe');
        $pro->activite = $request->input('activite');
        $pro->numero_tva_intracommunautaire = $request->input('numero_tva');
        $pro->save();

        Auth::login($user);
        return redirect()->route('home')->with('success', 'Compte Pro créé !');
    }

    private function restoreCartFromDatabase($user) {
        $dbPanier = Panier::with(['lignes'])->where('id_utilisateur', $user->id_utilisateur)->first();
        if (!$dbPanier) return;
        $sessionPanier = session('panier', []);
        foreach ($dbPanier->lignes as $ligne) {
            $stock = StockArticle::with(['produitCouleur.produit.photos', 'produitCouleur.couleur', 'taille'])->find($ligne->id_stock_article);
            if ($stock) {
                $p = $stock->produitCouleur->produit;
                $key = $p->id_produit . '-' . $stock->produitCouleur->couleur->id_couleur . '-' . $stock->taille->id_taille;
                if (!isset($sessionPanier[$key])) {
                    $sessionPanier[$key] = [
                        "nom" => $p->nom_produit . " (" . ucfirst($stock->produitCouleur->couleur->type_couleur) . " - " . strtoupper($stock->taille->type_taille) . ")",
                        "quantite" => $ligne->quantite, "prix" => $stock->produitCouleur->prix_total,
                        "photo" => $p->photos->first()->url_photo ?? 'img/placeholder.jpg', "id_stock" => $stock->id_stock_article
                    ];
                } else { $sessionPanier[$key]['quantite'] = max($sessionPanier[$key]['quantite'], $ligne->quantite); }
            }
        }
        session()->put('panier', $sessionPanier);
    }
}