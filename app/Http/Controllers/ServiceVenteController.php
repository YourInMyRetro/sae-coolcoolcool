<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\Produit;
use App\Models\ProduitCouleur;
use App\Models\Taille;
use App\Models\PhotoProduit;
use App\Models\StockArticle;
use App\Models\VoteTheme;
use App\Models\Candidat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ServiceVenteController extends Controller
{
    public function index()
    {
        return view('vente.dashboard');
    }

    public function createCategorie()
    {
        return view('vente.categorie_create');
    }

    public function storeCategorie(Request $request)
    {
        $request->validate([
            'nom_categorie' => 'required|string|max:255',
        ]);

        $categorie = new Categorie();
        $categorie->nom_categorie = $request->nom_categorie;
        $categorie->save();

        return redirect()->route('vente.dashboard')->with('success', 'Catégorie créée avec succès !');
    }

    public function createProduit()
    {
        $categories = Categorie::all();
        $couleurs = \App\Models\Couleur::all();
        $tailles = \App\Models\Taille::all();
        
        return view('vente.produit_create', compact('categories', 'couleurs', 'tailles'));
    }

    public function storeProduit(Request $request)
    {
        $request->validate([
            'nom_produit' => 'required|string|max:255',
            'id_categorie' => 'required|exists:categorie,id_categorie',
            'description' => 'nullable|string',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variantes' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $produit = new Produit();
            $produit->nom_produit = $request->nom_produit;
            $produit->id_categorie = $request->id_categorie;
            $produit->description = $request->description;
            $produit->visibilite = 'non_visible'; 
            $produit->save();

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $image) {
                    $cleanName = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME));
                    $fileName = time() . '_' . uniqid() . '_' . $cleanName . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('img/produits'), $fileName);

                    $photo = new PhotoProduit();
                    $photo->id_produit = $produit->id_produit;
                    $photo->url_photo = 'img/produits/' . $fileName;
                    $photo->save();
                }
            }

            foreach ($request->variantes as $index => $variante) {
                if (!isset($variante['couleur'])) continue;

                $couleur = \App\Models\Couleur::firstOrCreate(['nom_couleur' => $variante['couleur']]);

                $produitCouleur = new ProduitCouleur();
                $produitCouleur->id_produit = $produit->id_produit;
                $produitCouleur->id_couleur = $couleur->id_couleur;
                $produitCouleur->photo_produit = 'img/placeholder.jpg'; 
                $produitCouleur->prix_total = $variante['prix'];
                $produitCouleur->save();

                foreach ($variante['tailles'] as $tailleNom => $stock) {
                    if ($stock !== null && $stock !== '') {
                        $taille = \App\Models\Taille::firstOrCreate(['nom_taille' => $tailleNom]);
                        
                        $stockArticle = new StockArticle();
                        $stockArticle->id_produit_couleur = $produitCouleur->id_produit_couleur;
                        $stockArticle->id_taille = $taille->id_taille;
                        $stockArticle->stock_qte = $stock;
                        $stockArticle->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('vente.produits.list')->with('success', 'Produit créé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur : ' . $e->getMessage())->withInput();
        }
    }

    public function listProduits()
    {
        $produits = Produit::with(['categorie', 'photos', 'premierPrix'])->orderBy('id_produit', 'desc')->get();
        return view('vente.produits_list', compact('produits'));
    }

    public function toggleVisibilite($id)
    {
        $produit = Produit::findOrFail($id);
        $produit->visibilite = ($produit->visibilite === 'visible') ? 'non_visible' : 'visible';
        $produit->save();
        return back()->with('success', 'Visibilité mise à jour.');
    }

    public function addPhoto(Request $request, $id)
    {
        $request->validate(['photo' => 'required|image|max:2048']);
        $produit = Produit::findOrFail($id);
        
        $image = $request->file('photo');
        $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
        $image->move(public_path('img/produits'), $fileName);

        $photo = new PhotoProduit();
        $photo->id_produit = $produit->id_produit;
        $photo->url_photo = 'img/produits/' . $fileName;
        $photo->save();

        return back()->with('success', 'Photo ajoutée.');
    }

    public function deletePhoto($id)
    {
        $photo = PhotoProduit::findOrFail($id);
        // On permet la suppression même s'il ne reste qu'une photo (choix UX)
        if (file_exists(public_path($photo->url_photo))) {
            unlink(public_path($photo->url_photo));
        }
        $photo->delete();
        return back()->with('success', 'Photo supprimée.');
    }

    // --- VOTATIONS ---

    public function listVotations()
    {
        $competitions = VoteTheme::orderBy('date_ouverture', 'desc')->get();
        return view('vente.votation_list', compact('competitions'));
    }

    public function createVotation()
    {
        return view('vente.votation_create');
    }

    public function storeVotation(Request $request)
    {
        $request->validate([
            'nom_theme' => 'required|string|max:255|unique:vote_theme,nom_theme',
            'date_fermeture' => 'required|date|after:today'
        ]);

        $votation = new VoteTheme();
        $votation->nom_theme = $request->nom_theme;
        $votation->date_ouverture = Carbon::now();
        $votation->date_fermeture = $request->date_fermeture;
        $votation->save();

        return redirect()->route('vente.votation.list')->with('success', 'Votation créée avec succès !');
    }

    public function toggleStatutVotation($id)
    {
        $vote = VoteTheme::findOrFail($id);
        
        if ($vote->date_fermeture > Carbon::now()) {
            $vote->date_fermeture = Carbon::now()->subDay();
        } else {
            $vote->date_fermeture = Carbon::now()->addDays(15);
        }
        $vote->save();

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroyVotation($id)
    {
        $vote = VoteTheme::findOrFail($id);
        // Nettoyage manuel des relations si pas de cascade SQL
        DB::table('vote_candidat')->where('idtheme', $id)->delete();
        $vote->delete();

        return back()->with('success', 'Protocole de suppression exécuté. Données effacées.');
    }

    public function editCandidats($id)
    {
        $competition = VoteTheme::findOrFail($id);
        $tousLesJoueurs = Candidat::orderBy('nom_joueur')->get();
        
        $candidatsIds = DB::table('vote_candidat')
            ->join('concernecandidat', 'vote_candidat.id_vote_candidat', '=', 'concernecandidat.id_vote_candidat')
            ->where('vote_candidat.idtheme', $id)
            ->pluck('concernecandidat.idjoueur')
            ->toArray();

        return view('vente.votation_candidats', compact('competition', 'tousLesJoueurs', 'candidatsIds'));
    }

    public function updateCandidats(Request $request, $id)
    {
        $idsVoteCandidat = DB::table('vote_candidat')->where('idtheme', $id)->pluck('id_vote_candidat');
        DB::table('concernecandidat')->whereIn('id_vote_candidat', $idsVoteCandidat)->delete();
        DB::table('vote_candidat')->where('idtheme', $id)->delete();

        if ($request->has('joueurs')) {
            foreach ($request->joueurs as $joueurId) {
                $joueur = Candidat::find($joueurId);
                
                $idVoteCandidat = DB::table('vote_candidat')->insertGetId([
                    'idtheme' => $id,
                    'nom_affichage' => $joueur->prenom_joueur . ' ' . $joueur->nom_joueur,
                    'type_affichage' => 'Joueur'
                ], 'id_vote_candidat');

                DB::table('concernecandidat')->insert([
                    'id_vote_candidat' => $idVoteCandidat,
                    'idjoueur' => $joueurId
                ]);
            }
        }

        return redirect()->route('vente.votation.list')->with('success', 'Candidats mis à jour !');
    }
}