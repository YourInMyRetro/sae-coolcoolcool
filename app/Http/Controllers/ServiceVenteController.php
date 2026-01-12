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
use App\Models\Candidat;
use App\Models\Competition;

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
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'prix' => 'nullable|numeric|min:0',
            'variantes' => 'required|array',
            'variantes.*.id_couleur' => 'required|exists:couleur,id_couleur',
            'variantes.*.id_taille' => 'required|exists:taille,id_taille',
            'variantes.*.quantite' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {

            $produit = new Produit();
            $produit->nom_produit = $request->nom_produit;
            $produit->description_produit = $request->description_produit;
            $produit->id_categorie = $request->id_categorie;
            

            $prixSaisi = $request->filled('prix') ? $request->prix : 0;
            $produit->visibilite = ($prixSaisi > 0) ? 'visible' : 'cache';
            $produit->save();
            

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $image) {
                    $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('img/produits'), $fileName);

                    $photo = new PhotoProduit();
                    $photo->id_produit = $produit->id_produit;
                    $photo->url_photo = 'img/produits/' . $fileName;
                    $photo->save();
                }
            }

            $groupesCouleurs = [];
            foreach ($request->variantes as $variante) {
                $idCouleur = $variante['id_couleur'];
                if (!isset($groupesCouleurs[$idCouleur])) {
                    $groupesCouleurs[$idCouleur] = [];
                }
                $groupesCouleurs[$idCouleur][] = $variante;
            }

            foreach ($groupesCouleurs as $idCouleur => $lignes) {
                $pc = new ProduitCouleur();
                $pc->id_produit = $produit->id_produit;
                $pc->id_couleur = $idCouleur;
                $pc->prix_total = $prixSaisi; 
                $pc->save();

                foreach ($lignes as $ligne) {
                    $stock = new StockArticle();
                    $stock->id_produit_couleur = $pc->id_produit_couleur;
                    $stock->id_taille = $ligne['id_taille'];
                    $stock->stock = $ligne['quantite'];
                    $stock->save();
                }
            }

            DB::commit();

            if ($prixSaisi > 0) {
                return redirect()->route('vente.produits.list')->with('success', 'Produit créé, prix fixé et mis en ligne !');
            } else {
                return redirect()->route('vente.produits.list')->with('success', 'Stocks enregistrés. Produit envoyé au Directeur pour fixation du prix.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
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

    public function addPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        $produit = Produit::findOrFail($id);

        $image = $request->file('photo');
        $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
        $image->move(public_path('img/produits'), $fileName);

        $photo = new PhotoProduit();
        $photo->id_produit = $produit->id_produit;
        $photo->url_photo = 'img/produits/' . $fileName;
        $photo->save();

        return back()->with('success', 'Photo ajoutée avec succès !');
    }

    public function deletePhoto($id)
    {
        $photo = PhotoProduit::findOrFail($id);
        $produitId = $photo->id_produit;

        $count = PhotoProduit::where('id_produit', $produitId)->count();

        if ($count <= 1) {
            return back()->with('error', 'Impossible de supprimer : le produit doit avoir au moins une photo.');
        }

        if (file_exists(public_path($photo->url_photo))) {
            unlink(public_path($photo->url_photo));
        }

        $photo->delete();

        return back()->with('success', 'Photo supprimée.');
    }
public function editCandidats($id)
    {
        $competition = Competition::findOrFail($id);
        
        $tousLesJoueurs = Candidat::orderBy('nom_joueur')->get();
        
        $selectedJoueurs = $competition->candidats->pluck('idjoueur')->toArray();

        return view('vente.votation_candidats', compact('competition', 'tousLesJoueurs', 'selectedJoueurs'));
    }

    public function updateCandidats(Request $request, $id)
    {
        $competition = Competition::findOrFail($id);
        
        $competition->candidats()->sync($request->joueurs ?? []);

        return redirect()->route('vente.votation.list')->with('success', 'Liste des candidats mise à jour avec succès !');
    }

}