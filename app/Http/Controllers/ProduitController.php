<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Couleur;
use App\Models\Taille;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        // On initialise la requête sur les produits visibles
        $query = Produit::query()->where('visibilite', 'visible');

        // 1. Recherche Textuelle
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom_produit', 'ILIKE', "%{$search}%")
                  ->orWhere('description_produit', 'ILIKE', "%{$search}%");
            });
        }

        // 2. Filtre par Couleur (ID 4)
        // On cherche les produits qui ont au moins une variante dont la couleur correspond
        if ($request->filled('couleur')) {
            $query->whereHas('variantes.couleur', function($q) use ($request) {
                $q->where('type_couleur', $request->couleur);
            });
        }

        // 3. Filtre par Taille (ID 4)
        // C'est un peu plus profond : Produit -> ProduitCouleur -> StockArticle -> Taille
        if ($request->filled('taille')) {
            $query->whereHas('variantes.stocks.taille', function($q) use ($request) {
                $q->where('type_taille', $request->taille);
            });
        }

        // 4. Tri par Prix (ID 3)
        if ($request->filled('sort')) {
            // On joint la table produit_couleur pour avoir accès au 'prix_total'
            $query->leftJoin('produit_couleur', 'produit.id_produit', '=', 'produit_couleur.id_produit')
                  ->select('produit.*'); // On s'assure de récupérer les champs produit, pas ceux de produit_couleur

            if ($request->sort == 'prix_asc') {
                $query->orderBy('produit_couleur.prix_total', 'asc');
            } elseif ($request->sort == 'prix_desc') {
                $query->orderBy('produit_couleur.prix_total', 'desc');
            }
            
            // Important pour éviter les doublons si un produit a plusieurs couleurs
            $query->distinct();
        }

        // On récupère les résultats avec les relations pour l'affichage (image, prix min)
        $produits = $query->with(['premierPrix', 'premierePhoto'])->get();

        // Récupération des listes pour les menus déroulants (Select)
        // On prend toutes les couleurs et tailles disponibles dans la base pour les afficher dans le filtre
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille'); // Tri par ID pour avoir S, M, L dans l'ordre logique souvent

        return view('produits.index', compact('produits', 'allColors', 'allSizes'));
    }
    
    public function home()
    {
        $featuredProducts = Produit::with(['premierPrix', 'premierePhoto'])
                                   ->where('visibilite', 'visible')
                                   ->limit(4)
                                   ->get();
        return view('home', compact('featuredProducts'));
    }
}