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
        // On prépare la requête de base avec les relations nécessaires pour optimiser les performances
        $query = Produit::query()
            ->with(['premierPrix', 'premierePhoto', 'categorie', 'nations'])
            ->where('visibilite', 'visible');

        // 1. MOTEUR DE RECHERCHE AVANCÉ
        if ($request->filled('search')) {
            $search = $request->input('search');
            
            // On groupe les conditions "OU" dans une parenthèse pour ne pas casser les autres filtres
            $query->where(function($q) use ($search) {
                // Recherche par Nom
                $q->where('nom_produit', 'ILIKE', "%{$search}%")
                  // Recherche par Description
                  ->orWhere('description_produit', 'ILIKE', "%{$search}%")
                  // Recherche par ID (si l'utilisateur tape un nombre)
                  ->orWhereRaw("CAST(id_produit AS TEXT) LIKE ?", ["%{$search}%"])
                  // Recherche par Nom de Catégorie (ex: taper "Maillot" trouve tous les produits de la catégorie Maillot)
                  ->orWhereHas('categorie', function($subQ) use ($search) {
                      $subQ->where('nom_categorie', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // 2. Filtre Catégorie
        if ($request->filled('categorie')) {
            $query->where('id_categorie', $request->categorie);
        }

        // 3. Filtre Nation
        if ($request->filled('nation')) {
            $query->whereHas('nations', function($q) use ($request) {
                $q->where('nation.id_nation', $request->nation);
            });
        }

        // 4. Filtre Couleur
        if ($request->filled('couleur')) {
            $query->whereHas('variantes.couleur', function($q) use ($request) {
                $q->where('type_couleur', $request->couleur);
            });
        }

        // 5. Filtre Taille
        if ($request->filled('taille')) {
            $query->whereHas('variantes.stocks.taille', function($q) use ($request) {
                $q->where('type_taille', $request->taille);
            });
        }

        // 6. Tri
        if ($request->filled('sort')) {
            $query->leftJoin('produit_couleur', 'produit.id_produit', '=', 'produit_couleur.id_produit')
                  ->select('produit.*'); // Important pour garder l'ID produit correct
            
            if ($request->sort == 'price_asc') {
                $query->orderBy('produit_couleur.prix_total', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('produit_couleur.prix_total', 'desc');
            }
            $query->distinct();
        }

        // Exécution de la requête
        $produits = $query->get();

        // Récupération des données pour les filtres (Listes déroulantes)
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille');
        $allNations = Nation::orderBy('nom_nation')->get();
        $allCategories = Categorie::orderBy('nom_categorie')->get();

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