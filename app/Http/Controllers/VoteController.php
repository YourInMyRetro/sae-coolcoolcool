<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Vote;
use App\Models\VoteTheme;
use App\Models\Candidat;
use App\Models\Publication; 

class VoteController extends Controller
{
    public function index()
    {
        $votes = VoteTheme::where('date_ouverture', '<=', now())
                          ->where('date_fermeture', '>=', now())
                          ->get();

        
        return view('vote.index', compact('votes'));
    }

   public function show($id)
    {
        // 1. Récupérer le thème de vote
        $vote = VoteTheme::where('idtheme', $id)->firstOrFail();
        
        // 2. Récupérer les candidats AVEC LEUR PHOTO
        $candidats = \Illuminate\Support\Facades\DB::table('candidat')
            ->join('concernecandidat', 'candidat.idjoueur', '=', 'concernecandidat.idjoueur')
            ->join('vote_candidat', 'concernecandidat.id_vote_candidat', '=', 'vote_candidat.id_vote_candidat')
            
            // --- C'est ici que la magie opère pour l'image ---
            ->leftJoin('photo_candidat', 'candidat.idjoueur', '=', 'photo_candidat.idjoueur')
            ->leftJoin('photo_publication', 'photo_candidat.id_photo_publication', '=', 'photo_publication.id_photo_publication')
            // ------------------------------------------------
            
            ->where('vote_candidat.idtheme', $id)
            ->select(
                'candidat.*', 
                'vote_candidat.id_vote_candidat', 
                'vote_candidat.type_affichage', 
                'photo_publication.url_photo' // <--- On sélectionne l'URL ici
            ) 
            ->get();

        // 3. Récupérer les articles liés (Blog) pour la modale
        foreach($candidats as $candidat) {
            $candidat->articles_lies = Publication::where('titre_publication', 'LIKE', "%{$candidat->nom_joueur}%")
                                                  ->limit(3)
                                                  ->get();
        }

        // 4. Vérifier si l'utilisateur a déjà voté
        $aDejaVote = false;
        if (Auth::check()) {
            $aDejaVote = \Illuminate\Support\Facades\DB::table('vote')
                ->join('faitvote', 'vote.id_vote', '=', 'faitvote.id_vote')
                ->where('vote.idtheme', $id)
                ->where('faitvote.id_utilisateur', Auth::id())
                ->exists();
        }

        return view('vote.show', compact('vote', 'candidats', 'aDejaVote'));
    }

    public function store(Request $request, $id)
    {
        // 1. Validation : On s'assure qu'il y a bien 3 joueurs cochés
        $request->validate([
            'candidats' => 'required|array|min:3|max:3', 
        ], [
            'candidats.min' => 'Vous devez sélectionner exactement 3 joueurs.',
            'candidats.max' => 'Vous devez sélectionner exactement 3 joueurs.',
            'candidats.required' => 'Veuillez sélectionner 3 joueurs.'
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['msg' => 'Connectez-vous pour voter.']);
        }

        // 2. Vérification "A déjà voté" (CORRIGÉ)
        // On doit joindre 'vote' pour vérifier si l'utilisateur a déjà voté POUR CE THÈME ($id)
        $exists = DB::table('faitvote')
            ->join('vote', 'faitvote.id_vote', '=', 'vote.id_vote')
            ->where('faitvote.id_utilisateur', Auth::id())
            ->where('vote.idtheme', $id) // $id est l'idtheme passé dans l'URL
            ->exists();
        
        if ($exists) {
            return back()->withErrors(['msg' => 'Vous avez déjà voté pour cette élection !']);
        }

        // 3. Enregistrement du vote
        DB::transaction(function () use ($request, $id) {
            
            // A. Créer un bulletin de vote dans la table 'vote' pour ce thème
            $idVote = DB::table('vote')->insertGetId([
                'idtheme' => $id,
                'date_vote' => now() 
            ], 'id_vote'); // On précise la clé primaire de retour

            // B. Lier l'utilisateur à ce bulletin dans 'faitvote'
            DB::table('faitvote')->insert([
                'id_utilisateur' => Auth::id(),
                'id_vote' => $idVote
            ]);

            // C. Incrémenter les voix des joueurs (Simplicité pour le dashboard)
            // Attention : Ta vue envoie des 'idjoueur' (via value="{{ $candidat->idjoueur }}")
            // Mais la requête ici utilisait 'id_candidat'. 
            // Si ta table candidat a pour clé 'idjoueur', utilise 'idjoueur'.
            Candidat::whereIn('idjoueur', $request->candidats)->increment('nombre_voix');
        });

        return redirect()->route('vote.index')->with('success', 'Votre vote a bien été pris en compte. Merci !');
    }
}