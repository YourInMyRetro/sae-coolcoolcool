<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Adresse;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\CarteBancaire;
use App\Models\Reglement;
use App\Models\StockArticle;
use Carbon\Carbon;

class CommandeController extends Controller
{
    // ------------------------------------------------------------------
    // ÉTAPE 1 : CHOIX DU MODE DE LIVRAISON (ADRESSE + MODE)
    // ------------------------------------------------------------------
    public function livraison()
    {
        $panier = session()->get('panier');
        if(!$panier || count($panier) == 0) {
            return redirect()->route('panier.index');
        }

        $userId = Auth::id();

        $adresses = DB::table('adresse')
            ->join('possedeadresse', 'adresse.id_adresse', '=', 'possedeadresse.id_adresse')
            ->where('possedeadresse.id_utilisateur', $userId)
            ->select('adresse.*')
            ->get();

        return view('commande.livraison', compact('adresses'));
    }

    // ------------------------------------------------------------------
    // TRAITEMENT DE L'ADRESSE ET DU MODE
    // ------------------------------------------------------------------
    public function validerLivraison(Request $request)
    {
        $userId = Auth::id();

        // 1. Gestion de l'adresse
        if ($request->filled('id_adresse_existante')) {
            $idAdresse = $request->id_adresse_existante;
        } 
        else {
            $request->validate([
                'rue' => 'required',
                'ville_adresse' => 'required',
                'code_postal_adresse' => 'required',
                'pays_adresse' => 'required',
            ]);

            $adresse = Adresse::create([
                'rue' => $request->rue,
                'code_postal_adresse' => $request->code_postal_adresse,
                'ville_adresse' => $request->ville_adresse,
                'pays_adresse' => $request->pays_adresse,
                'type_adresse' => 'Livraison'
            ]);
            
            DB::table('possedeadresse')->insert([
                'id_adresse' => $adresse->id_adresse,
                'id_utilisateur' => $userId
            ]);
            
            $idAdresse = $adresse->id_adresse;
        }

        // 2. Sauvegarde du mode de transport en session
        $modeLivraison = $request->input('mode_livraison', 'Standard'); 
        session()->put('type_livraison_choisi', $modeLivraison);

        session()->put('id_adresse_livraison', $idAdresse);
        
        return redirect()->route('commande.paiement');
    }

    // ------------------------------------------------------------------
    // ÉTAPE 2 : PAGE DE PAIEMENT (C'était celle qui manquait !)
    // ------------------------------------------------------------------
    public function paiement()
    {
        if (!session()->has('id_adresse_livraison')) {
            return redirect()->route('commande.livraison');
        }

        $userId = Auth::id();
        $cartes = CarteBancaire::where('id_utilisateur', $userId)->get();
        
        $panier = session()->get('panier', []);
        
        // Calcul du total produits
        $totalProduits = 0;
        foreach($panier as $item) {
            $totalProduits += $item['prix'] * $item['quantite'];
        }

        // Ajout des frais de port pour l'affichage
        $typeLivraison = session()->get('type_livraison_choisi', 'Standard');
        $fraisPort = match($typeLivraison) {
            'Express' => 14.90,
            'Relais' => 3.50,
            default => 5.00,
        };

        // Le total affiché inclut maintenant les frais
        $total = $totalProduits + $fraisPort;

        return view('commande.paiement', compact('cartes', 'total', 'fraisPort'));
    }

    // ------------------------------------------------------------------
    // TRAITEMENT DU PAIEMENT ET CRÉATION DE LA COMMANDE
    // ------------------------------------------------------------------
    public function processPaiement(Request $request)
    {
        $userId = Auth::id();
        $panier = session()->get('panier');
        $idAdresse = session()->get('id_adresse_livraison');
        $typeLivraison = session()->get('type_livraison_choisi', 'Standard');
        
        // --- 0. VERIFICATION ULTIME DU STOCK ---
        foreach($panier as $item) {
            $stockReel = StockArticle::find($item['id_stock']);
            if (!$stockReel || $stockReel->stock < $item['quantite']) {
                return redirect()->route('panier.index')
                    ->with('error', "Attention ! Le stock pour " . $item['nom'] . " a changé ou est épuisé.");
            }
        }

        // Calcul du total
        $totalProduits = 0;
        foreach($panier as $item) {
            $totalProduits += $item['prix'] * $item['quantite'];
        }

        // Logique de prix selon le mode
        $fraisPort = match($typeLivraison) {
            'Express' => 14.90,
            'Relais' => 3.50,
            default => 5.00,
        };

        $montantTotal = $totalProduits + $fraisPort;

        // === 1. GESTION DE LA CARTE BANCAIRE ===
        $idCb = $request->input('use_saved_card');

        if (!$idCb) {
            $dateExpiration = '2030-01-01'; 
            if ($request->filled('expiration')) {
                $parts = explode('/', $request->input('expiration'));
                if (count($parts) == 2) {
                    $mois = trim($parts[0]);
                    $annee = '20' . trim($parts[1]); 
                    if(checkdate($mois, 01, $annee)) {
                        $dateExpiration = "$annee-$mois-01";
                    }
                }
            }

            $carte = CarteBancaire::create([
                'id_utilisateur' => $userId,
                'numero_chiffre' => $request->card_number ?? '0000',
                'ccv_chiffre'    => $request->input('ccv') ?? '000',
                'expiration'     => $dateExpiration
            ]);
            $idCb = $carte->id_cb;
        }

        // === 2. CRÉATION DE LA COMMANDE ===
        $commande = Commande::create([
            'id_adresse'       => $idAdresse,
            'id_utilisateur'   => $userId,
            'date_commande'    => now(),
            'montant_total'    => $montantTotal,
            'frais_livraison'  => $fraisPort,
            'taxes_livraison'  => 0.00,
            'statut_livraison' => 'Validée',
            'type_livraison'   => $typeLivraison
        ]);

        // === 3. CRÉATION DES LIGNES ET MISE À JOUR STOCK ===
        foreach($panier as $key => $item) {
            $ligne = LigneCommande::create([
                'id_commande'           => $commande->id_commande,
                'quantite_commande'     => $item['quantite'],
                'prix_unitaire_negocie' => $item['prix']
            ]);

            DB::table('estplacee')->insert([
                'id_stock_article'  => $item['id_stock'],
                'id_ligne_commande' => $ligne->id_ligne_commande
            ]);

            $stockArticle = StockArticle::find($item['id_stock']);
            if ($stockArticle) {
                $stockArticle->decrement('stock', $item['quantite']);
            }
        }

        // === 4. ENREGISTREMENT DU RÈGLEMENT ===
        Reglement::create([
            'id_cb'             => $idCb,
            'id_commande'       => $commande->id_commande,
            'date_reglement'    => now(),
            'montant_reglement' => $montantTotal,
            'mode_reglement'    => 'Carte Bancaire'
        ]);

        // === 5. NETTOYAGE ===
        $dbPanier = \App\Models\Panier::where('id_utilisateur', $userId)->first();
        if ($dbPanier) {
            \App\Models\LignePanier::where('id_panier', $dbPanier->id_panier)->delete();
        }

        session()->forget(['panier', 'id_adresse_livraison', 'type_livraison_choisi']);

        return redirect()->route('commande.succes');
    }

    public function succes()
    {
        return view('commande.succes');
    }
}