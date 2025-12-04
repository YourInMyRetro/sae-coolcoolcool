<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Professionel;
use App\Models\Panier;
use App\Models\StockArticle; // Nécessaire pour reconstruire les infos produits

class AuthController extends Controller
{
    // --- LOGIN ---

    public function login() {
        return view('auth.login');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'mail' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['mail' => $credentials['mail'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            
            // --- RESTAURATION DU PANIER ---
            $this->restoreCartFromDatabase(Auth::user());

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'mail' => 'Email ou mot de passe incorrect.',
        ])->onlyInput('mail');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // --- INSCRIPTION PARTICULIER ---

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'mail' => 'required|string|email|max:100|unique:utilisateur',
            'date_naissance' => 'required|date',
            'pays_naissance' => 'required|string|max:50',
            'langue' => 'required|string|max:50',
            'password' => 'required|string|min:4|confirmed',
        ]);

        $user = new User();
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->mail = $request->input('mail');
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance');
        $user->langue = $request->input('langue');
        $user->surnom = $request->input('surnom');
        $user->mot_de_passe_chiffre = Hash::make($request->input('password'));

        $user->save();

        Auth::login($user);
        
        // Pas de panier à restaurer pour un nouveau compte, mais on pourrait sauvegarder la session actuelle
        
        return redirect()->route('home')->with('success', 'Bienvenue sur FIFA Store !');
    }

    // --- INSCRIPTION PROFESSIONNEL ---

    public function showRegisterProForm()
    {
        return view('auth.register_pro');
    }

    public function registerPro(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'mail' => 'required|string|email|max:100|unique:utilisateur',
            'date_naissance' => 'required|date',
            'pays_naissance' => 'required|string|max:50',
            'langue' => 'required|string|max:50',
            'password' => 'required|string|min:4|confirmed',
            'nom_societe' => 'required|string|max:100',
            'activite' => 'required|string|max:100',
            'numero_tva' => 'required|string|max:30|unique:professionel,numero_tva_intracommunautaire',
        ]);

        $user = new User();
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->mail = $request->input('mail');
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance');
        $user->langue = $request->input('langue');
        $user->mot_de_passe_chiffre = Hash::make($request->input('password'));
        $user->save();

        $pro = new Professionel();
        $pro->id_utilisateur = $user->id_utilisateur;
        $pro->nom_societe = $request->input('nom_societe');
        $pro->activite = $request->input('activite');
        $pro->numero_tva_intracommunautaire = $request->input('numero_tva');
        $pro->save();

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Compte Professionnel créé avec succès !');
    }

    /**
     * Méthode privée pour restaurer le panier depuis la BDD vers la Session
     */
    private function restoreCartFromDatabase($user)
    {
        // 1. Récupérer le panier en BDD avec ses lignes
        $dbPanier = Panier::with(['lignes'])->where('id_utilisateur', $user->id_utilisateur)->first();

        if (!$dbPanier) {
            return; // Rien à restaurer
        }

        // 2. Récupérer le panier actuel de la session (visiteur)
        $sessionPanier = session('panier', []);

        // 3. Fusionner BDD -> Session
        foreach ($dbPanier->lignes as $ligne) {
            // On a besoin de récupérer les infos détaillées pour reconstruire la session
            // (Nom, Prix, Photo, etc.) car la table ligne_panier n'a que des ID.
            $stock = StockArticle::with(['produitCouleur.produit.photos', 'produitCouleur.couleur', 'taille'])
                                 ->find($ligne->id_stock_article);

            if ($stock && $stock->produitCouleur && $stock->produitCouleur->produit) {
                $produit = $stock->produitCouleur->produit;
                $couleur = $stock->produitCouleur->couleur;
                $taille  = $stock->taille;

                // Reconstitution de la clé unique utilisée dans PanierController
                // Format : id_produit - id_couleur - id_taille
                $panierKey = $produit->id_produit . '-' . $couleur->id_couleur . '-' . $taille->id_taille;

                // Si l'article n'est pas déjà dans la session, on l'ajoute
                // (Ou on pourrait additionner les quantités si on voulait être perfectionniste)
                if (!isset($sessionPanier[$panierKey])) {
                    $sessionPanier[$panierKey] = [
                        "nom" => $produit->nom_produit . " (" . ucfirst($couleur->type_couleur) . " - " . strtoupper($taille->type_taille) . ")",
                        "quantite" => $ligne->quantite,
                        "prix" => $stock->produitCouleur->prix_total,
                        // On prend la première photo dispo
                        "photo" => $produit->photos->first()->url_photo ?? 'img/placeholder.jpg',
                        "id_stock" => $stock->id_stock_article
                    ];
                } else {
                    // Si conflit, on garde la quantité la plus élevée (ou on additionne)
                    // Ici on garde le max pour éviter les doublons accidentels
                    $sessionPanier[$panierKey]['quantite'] = max($sessionPanier[$panierKey]['quantite'], $ligne->quantite);
                }
            }
        }

        // 4. Sauvegarder la fusion dans la Session
        session()->put('panier', $sessionPanier);
    }
}