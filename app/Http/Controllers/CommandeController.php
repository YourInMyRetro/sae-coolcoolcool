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
use App\Models\Produit;
use Carbon\Carbon;

class CommandeController extends Controller
{
    // ------------------------------------------------------------------
    // ÉTAPE 1 : CHOIX DU MODE DE LIVRAISON (ADRESSE)
    // ------------------------------------------------------------------
    public function livraison()
    {
        // 1. Vérification du panier
        $panier = session()->get('panier');
        if(!$panier || count($panier) == 0) {
            return redirect()->route('panier.index');
        }

        $userId = Auth::id();

        // 2. Récupération des adresses via la table de liaison 'possedeadresse'
        // On fait une jointure pour récupérer les infos de la table 'adresse'
        $adresses = DB::table('adresse')
            ->join('possedeadresse', 'adresse.id_adresse', '=', 'possedeadresse.id_adresse')
            ->where('possedeadresse.id_utilisateur', $userId)
            ->select('adresse.*')
            ->get();

        return view('commande.livraison', compact('adresses'));
    }

    // ------------------------------------------------------------------
    // TRAITEMENT DE L'ADRESSE (Création ou Sélection)
    // ------------------------------------------------------------------
    public function validerLivraison(Request $request)
    {
        $userId = Auth::id();

        // Cas A : L'utilisateur a choisi une adresse existante
        if ($request->filled('id_adresse_existante')) {
            $idAdresse = $request->id_adresse_existante;
        } 
        // Cas B : L'utilisateur crée une nouvelle adresse
        else {
            $request->validate([
                'rue' => 'required',
                'ville_adresse' => 'required',
                'code_postal_adresse' => 'required',
                'pays_adresse' => 'required',
            ]);

            // 1. Insertion dans la table 'adresse'
            // On utilise create() via le Modèle Adresse configuré précédemment
            $adresse = Adresse::create([
                'rue' => $request->rue,
                'code_postal_adresse' => $request->code_postal_adresse,
                'ville_adresse' => $request->ville_adresse,
                'pays_adresse' => $request->pays_adresse,
                'type_adresse' => 'Livraison' // Valeur par défaut obligatoire selon ton CHECK SQL
            ]);
            
            // 2. Création du lien dans 'possedeadresse'
            DB::table('possedeadresse')->insert([
                'id_adresse' => $adresse->id_adresse,
                'id_utilisateur' => $userId
            ]);
            
            $idAdresse = $adresse->id_adresse;
        }

        // On sauvegarde l'ID pour l'étape suivante
        session()->put('id_adresse_livraison', $idAdresse);

        return redirect()->route('commande.paiement');
    }

    // ------------------------------------------------------------------
    // ÉTAPE 2 : PAGE DE PAIEMENT
    // ------------------------------------------------------------------
    public function paiement()
    {
        // Vérification sécurité : on doit avoir une adresse
        if (!session()->has('id_adresse_livraison')) {
            return redirect()->route('commande.livraison');
        }

        $userId = Auth::id();

        // Récupération des cartes enregistrées pour cet utilisateur
        $cartes = CarteBancaire::where('id_utilisateur', $userId)->get();
        
        // Calcul du montant total
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
        
        // Sécurité : On recalcule le total ici plutôt que de faire confiance à l'input hidden
        $totalCalcul = 0;
        foreach($panier as $item) {
            $totalCalcul += $item['prix'] * $item['quantite'];
        }
        $fraisPort = 5.00;
        $montantTotal = $totalCalcul + $fraisPort;

        // === 1. GESTION DE LA CARTE BANCAIRE ===
        $idCb = $request->input('use_saved_card');

        // Si l'utilisateur n'a pas choisi une carte existante, on en crée une
        if (!$idCb) {
            // Conversion de la date MM/YY (ex: 12/25) vers YYYY-MM-DD (ex: 2025-12-01)
            $dateExpiration = '2030-01-01'; // Valeur par défaut
            
            if ($request->filled('expiration')) {
                $parts = explode('/', $request->input('expiration'));
                if (count($parts) == 2) {
                    $mois = trim($parts[0]);
                    $annee = '20' . trim($parts[1]); // On ajoute le siècle '20'
                    // On check validité rapide
                    if(checkdate($mois, 01, $annee)) {
                        $dateExpiration = "$annee-$mois-01";
                    }
                }
            }

            // Création de la carte dans la BDD
            $carte = CarteBancaire::create([
                'id_utilisateur' => $userId,
                'numero_chiffre' => $request->card_number ?? '0000', // En production, il faut chiffrer ça !
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
            'statut_livraison' => 'Validée', // Statut imposé par le CHECK SQL
            'type_livraison'   => 'Standard'
        ]);

        // === 3. CRÉATION DES LIGNES ET LIEN STOCK ===
        foreach($panier as $key => $item) {
            // Création de la ligne de commande
            $ligne = LigneCommande::create([
                'id_commande'           => $commande->id_commande,
                'quantite_commande'     => $item['quantite'],
                'prix_unitaire_negocie' => $item['prix']
            ]);

            // --- Logique complexe pour 'estplacee' ---
            // Ta BDD demande de lier la ligne de commande à un 'id_stock_article' précis.
            // Comme le panier en session (version actuelle) n'a pas l'ID stock, on doit le trouver.
            // On déduit le produit depuis la clé du panier (ex: "12-5" => id_produit 12, couleur 5) ou juste l'ID.
            
            $parts = explode('-', $key);
            $idProduit = $parts[0];
            $idCouleur = $parts[1] ?? null; // Peut être null si pas de variante gérée

            // On cherche un article en stock correspondant
            $queryStock = DB::table('stock_article')
                ->join('produit_couleur', 'stock_article.id_produit_couleur', '=', 'produit_couleur.id_produit_couleur')
                ->where('produit_couleur.id_produit', $idProduit);

            if ($idCouleur) {
                $queryStock->where('produit_couleur.id_couleur', $idCouleur);
            }

            // On prend le premier stock disponible (par défaut Taille L ou autre si on ne gère pas la taille)
            $stockItem = $queryStock->first();

            // Si on trouve un stock, on fait le lien. Sinon, on met 1 par défaut pour éviter le crash (mode dégradé)
            $idStockFinal = $stockItem ? $stockItem->id_stock_article : 1;

            // Insertion dans la table d'association 'estplacee'
            DB::table('estplacee')->insert([
                'id_stock_article'  => $idStockFinal,
                'id_ligne_commande' => $ligne->id_ligne_commande
            ]);
        }

        // === 4. ENREGISTREMENT DU RÈGLEMENT ===
        // Ta table 'reglement' a une clé étrangère vers 'commande' (id_commande)
        Reglement::create([
            'id_cb'             => $idCb,
            'id_commande'       => $commande->id_commande,
            'date_reglement'    => now(),
            'montant_reglement' => $montantTotal,
            'mode_reglement'    => 'Carte Bancaire'
        ]);

        // === 5. FIN DU PROCESSUS ===
        session()->forget(['panier', 'id_adresse_livraison']);

        return redirect()->route('commande.succes');
    }

    // Page de succès
    public function succes()
    {
        return view('commande.succes');
    }
}