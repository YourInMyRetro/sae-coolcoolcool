<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\StockArticle; 
use App\Models\Taille;

class PanierController extends Controller
{
    public function index()
    {
        $panier = session()->get('panier', []);
        $total = 0;
        foreach($panier as $item) {
            $total += $item['prix'] * $item['quantite'];
        }
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
        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function supprimer($id)
    {
        $panier = session()->get('panier');
        if(isset($panier[$id])) {
            unset($panier[$id]);
            session()->put('panier', $panier);
        }
        return redirect()->back();
    }

    public function vider()
    {
        session()->forget('panier');
        return redirect()->route('produits.index');
    }

    public function update(Request $request, $id)
    {
        $panier = session()->get('panier');

        if($request->quantite && isset($panier[$id])) {
            $panier[$id]['quantite'] = $request->quantite;
            session()->put('panier', $panier);
            return redirect()->back()->with('success', 'Quantité mise à jour !');
        }
        
        return redirect()->back();
    }
}