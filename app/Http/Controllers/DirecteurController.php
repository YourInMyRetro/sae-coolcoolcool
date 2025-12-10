<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Models\Couleur;
use App\Models\ProduitCouleur;
use App\Models\Taille;
use App\Models\StockArticle;
use App\Models\Commande; // On utilise le modèle Eloquent
use Carbon\Carbon;

class DirecteurController extends Controller
{
    // =================================================================
    // US 29 & 30 : DASHBOARD (VERSION PHP - INFAILLIBLE)
    // =================================================================
    public function dashboard()
    {
        // 1. RÉCUPÉRATION BRUTE (On prend tout ce qui n'est pas annulé)
        $commandes = Commande::where('statut_livraison', '!=', 'Annulée')
                             ->orderBy('date_commande', 'desc')
                             ->get();

        // 2. CALCUL CA MENSUEL (US 29) - Fait par PHP
        $ventesMensuelles = $commandes->groupBy(function($c) {
            return substr($c->date_commande, 0, 7); // Extrait "YYYY-MM"
        })->map(function ($groupe, $mois) {
            return (object) [
                'mois' => $mois,
                'total_ventes' => $groupe->sum('montant_total')
            ];
        })->take(12);

        // 3. CALCUL PAR CATÉGORIE (US 30) - SQL Simplifié + Traitement PHP
        // On récupère juste les lignes brutes avec leur catégorie
        $lignes = DB::table('ligne_commande')
            ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('estplacee', 'ligne_commande.id_ligne_commande', '=', 'estplacee.id_ligne_commande')
            ->join('stock_article', 'estplacee.id_stock_article', '=', 'stock_article.id_stock_article')
            ->join('produit_couleur', 'stock_article.id_produit_couleur', '=', 'produit_couleur.id_produit_couleur')
            ->join('produit', 'produit_couleur.id_produit', '=', 'produit.id_produit')
            ->join('categorie', 'produit.id_categorie', '=', 'categorie.id_categorie')
            ->select(
                'categorie.nom_categorie', 
                'commande.date_commande',
                DB::raw('(ligne_commande.prix_unitaire_negocie * ligne_commande.quantite_commande) as montant')
            )
            ->where('commande.statut_livraison', '!=', 'Annulée')
            ->get();

        // On groupe en PHP
        $ventesParCategorie = $lignes->groupBy(function($l) {
            return substr($l->date_commande, 0, 7) . ' - ' . $l->nom_categorie;
        })->map(function ($groupe, $key) {
            list($mois, $cat) = explode(' - ', $key);
            return (object) [
                'mois' => $mois,
                'nom_categorie' => $cat,
                'total' => $groupe->sum('montant')
            ];
        })->sortByDesc('mois');

        // Compteur pour le bouton d'action
        $nbProduitsIncomplets = Produit::whereDoesntHave('produitCouleurs')->count();

        return view('directeur.dashboard', compact('ventesMensuelles', 'ventesParCategorie', 'nbProduitsIncomplets'));
    }

    // =================================================================
    // US 40 & 54 : GESTION DES PRODUITS (AVEC STOCK INITIAL)
    // =================================================================
    public function produitsIncomplets()
    {
        $produitsSansPrix = Produit::whereDoesntHave('produitCouleurs')->get();
        $couleurs = Couleur::all();
        return view('directeur.produits_incomplets', compact('produitsSansPrix', 'couleurs'));
    }

    public function updatePrix(Request $request, $id)
    {
        $request->validate([
            'id_couleur' => 'required|exists:couleur,id_couleur',
            'prix_total' => 'required|numeric|min:0',
            'stock_initial' => 'required|integer|min:0', // <--- Gestion Stock
        ]);

        // 1. Prix
        $produitCouleur = new ProduitCouleur();
        $produitCouleur->id_produit = $id;
        $produitCouleur->id_couleur = $request->input('id_couleur');
        $produitCouleur->prix_total = $request->input('prix_total');
        $produitCouleur->save();

        // 2. Stock
        $stockQty = $request->input('stock_initial');
        $tailles = Taille::all();
        foreach($tailles as $taille) {
            $stock = new StockArticle();
            $stock->id_produit_couleur = $produitCouleur->id_produit_couleur;
            $stock->id_taille = $taille->id_taille;
            $stock->stock = $stockQty;
            $stock->save();
        }

        // 3. Visibilité
        $produit = Produit::findOrFail($id);
        $produit->visibilite = 'visible';
        $produit->save();

        return redirect()->route('directeur.produits_incomplets')
            ->with('success', "Produit validé (Prix: {$produitCouleur->prix_total}€, Stock: $stockQty)");
    }
}