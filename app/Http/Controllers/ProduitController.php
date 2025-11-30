<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Couleur;
use App\Models\Taille;
use App\Models\Nation;
use App\Models\Categorie;
use App\Models\ProduitCouleur; // <--- INDISPENSABLE pour le tri !
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        // 1. Requête de base (On charge les relations nécessaires)
        $query = Produit::query()
            ->with(['premierPrix', 'premierePhoto', 'categorie', 'nations'])
            ->where('visibilite', 'visible');

        // 2. Moteur de Recherche
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

        // 4. TRI PAR PRIX (Correction Spéciale PostgreSQL)
        // On utilise une "sous-requête" pour trier sans perturber les autres filtres.
        if ($request->filled('sort')) {
            if ($request->sort == 'price_asc') {
                $query->orderBy(
                    ProduitCouleur::select('prix_total')
                        ->whereColumn('produit_couleur.id_produit', 'produit.id_produit')
                        ->orderBy('prix_total', 'asc')
                        ->limit(1)
                , 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy(
                    ProduitCouleur::select('prix_total')
                        ->whereColumn('produit_couleur.id_produit', 'produit.id_produit')
                        ->orderBy('prix_total', 'asc') // On trie selon le prix de base du produit
                        ->limit(1)
                , 'desc');
            }
        }

        // Exécution finale
        $produits = $query->get();

        // Récupération des listes pour les filtres
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille');
        $allNations = Nation::orderBy('nom_nation')->get();
        $allCategories = Categorie::orderBy('nom_categorie')->get();

        return view('produits.index', compact('produits', 'allColors', 'allSizes', 'allNations', 'allCategories'));
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