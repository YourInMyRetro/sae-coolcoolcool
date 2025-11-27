<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    // Page d'accueil (Home)
    public function home()
    {
        // On récupère quelques produits "vedettes" pour l'accueil
        $featuredProducts = Produit::with(['premierPrix', 'premierePhoto'])
                                   ->where('visibilite', 'visible')
                                   ->limit(4)
                                   ->get();
        return view('home', compact('featuredProducts'));
    }

    // Page de Recherche / Boutique
    public function index(Request $request)
    {
        $query = Produit::with(['premierPrix', 'premierePhoto'])
                        ->where('visibilite', 'visible');

        // Recherche par mot-clé (Barre de recherche)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nom_produit', 'ILIKE', "%{$search}%")
                  ->orWhere('description_produit', 'ILIKE', "%{$search}%");
        }

        // SELECT * ALL (avec les filtres appliqués)
        $produits = $query->get();

        return view('produits.index', compact('produits'));
    }
}