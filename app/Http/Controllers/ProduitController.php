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
        // 1. REQUÊTE PRODUITS (Inchangée)
        $query = Produit::query()
            ->with(['premierPrix', 'premierePhoto', 'categorie', 'nations'])
            ->where('visibilite', 'visible');

        // Moteur de recherche
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

        // Filtres
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

        // Tri
        if ($request->filled('sort')) {
            $query->leftJoin('produit_couleur', 'produit.id_produit', '=', 'produit_couleur.id_produit')
                  ->select('produit.*')
                  ->distinct();
            
            if ($request->sort == 'price_asc') {
                $query->orderBy('produit_couleur.prix_total', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('produit_couleur.prix_total', 'desc');
            }
        }

        $produits = $query->get();

        // 2. DONNÉES DE FILTRAGE
        $allColors = Couleur::orderBy('type_couleur')->pluck('type_couleur');
        $allSizes = Taille::orderBy('id_taille')->pluck('type_taille');
        $allNations = Nation::orderBy('nom_nation')->get();
        
        // --- C'EST ICI QUE LA MAGIE OPÈRE ---
        // On récupère toutes les catégories plates
        $categoriesPlates = Categorie::orderBy('nom_categorie')->get();

        // On construit la hiérarchie MANUELLEMENT
        // Structure : 'Nom du Groupe' => ['Nom Catégorie 1', 'Nom Catégorie 2']
        $mapping = [
            'UNIVERS VÊTEMENTS' => ['Maillots', 'Vêtement'], 
            'ÉQUIPEMENTS'       => ['Accessoires', 'Ballons'],
            'COLLECTOR'         => ['Objets de collection', 'Signés']
        ];

        $categoryGroups = [];

        // On remplit les groupes avec les objets Catégorie réels
        foreach ($mapping as $groupName => $catNames) {
            $categoryGroups[$groupName] = $categoriesPlates->filter(function($cat) use ($catNames) {
                return in_array($cat->nom_categorie, $catNames);
            });
        }
        
        // On récupère celles qui ne sont pas mappées (au cas où tu en ajoutes d'autres plus tard)
        $mappedIds = $categoriesPlates->whereIn('nom_categorie', array_merge(...array_values($mapping)))->pluck('id_categorie');
        $others = $categoriesPlates->whereNotIn('id_categorie', $mappedIds);
        
        if($others->isNotEmpty()) {
            $categoryGroups['AUTRES'] = $others;
        }

        return view('boutique', compact('produits', 'allColors', 'allSizes', 'allNations', 'categoryGroups'));
    }

    public function home() {
        $featuredProducts = Produit::where('visibilite', 'visible')->limit(4)->get();
        return view('home', compact('featuredProducts'));
    }

    public function show(Request $request, $id) {
        $produit = Produit::with(['variantes.couleur', 'variantes.stocks.taille', 'nations', 'categorie', 'premierePhoto', 'premierPrix'])
            ->findOrFail($id);
        
        $viewed = session()->get('viewed_products', []);

        if(!in_array($id, $viewed)) {
            session()->push('viewed_products', $id);
        }

        $produitsVus = Produit::whereIn('id_produit', $viewed)
            ->where('id_produit', '!=', $id)
            ->with('premierePhoto', 'premierPrix')
            ->limit(4)
            ->get();

        $produitsSimilaires = Produit::where('id_categorie', $produit->id_categorie)
            ->where('id_produit', '!=', $id)
            ->with('premierePhoto', 'premierPrix')
            ->inRandomOrder()
            ->limit(4)
            ->get();
        
        $tailles = collect();
        foreach ($produit->variantes as $variante) {
            foreach ($variante->stocks as $stock) {
                if ($stock->taille) {
                    $tailles->push($stock->taille);
                }
            }
        }
        $tailles = $tailles->unique('id_taille')->sortBy('id_taille');
        $tailleSelectionnee = $request->query('taille');
    
        return view('produits.show', compact('produit', 'tailles', 'tailleSelectionnee', 'produitsVus', 'produitsSimilaires'));
    }
}