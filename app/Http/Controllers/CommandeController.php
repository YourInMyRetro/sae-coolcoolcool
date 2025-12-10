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
use App\Models\StockArticle; // Import du modèle Stock
use Carbon\Carbon;

class CommandeController extends Controller
{
    // ------------------------------------------------------------------
    // ÉTAPE 1 : CHOIX DU MODE DE LIVRAISON (ADRESSE)
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
    // TRAITEMENT DE L'ADRESSE
    // ------------------------------------------------------------------
    public function validerLivraison(Request $request)
    {
        $userId = Auth::id();

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

        session()->put('id_adresse_livraison', $idAdresse);
        return redirect()->route('commande.paiement');
    }

    // ------------------------------------------------------------------
    // ÉTAPE 2 : PAGE DE PAIEMENT
    // ------------------------------------------------------------------
    public function paiement()
    {
        if (!session()->has('id_adresse_livraison')) {
            return redirect()->route('commande.livraison');
        }

        $userId = Auth::id();
        $cartes = CarteBancaire::where('id_utilisateur', $userId)->get();
        
        $panier = session()->get('panier', []);
        $total = 0;
        foreach($panier as $item) {
            $total += $item['prix'] * $item['quantite'];
        }

        return view('commande.paiement', compact('cartes', 'total'));
    }

    // ------------------------------------------------------------------
    // TRAITEMENT DU PAIEMENT ET CRÉATION DE LA COMMANDE
    // ------------------------------------------------------------------
    public function processPaiement(Request $request)
    {
        $userId = Auth::id();
        $panier = session()->get('panier');
        $idAdresse = session()->get('id_adresse_livraison');
        
        // --- 0. VERIFICATION ULTIME DU STOCK ---
        // Avant de débiter la carte, on revérifie que tout est encore dispo
        foreach($panier as $item) {
            $stockReel = StockArticle::find($item['id_stock']);
            if (!$stockReel || $stockReel->stock < $item['quantite']) {
                return redirect()->route('panier.index')
                    ->with('error', "Attention ! Le stock pour " . $item['nom'] . " a changé ou est épuisé.");
            }
        }

        // Calcul du total
        $totalCalcul = 0;
        foreach($panier as $item) {
            $totalCalcul += $item['prix'] * $item['quantite'];
        }
        $fraisPort = 5.00;
        $montantTotal = $totalCalcul + $fraisPort;

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
            'type_livraison'   => 'Standard'
        ]);

        // === 3. CRÉATION DES LIGNES ET MISE À JOUR STOCK ===
        foreach($panier as $key => $item) {
            // A. Création ligne commande
            $ligne = LigneCommande::create([
                'id_commande'           => $commande->id_commande,
                'quantite_commande'     => $item['quantite'],
                'prix_unitaire_negocie' => $item['prix']
            ]);

            // B. Lien avec le stock (table estplacee)
            // On utilise l'ID stock stocké directement dans la session par PanierController
            $idStock = $item['id_stock']; 

            DB::table('estplacee')->insert([
                'id_stock_article'  => $idStock,
                'id_ligne_commande' => $ligne->id_ligne_commande
            ]);

            // C. --- DIMINUTION DU STOCK (Le point crucial) ---
            // On récupère l'article de stock et on décrémente
            $stockArticle = StockArticle::find($idStock);
            if ($stockArticle) {
                // decrement() est une méthode Laravel qui fait "stock = stock - X" de manière sécurisée
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
        // Si l'utilisateur a un panier en base de données, on le vide aussi
        $dbPanier = \App\Models\Panier::where('id_utilisateur', $userId)->first();
        if ($dbPanier) {
            \App\Models\LignePanier::where('id_panier', $dbPanier->id_panier)->delete();
        }

        session()->forget(['panier', 'id_adresse_livraison']);

        return redirect()->route('commande.succes');
    }

    public function succes()
    {
        return view('commande.succes');
    }
}