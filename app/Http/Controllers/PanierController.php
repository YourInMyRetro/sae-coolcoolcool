<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Produit;
use App\Models\StockArticle; 
use App\Models\Taille;
use App\Models\Panier;
use App\Models\LignePanier;

class PanierController extends Controller
{
    public function index()
    {
        $panier = session()->get('panier', []);
        $total = 0;

        foreach($panier as $id => &$item) {
            $total += $item['prix'] * $item['quantite'];
            
            // Récupération du stock frais pour affichage
            $stockItem = StockArticle::find($item['id_stock']);
            $item['stock_max'] = $stockItem ? $stockItem->stock : 0;
        }
        unset($item);

        return view('panier.index', compact('panier', 'total'));
    }

    public function ajouter(Request $request, $id)
    {
        $request->validate([
            'id_couleur' => 'required|exists:couleur,id_couleur',
            'id_taille' => 'required|exists:taille,id_taille',
        ]);

        $produit = Produit::with(['variantes.couleur', 'premierPrix', 'premierePhoto'])->findOrFail($id);
        
        $couleurId = $request->input('id_couleur');
        $tailleId = $request->input('id_taille');

        // 1. Trouver la variante
        $variante = $produit->variantes->where('id_couleur', $couleurId)->first();
        if (!$variante) {
            return redirect()->back()->with('error', 'Cette couleur n\'est pas disponible.');
        }

        // 2. Trouver le stock et VÉRIFIER
        $stockItem = StockArticle::where('id_produit_couleur', $variante->id_produit_couleur)
                             ->where('id_taille', $tailleId)
                             ->first();

        if (!$stockItem || $stockItem->stock <= 0) {
            return redirect()->back()->with('error', 'Produit en rupture de stock.');
        }

        // Infos pour le panier
        $nomCouleur = ucfirst($variante->couleur->type_couleur);
        $tailleObj = Taille::find($tailleId);
        $nomTaille = $tailleObj ? strtoupper($tailleObj->type_taille) : 'N/A';

        $panierId = $produit->id_produit . '-' . $couleurId . '-' . $tailleId;
        $panier = session()->get('panier', []);

        // 3. Vérifier si l'ajout ne dépasse pas le stock total
        $qteActuelle = isset($panier[$panierId]) ? $panier[$panierId]['quantite'] : 0;
        
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
                "id_stock" => $stockItem->id_stock_article // INDISPENSABLE pour la commande
            ];
        }

        session()->put('panier', $panier);

        // Synchro BDD si connecté
        if (Auth::check()) {
            $this->syncPanierBdd(Auth::user(), $stockItem->id_stock_article, $panier[$panierId]['quantite']);
        }

        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function update(Request $request, $id)
    {
        $panier = session()->get('panier');

        if($request->quantite && isset($panier[$id])) {
            $idStock = $panier[$id]['id_stock'];
            $stockReel = StockArticle::find($idStock);

            if (!$stockReel) {
                return redirect()->back()->with('error', 'Produit introuvable.');
            }

            // VÉRIFICATION DYNAMIQUE
            if ($request->quantite > $stockReel->stock) {
                $panier[$id]['quantite'] = $stockReel->stock; 
                session()->put('panier', $panier);
                
                if (Auth::check()) {
                    $this->syncPanierBdd(Auth::user(), $idStock, $stockReel->stock);
                }

                return redirect()->back()->with('error', "Stock insuffisant ! Quantité ajustée au max ({$stockReel->stock}).");
            }

            $panier[$id]['quantite'] = $request->quantite;
            session()->put('panier', $panier);

            if (Auth::check()) {
                $this->syncPanierBdd(Auth::user(), $idStock, $request->quantite);
            }

            return redirect()->back()->with('success', 'Quantité mise à jour !');
        }
        
        return redirect()->back();
    }

    public function supprimer($id)
    {
        $panier = session()->get('panier');
        
        if(isset($panier[$id])) {
            if (Auth::check()) {
                $idStock = $panier[$id]['id_stock'];
                $dbPanier = Panier::where('id_utilisateur', Auth::id())->first();
                if ($dbPanier) {
                    LignePanier::where('id_panier', $dbPanier->id_panier)
                               ->where('id_stock_article', $idStock)
                               ->delete();
                }
            }
            unset($panier[$id]);
            session()->put('panier', $panier);
        }
        return redirect()->back();
    }

    public function vider()
    {
        if (Auth::check()) {
            $dbPanier = Panier::where('id_utilisateur', Auth::id())->first();
            if ($dbPanier) {
                LignePanier::where('id_panier', $dbPanier->id_panier)->delete();
            }
        }
        session()->forget('panier');
        return redirect()->route('produits.index');
    }

    // Helper pour éviter la duplication de code BDD
    private function syncPanierBdd($user, $idStock, $qty) {
        $dbPanier = Panier::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['date_creation' => Carbon::now(), 'date_modification' => Carbon::now()]
        );
        $dbPanier->date_modification = Carbon::now();
        $dbPanier->save();

        $ligne = LignePanier::where('id_panier', $dbPanier->id_panier)
                            ->where('id_stock_article', $idStock)
                            ->first();

        if ($ligne) {
            $ligne->quantite = $qty;
            $ligne->save();
        } else {
            LignePanier::create([
                'id_panier' => $dbPanier->id_panier,
                'id_stock_article' => $idStock,
                'quantite' => $qty
            ]);
        }
    }
}