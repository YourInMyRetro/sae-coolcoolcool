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
        // 1. Base de la requête
        $query = Produit::query()
            ->with(['premierPrix', 'premierePhoto', 'categorie', 'nations', 'variantes.stocks'])
            ->where('visibilite', 'visible');

        // 2. Recherche (Mot clé)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom_produit', 'ILIKE', "%{$search}%")
                  ->orWhere('description_produit', 'ILIKE', "%{$search}%")
                  ->orWhereHas('categorie', function($subQ) use ($search) {
                      $subQ->where('nom_categorie', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // 3. Filtres (Cumulatifs)
        
        // Catégorie
        if ($request->filled('categorie')) {
            $query->where('id_categorie', $request->categorie);

        }

        // Nation (Brésil, France...)
        if ($request->filled('nation')) {
            $query->whereHas('nations', function($q) use ($request) {
                $q->where('nation.id_nation', $request->nation);
            });
        }

        // Couleur (Via les variantes)
        if ($request->filled('couleur')) {
            $query->whereHas('variantes.couleur', function($q) use ($request) {
                $q->where('type_couleur', $request->couleur);
            });
        }

        // Taille (Via les stocks)
        if ($request->filled('taille')) {
            $query->whereHas('variantes.stocks.taille', function($q) use ($request) {
                $q->where('type_taille', $request->taille);
            });
        }

        // Prix Max (Le fameux ajout)
        if ($request->filled('prix_max')) {
            $query->whereHas('variantes', function($q) use ($request) {
                $q->where('prix_total', '<=', $request->prix_max);
            });
        }

        // 4. Tri
        if ($request->filled('sort')) {
            $direction = $request->sort == 'price_asc' ? 'asc' : 'desc';
            $query->orderBy(
                \App\Models\ProduitCouleur::select('prix_total')
                    ->whereColumn('produit_couleur.id_produit', 'produit.id_produit')
                    ->orderBy('prix_total', 'asc')
                    ->limit(1),
                $direction
            );
        } else {
            $query->orderBy('date_creation', 'desc');
        }

        // 5. Exécution
        $produits = $query->get();

        // 6. Données pour les filtres (Sidebar)
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille');
        $allNations = Nation::orderBy('nom_nation')->get();
        $categoriesPlates = Categorie::orderBy('nom_categorie')->get();

        // Groupement des catégories
        $mapping = [
            'VÊTEMENTS' => ['Maillots', 'Shorts', 'Veste', 'T-Shirt'],
            'MATÉRIEL'  => ['Ballons', 'Crampons', 'Accessoires'],
            'COLLECTOR' => ['Signés', 'Vintage']
        ];

        $categoryGroups = [];
        foreach ($mapping as $groupName => $catNames) {
            $categoryGroups[$groupName] = $categoriesPlates->filter(function($cat) use ($catNames) {
                // Recherche partielle (ex: "Maillots" matchera "Maillots Retro")
                foreach($catNames as $name) {
                    if (stripos($cat->nom_categorie, $name) !== false) return true;
                }
                return false;
            });
        }
        
        // Les "Autres"
        $mappedIds = collect($categoryGroups)->flatten()->pluck('id_categorie');
        $others = $categoriesPlates->whereNotIn('id_categorie', $mappedIds);
        if($others->isNotEmpty()) $categoryGroups['AUTRES'] = $others;

        return view('boutique', compact('produits', 'allColors', 'allSizes', 'allNations', 'categoryGroups'));
    }


    public function show(Request $request, $id) {
        $produit = Produit::with(['variantes.couleur', 'variantes.stocks.taille', 'nations', 'categorie', 'photos', 'premierPrix'])->findOrFail($id);
        $viewed = session()->get('viewed_products', []);
        if(!in_array($id, $viewed)) session()->push('viewed_products', $id);
        $produitsVus = Produit::whereIn('id_produit', $viewed)->where('id_produit', '!=', $id)->with('premierePhoto', 'premierPrix')->limit(4)->get();
        $produitsSimilaires = Produit::where('id_categorie', $produit->id_categorie)->where('id_produit', '!=', $id)->with('premierePhoto', 'premierPrix')->inRandomOrder()->limit(4)->get();
        
        $tailles = collect();
        foreach ($produit->variantes as $variante) {
            foreach ($variante->stocks as $stock) {
                if ($stock->taille) $tailles->push($stock->taille);
            }
        }
        $tailles = $tailles->unique('id_taille')->sortBy('id_taille');
        $tailleSelectionnee = $request->query('taille');
        return view('produits.show', compact('produit', 'tailles', 'tailleSelectionnee', 'produitsVus', 'produitsSimilaires'));
    }

    public function home() {
        $featuredProducts = Produit::where('visibilite', 'visible')->limit(4)->get();
        return view('home', compact('featuredProducts'));
    }
}