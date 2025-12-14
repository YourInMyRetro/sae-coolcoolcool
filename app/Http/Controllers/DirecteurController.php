<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produit;
use App\Models\Couleur;
use App\Models\ProduitCouleur;
use App\Models\Taille;
use App\Models\StockArticle;
use App\Models\Commande;
use Carbon\Carbon;

class DirecteurController extends Controller
{
    // =================================================================
    // DASHBOARD (Inchangé)
    // =================================================================
    public function dashboard()
    {
        $commandes = Commande::where('statut_livraison', '!=', 'Annulée')
                             ->orderBy('date_commande', 'desc')
                             ->get();

        $ventesMensuelles = $commandes->groupBy(function($c) {
            return substr($c->date_commande, 0, 7); 
        })->map(function ($groupe, $mois) {
            return (object) [
                'mois' => $mois,
                'total_ventes' => $groupe->sum('montant_total')
            ];
        })->take(12);

        $lignes = DB::table('ligne_commande')
            ->join('commande', 'ligne_commande.id_commande', '=', 'commande.id_commande')
            ->join('estplacee', 'ligne_commande.id_ligne_commande', '=', 'estplacee.id_ligne_commande')
            ->join('stock_article', 'estplacee.id_stock_article', '=', 'stock_article.id_stock_article')
            ->join('produit_couleur', 'stock_article.id_produit_couleur', '=', 'produit_couleur.id_produit_couleur')
            ->join('produit', 'produit_couleur.id_produit', '=', 'produit.id_produit')
            ->join('categorie', 'produit.id_categorie', '=', 'categorie.id_categorie')
            ->select('categorie.nom_categorie', 'commande.date_commande', DB::raw('(ligne_commande.prix_unitaire_negocie * ligne_commande.quantite_commande) as montant'))
            ->where('commande.statut_livraison', '!=', 'Annulée')
            ->get();

        $ventesParCategorie = $lignes->groupBy(function($l) {
            return substr($l->date_commande, 0, 7) . ' - ' . $l->nom_categorie;
        })->map(function ($groupe, $key) {
            list($mois, $cat) = explode(' - ', $key);
            return (object) ['mois' => $mois, 'nom_categorie' => $cat, 'total' => $groupe->sum('montant')];
        })->sortByDesc('mois');

        $nbProduitsIncomplets = Produit::whereDoesntHave('produitCouleurs')->count();

        return view('directeur.dashboard', compact('ventesMensuelles', 'ventesParCategorie', 'nbProduitsIncomplets'));
    }

    // =================================================================
    // GESTION PRODUITS (MODIFIÉ : PLUS DE COULEUR)
    // =================================================================
    public function produitsIncomplets()
    {
        $produitsSansPrix = Produit::whereDoesntHave('produitCouleurs')->get();
        
        // On n'envoie plus les couleurs à la vue, on n'en a plus besoin
        return view('directeur.produits_incomplet', compact('produitsSansPrix'));
    }

    public function updatePrix(Request $request, $id)
    {
        // 1. Validation : ON NE DEMANDE QUE LE PRIX
        $request->validate([
            'prix_total' => 'required|numeric|min:0',
        ]);

        // 2. Attribution automatique d'une couleur par défaut
        // On prend la première couleur dispo en base (ex: ID 1) pour satisfaire la BDD
        $defaultCouleur = Couleur::first();
        if (!$defaultCouleur) {
            return back()->with('error', 'Aucune couleur n\'existe en base de données. Impossible de créer la déclinaison.');
        }

        // 3. Création de la déclinaison
        $produitCouleur = new ProduitCouleur();
        $produitCouleur->id_produit = $id;
        $produitCouleur->id_couleur = $defaultCouleur->id_couleur; // Automatique
        $produitCouleur->prix_total = $request->input('prix_total');
        $produitCouleur->save();

        // 4. Stock auto à 0
        $tailles = Taille::all();
        foreach($tailles as $taille) {
            $stock = new StockArticle();
            $stock->id_produit_couleur = $produitCouleur->id_produit_couleur;
            $stock->id_taille = $taille->id_taille;
            $stock->stock = 0; 
            $stock->save();
        }

        // 5. Activation produit
        $produit = Produit::findOrFail($id);
        $produit->visibilite = 'visible';
        $produit->save();

        return redirect()->route('directeur.produits_incomplets')
                         ->with('success', "Prix fixé à {$produitCouleur->prix_total}€. Produit en ligne (Couleur: {$defaultCouleur->type_couleur}).");
    }
}