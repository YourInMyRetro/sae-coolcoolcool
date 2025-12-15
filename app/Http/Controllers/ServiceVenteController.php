<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\Couleur;
use App\Models\Taille;
use App\Models\Produit;
use App\Models\ProduitCouleur;
use App\Models\StockArticle;
use App\Models\PhotoProduit;
use Illuminate\Support\Facades\DB;

class ServiceVenteController extends Controller
{
    public function index()
    {
        return view('vente.dashboard');
    }

    public function createCategorie()
    {
        $categories = Categorie::all();
        return view('vente.categorie_create', compact('categories'));
    }

    public function storeCategorie(Request $request)
    {
        $request->validate(['nom_categorie' => 'required|unique:categorie,nom_categorie|max:50']);

        $cat = new Categorie();
        $cat->nom_categorie = $request->nom_categorie;
        $cat->save();

        return redirect()->route('vente.categorie.create')->with('success', 'Catégorie créée avec succès !');
    }

    public function createProduit()
    {
        $categories = Categorie::orderBy('nom_categorie')->get();
        $couleurs = Couleur::orderBy('type_couleur')->get();
        $tailles = Taille::orderBy('id_taille')->get(); 

        return view('vente.produit_create', compact('categories', 'couleurs', 'tailles'));
    }

    public function storeProduit(Request $request)
    {
        $request->validate([
            'nom_produit' => 'required|max:255',
            'id_categorie' => 'required|exists:categorie,id_categorie',
            'prix' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048', 
        ]);

        DB::beginTransaction();

        try {
            $produit = new Produit();
            $produit->nom_produit = $request->nom_produit;
            $produit->description_produit = $request->description_produit;
            $produit->id_categorie = $request->id_categorie;
            $produit->visibilite = 'cache'; 
            $produit->save();
            
            if ($request->has('id_couleur')) {
                $pc = new ProduitCouleur();
                $pc->id_produit = $produit->id_produit;
                $pc->id_couleur = $request->id_couleur;
                $pc->prix_total = $request->prix;
                $pc->save();

                if ($request->has('id_taille')) {
                    $stock = new StockArticle();
                    $stock->id_produit_couleur = $pc->id_produit_couleur;
                    $stock->id_taille = $request->id_taille;
                    $stock->stock = $request->stock ?? 0;
                    $stock->save();
                }
            }

            if ($request->hasFile('photo')) {
                $fileName = time() . '_' . $request->file('photo')->getClientOriginalName();
                $request->file('photo')->move(public_path('img/produits'), $fileName);

                $photo = new PhotoProduit();
                $photo->id_produit = $produit->id_produit;
                $photo->url_photo = 'img/produits/' . $fileName;
                $photo->save();
            }

            DB::commit();
            return redirect()->route('vente.produits.list')->with('success', 'Produit créé ! Vous pouvez maintenant le rendre visible.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    public function listProduits()
    {
        $produits = Produit::with('categorie')->orderBy('id_produit', 'desc')->get();
        return view('vente.produits_list', compact('produits'));
    }

    public function toggleVisibilite($id)
    {
        $produit = Produit::findOrFail($id);
        $produit->visibilite = ($produit->visibilite === 'visible') ? 'cache' : 'visible';
        $produit->save();

        return back()->with('success', 'Visibilité mise à jour pour ' . $produit->nom_produit);
    }
}