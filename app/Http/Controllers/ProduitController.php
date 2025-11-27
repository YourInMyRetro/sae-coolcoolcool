<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Couleur; // Importation des modèles
use App\Models\Taille;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::query()->where('visibilite', 'visible');

        // 1. Recherche
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom_produit', 'ILIKE', "%{$search}%")
                  ->orWhere('description_produit', 'ILIKE', "%{$search}%");
            });
        }

        // 2. Filtre Couleur
        if ($request->filled('couleur')) {
            $query->whereHas('variantes.couleur', function($q) use ($request) {
                $q->where('type_couleur', $request->couleur);
            });
        }

        // 3. Filtre Taille
        if ($request->filled('taille')) {
            $query->whereHas('variantes.stocks.taille', function($q) use ($request) {
                $q->where('type_taille', $request->taille);
            });
        }

        // 4. Tri
        if ($request->filled('sort')) {
            $query->leftJoin('produit_couleur', 'produit.id_produit', '=', 'produit_couleur.id_produit')
                  ->select('produit.*');
            
            if ($request->sort == 'price_asc') { // Correction ici: price_asc pour correspondre à ton HTML
                $query->orderBy('produit_couleur.prix_total', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('produit_couleur.prix_total', 'desc');
            }
            $query->distinct();
        }

        $produits = $query->with(['premierPrix', 'premierePhoto'])->get();

        // On récupère les listes pour les filtres
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille');

        // On appelle bien 'boutique' ici !
        return view('boutique', compact('produits', 'allColors', 'allSizes'));
    }

    public function home() {
        $featuredProducts = Produit::where('visibilite', 'visible')->limit(4)->get();
        return view('home', compact('featuredProducts'));
    }
}