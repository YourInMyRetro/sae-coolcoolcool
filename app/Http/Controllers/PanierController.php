<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // AJOUT INDISPENSABLE
use Carbon\Carbon; // Pour les dates
use App\Models\Produit;
use App\Models\StockArticle; 
use App\Models\Taille;
use App\Models\Panier;      // AJOUT
use App\Models\LignePanier; // AJOUT

class PanierController extends Controller
{
    public function index()
    {
        $panier = session()->get('panier', []);
        $total = 0;

        // On parcourt le panier pour recalculer le total ET récupérer le stock frais
        foreach($panier as $id => &$item) { // Le '&' permet de modifier l'item directement
            $total += $item['prix'] * $item['quantite'];
            
            // On va chercher le stock frais en BDD
            $stockItem = StockArticle::find($item['id_stock']);
            // On ajoute une clé 'stock_max' à notre tableau (juste pour l'affichage)
            $item['stock_max'] = $stockItem ? $stockItem->stock : 0;
        }
        // On pense à enlever la référence pour éviter des bugs bizarres PHP
        unset($item);

        return view('panier.index', compact('panier', 'total'));
    }

    public function ajouter(Request $request, $id)
    {
        // Validation basique
        $request->validate([
            'id_couleur' => 'required|exists:couleur,id_couleur',
            'id_taille' => 'required|exists:taille,id_taille',
        ]);

        $produit = Produit::with(['variantes.couleur', 'premierPrix', 'premierePhoto'])->findOrFail($id);
        
        $couleurId = $request->input('id_couleur');
        $tailleId = $request->input('id_taille');

        // 1. Trouver la variante (Produit + Couleur)
        $variante = $produit->variantes->where('id_couleur', $couleurId)->first();

        if (!$variante) {
            return redirect()->back()->with('error', 'Cette couleur n\'est pas disponible.');
        }

        // 2. Trouver le stock précis (Variante + Taille)
        $stockItem = StockArticle::where('id_produit_couleur', $variante->id_produit_couleur)
                             ->where('id_taille', $tailleId)
                             ->first();

        // --- CORRECTION ICI : 'stock' au lieu de 'quantite_stock' ---
        if (!$stockItem || $stockItem->stock <= 0) {
            return redirect()->back()->with('error', 'Désolé, ce produit est en rupture de stock pour cette combinaison.');
        }

        // Récupération des noms pour l'affichage
        $nomCouleur = ucfirst($variante->couleur->type_couleur);
        $tailleObj = Taille::find($tailleId);
        $nomTaille = $tailleObj ? strtoupper($tailleObj->type_taille) : 'N/A';

        // 3. Clé unique dans le panier
        $panierId = $produit->id_produit . '-' . $couleurId . '-' . $tailleId;

        $panier = session()->get('panier', []);

        // Vérification quantité panier vs stock
        $qteActuelle = isset($panier[$panierId]) ? $panier[$panierId]['quantite'] : 0;
        
        // --- CORRECTION ICI : 'stock' au lieu de 'quantite_stock' ---
        if ($qteActuelle + 1 > $stockItem->stock) {
             return redirect()->back()->with('error', 'Stock insuffisant pour ajouter plus d\'articles.');
        }

        if(isset($panier[$panierId])) {
            $panier[$panierId]['quantite']++;
        } else {
            $panier[$panierId] = [
                "nom" => $produit->nom_produit . " (" . $nomCouleur . " - " . $nomTaille . ")",
                "quantite" => 1,
                "prix" => $produit->premierPrix->prix_total ?? 0,
                "photo" => $produit->premierePhoto->url_photo ?? 'img/placeholder.jpg',
                "id_stock" => $stockItem->id_stock_article 
            ];
        }

        session()->put('panier', $panier);

        // ====================================================
        // AJOUT : PERSISTANCE EN BDD (Si connecté)
        // ====================================================
        if (Auth::check()) {
            $user = Auth::user();
            
            // 1. Trouver ou Créer le panier de l'utilisateur
            $dbPanier = Panier::firstOrCreate(
                ['id_utilisateur' => $user->id_utilisateur],
                ['date_creation' => Carbon::now(), 'date_modification' => Carbon::now()]
            );

            // Mise à jour de la date
            $dbPanier->date_modification = Carbon::now();
            $dbPanier->save();

            // 2. Gestion de la ligne panier
            $ligne = LignePanier::where('id_panier', $dbPanier->id_panier)
                                ->where('id_stock_article', $stockItem->id_stock_article)
                                ->first();

            if ($ligne) {
                // Si existe déjà, on met à jour la quantité avec celle de la session
                $ligne->quantite = $panier[$panierId]['quantite'];
                $ligne->save();
            } else {
                // Sinon on crée
                LignePanier::create([
                    'id_panier' => $dbPanier->id_panier,
                    'id_stock_article' => $stockItem->id_stock_article,
                    'quantite' => 1
                ]);
            }
        }
        // ====================================================

        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function supprimer($id)
    {
        $panier = session()->get('panier');
        
        if(isset($panier[$id])) {
            
            // ====================================================
            // AJOUT : SUPPRESSION EN BDD AVANT DE SUPPRIMER LA SESSION
            // ====================================================
            if (Auth::check()) {
                $idStock = $panier[$id]['id_stock']; // On récupère l'ID Stock avant delete
                $user = Auth::user();
                $dbPanier = Panier::where('id_utilisateur', $user->id_utilisateur)->first();

                if ($dbPanier) {
                    LignePanier::where('id_panier', $dbPanier->id_panier)
                               ->where('id_stock_article', $idStock)
                               ->delete();
                }
            }
            // ====================================================

            unset($panier[$id]);
            session()->put('panier', $panier);
        }
        return redirect()->back();
    }

    public function vider()
    {
        // ====================================================
        // AJOUT : VIDER LA BDD
        // ====================================================
        if (Auth::check()) {
            $user = Auth::user();
            $dbPanier = Panier::where('id_utilisateur', $user->id_utilisateur)->first();
            if ($dbPanier) {
                // On supprime toutes les lignes liées à ce panier
                LignePanier::where('id_panier', $dbPanier->id_panier)->delete();
                // Optionnel : On peut aussi supprimer le panier lui-même, ou le garder vide
            }
        }
        // ====================================================

        session()->forget('panier');
        return redirect()->route('produits.index');
    }

    public function update(Request $request, $id)
    {
        $panier = session()->get('panier');

        if($request->quantite && isset($panier[$id])) {
            // 1. Récupérer l'ID du stock stocké dans la session
            $idStock = $panier[$id]['id_stock'];

            // 2. Vérifier le stock réel en BDD
            $stockReel = StockArticle::find($idStock);

            if (!$stockReel) {
                return redirect()->back()->with('error', 'Produit introuvable.');
            }

            // 3. Comparer la demande avec le stock disponible
            if ($request->quantite > $stockReel->stock) {
                // On met la quantité au maximum dispo pour être sympa
                $panier[$id]['quantite'] = $stockReel->stock; 
                session()->put('panier', $panier);
                
                // Mettre à jour BDD aussi ici si connecté (cas limite)
                $this->updateDbQuantity($idStock, $stockReel->stock);

                return redirect()->back()->with('error', "Stock insuffisant ! Nous avons ajusté la quantité au maximum disponible ({$stockReel->stock}).");
            }

            // 4. Si tout est bon, on met à jour la session
            $panier[$id]['quantite'] = $request->quantite;
            session()->put('panier', $panier);

            // ====================================================
            // AJOUT : MISE À JOUR BDD
            // ====================================================
            $this->updateDbQuantity($idStock, $request->quantite);
            // ====================================================

            return redirect()->back()->with('success', 'Quantité mise à jour !');
        }
        
        return redirect()->back();
    }

    // Petite fonction privée pour éviter de répéter le code dans update()
    private function updateDbQuantity($idStock, $qty) {
        if (Auth::check()) {
            $user = Auth::user();
            $dbPanier = Panier::where('id_utilisateur', $user->id_utilisateur)->first();
            if ($dbPanier) {
                LignePanier::where('id_panier', $dbPanier->id_panier)
                           ->where('id_stock_article', $idStock)
                           ->update(['quantite' => $qty]);
            }
        }
    }
}