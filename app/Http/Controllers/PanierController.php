<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;

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

    public function ajouter($id)
    {
        $produit = Produit::with('premierPrix', 'premierePhoto')->findOrFail($id);
        $panier = session()->get('panier', []);

        if(isset($panier[$id])) {
            $panier[$id]['quantite']++;
        } else {
            $panier[$id] = [
                "nom" => $produit->nom_produit,
                "quantite" => 1,
                "prix" => $produit->premierPrix->prix_total ?? 0,
                "photo" => $produit->premierePhoto->url_photo ?? 'img/placeholder.jpg'
            ];
        }

        session()->put('panier', $panier);
        return redirect()->back()->with('success', 'Produit ajouté !');
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
}