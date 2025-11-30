<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Couleur;
use App\Models\Taille;
use App\Models\Nation;
use App\Models\Categorie;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        // 1. Requête de base
        // On charge les relations nécessaires (prix, photo, etc.)
        $query = Produit::query()
            ->with(['premierPrix', 'premierePhoto', 'categorie', 'nations'])
            ->where('visibilite', 'visible');

        // 2. MOTEUR DE RECHERCHE
        if ($request->filled('search')) {
            $search = $request->input('search');
            
            $query->where(function($q) use ($search) {
                $q->where('nom_produit', 'ILIKE', "%{$search}%")
                  ->orWhere('description_produit', 'ILIKE', "%{$search}%")
                  ->orWhereRaw("CAST(id_produit AS TEXT) LIKE ?", ["%{$search}%"])
                  ->orWhereHas('categorie', function($subQ) use ($search) {
                      $subQ->where('nom_categorie', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // 3. Filtres (Catégorie, Nation, Couleur, Taille)
        if ($request->filled('categorie')) {
            $query->where('id_categorie', $request->categorie);
        }

        if ($request->filled('nation')) {
            $query->whereHas('nations', function($q) use ($request) {
                $q->where('nation.id_nation', $request->nation);
            });
        }

        if ($request->filled('couleur')) {
            $query->whereHas('variantes.couleur', function($q) use ($request) {
                $q->where('type_couleur', $request->couleur);
            });
        }

        if ($request->filled('taille')) {
            $query->whereHas('variantes.stocks.taille', function($q) use ($request) {
                $q->where('type_taille', $request->taille);
            });
        }

        // --- ÉTAPE CRUCIALE : Exécution de la requête SANS tri SQL ---
        // Cela évite l'erreur "SELECT DISTINCT" de PostgreSQL à 100%
        $produits = $query->get();

        // 4. TRI EN PHP (C'est ici que la magie opère sans bug)
        if ($request->filled('sort')) {
            if ($request->sort == 'price_asc') {
                $produits = $produits->sortBy(function($produit) {
                    // On trie par le prix total, ou un très grand nombre si pas de prix
                    return $produit->premierPrix->prix_total ?? 99999999;
                });
            } elseif ($request->sort == 'price_desc') {
                $produits = $produits->sortByDesc(function($produit) {
                    // On trie par le prix total, ou 0 si pas de prix
                    return $produit->premierPrix->prix_total ?? 0;
                });
            }
        }

        // Récupération des données pour les filtres (Listes déroulantes)
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille');
        $allNations = Nation::orderBy('nom_nation')->get();
        $allCategories = Categorie::orderBy('nom_categorie')->get();

        // On renvoie vers ta vue 'boutique'
        return view('boutique', compact('produits', 'allColors', 'allSizes', 'allNations', 'allCategories'));
    }

    public function home() {
        $featuredProducts = Produit::where('visibilite', 'visible')->limit(4)->get();
        return view('home', compact('featuredProducts'));
    }

    public function show($id) {
        $produit = Produit::with(['variantes.couleur', 'variantes.stocks.taille', 'nations', 'categorie'])->findOrFail($id);
        return view('produits.show', compact('produit'));
    }
}