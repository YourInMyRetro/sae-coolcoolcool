<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Models\Couleur;
use App\Models\ProduitCouleur;
use App\Models\Taille;
use App\Models\StockArticle;
use Carbon\Carbon;

class DirecteurController extends Controller
{
    // =================================================================
    // US 29 & 30 : DASHBOARD & STATISTIQUES
    // =================================================================
    public function dashboard()
    {
        // 1. Chiffre d'affaires par mois (US 29)
        // On tape dans la table 'commande' directement
        $ventesMensuelles = DB::table('commande')
            ->select(
                DB::raw("TO_CHAR(date_commande, 'YYYY-MM') as mois"),
                DB::raw("SUM(montant_total) as total_ventes")
            )
            ->where('statut_livraison', '!=', 'Annulée') // On ignore les annulées
            ->groupBy('mois')
            ->orderBy('mois', 'desc')
            ->limit(12) // Les 12 derniers mois
            ->get();

        // 2. Chiffre d'affaires par Catégorie par mois (US 30)
        // Là c'est la guerre : jointure massive pour remonter de la ligne de commande à la catégorie
        $ventesParCategorie = DB::table('ligne_commande')
            ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('estplacee', 'ligne_commande.id_ligne_commande', '=', 'estplacee.id_ligne_commande')
            ->join('stock_article', 'estplacee.id_stock_article', '=', 'stock_article.id_stock_article')
            ->join('produit_couleur', 'stock_article.id_produit_couleur', '=', 'produit_couleur.id_produit_couleur')
            ->join('produit', 'produit_couleur.id_produit', '=', 'produit.id_produit')
            ->join('categorie', 'produit.id_categorie', '=', 'categorie.id_categorie')
            ->select(
                DB::raw("TO_CHAR(commande.date_commande, 'YYYY-MM') as mois"),
                'categorie.nom_categorie',
                DB::raw("SUM(ligne_commande.prix_unitaire_negocie * ligne_commande.quantite_commande) as total")
            )
            ->where('commande.statut_livraison', '!=', 'Annulée')
            ->groupBy('mois', 'categorie.nom_categorie')
            ->orderBy('mois', 'desc')
            ->orderBy('total', 'desc')
            ->get();

        return view('directeur.dashboard', compact('ventesMensuelles', 'ventesParCategorie'));
    }

    // =================================================================
    // US 40 & 54 : GESTION DES PRODUITS SANS PRIX
    // =================================================================
    public function produitsIncomplets()
    {
        // US 40 : On cherche les produits qui n'ont AUCUNE liaison dans produit_couleur
        // (C'est dans produit_couleur que se trouve le prix)
        $produitsSansPrix = Produit::whereDoesntHave('produitCouleurs')->get();
        
        // On charge aussi les couleurs pour le formulaire d'ajout (US 54)
        $couleurs = Couleur::all();

        return view('directeur.produits_incomplets', compact('produitsSansPrix', 'couleurs'));
    }

    public function updatePrix(Request $request, $id)
    {
        // US 54 : Fixer un prix = Créer une déclinaison (Produit + Couleur + Prix)
        $request->validate([
            'id_couleur' => 'required|exists:couleur,id_couleur',
            'prix_total' => 'required|numeric|min:0',
        ]);

        // 1. On crée la liaison Produit-Couleur (le prix est ici)
        $produitCouleur = new ProduitCouleur();
        $produitCouleur->id_produit = $id;
        $produitCouleur->id_couleur = $request->input('id_couleur');
        $produitCouleur->prix_total = $request->input('prix_total');
        // Optionnel : prix promo (on laisse null pour l'instant)
        $produitCouleur->save();

        // 2. CRITIQUE : Pour qu'un produit soit vendable, il faut du STOCK et une TAILLE
        // On va créer un stock initial à 0 pour toutes les tailles (S, M, L, XL...) par défaut
        // Sinon le produit existera mais sera introuvable en rayon.
        $tailles = Taille::all();
        foreach($tailles as $taille) {
            $stock = new StockArticle();
            $stock->id_produit_couleur = $produitCouleur->id_produit_couleur;
            $stock->id_taille = $taille->id_taille;
            $stock->stock = 0; // Le stockiste gérera ça plus tard
            $stock->save();
        }

        // 3. On passe le produit en "Visible" s'il était caché
        $produit = Produit::findOrFail($id);
        $produit->visibilite = 'visible';
        $produit->save();

        return redirect()->route('directeur.produits_incomplets')
                         ->with('success', 'Prix fixé et déclinaison créée avec succès. Stock initialisé à 0.');
    }
}