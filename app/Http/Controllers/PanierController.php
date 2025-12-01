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

    public function ajouter(Request $request, $id)
    {
        // 1. On récupère le produit avec ses variantes (couleurs)
        $produit = Produit::with(['variantes.couleur', 'premierPrix', 'premierePhoto'])->findOrFail($id);
        
        // 2. Gestion de la couleur
        // Si l'utilisateur a choisi une couleur via le formulaire (ID 10)
        $couleurId = $request->input('id_couleur');
        $nomCouleur = '';

        if ($couleurId) {
            // On cherche le nom de la couleur choisie pour l'afficher dans le panier
            $varianteChoisie = $produit->variantes->where('id_couleur', $couleurId)->first(); // On cherche dans les variantes chargées
            if ($varianteChoisie && $varianteChoisie->couleur) {
                $nomCouleur = ' - ' . ucfirst($varianteChoisie->couleur->type_couleur);
            }
        }

        // 3. Création d'un ID unique pour le panier
        // Astuce : Si on prend le maillot (ID 1) en Rouge (Color 5), l'ID dans le panier sera "1-5"
        // Ça permet d'avoir deux lignes différentes pour le même maillot en deux couleurs.
        $panierId = $produit->id_produit . ($couleurId ? '-' . $couleurId : '');

        $panier = session()->get('panier', []);

        if(isset($panier[$panierId])) {
            $panier[$panierId]['quantite']++;
        } else {
            $panier[$panierId] = [
                "nom" => $produit->nom_produit . $nomCouleur, // Ex: "Maillot France - Bleu"
                "quantite" => 1,
                "prix" => $produit->premierPrix->prix_total ?? 0,
                "photo" => $produit->premierePhoto->url_photo ?? 'img/placeholder.jpg'
            ];
        }

        session()->put('panier', $panier);
        return redirect()->back()->with('success', 'Produit ajouté !');
    }
}