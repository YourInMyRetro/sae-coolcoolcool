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
        
        $vote = VoteTheme::where('idtheme', $id)->firstOrFail();
        
       
        $candidats = \Illuminate\Support\Facades\DB::table('candidat')
            ->join('concernecandidat', 'candidat.idjoueur', '=', 'concernecandidat.idjoueur')
            ->join('vote_candidat', 'concernecandidat.id_vote_candidat', '=', 'vote_candidat.id_vote_candidat')
            ->where('vote_candidat.idtheme', $id)
            ->select('candidat.*', 'vote_candidat.id_vote_candidat') // On récupère l'ID vote_candidat pour le formulaire
            ->get();

        
        foreach($candidats as $candidat) {
            $candidat->articles_lies = Publication::where('titre_publication', 'LIKE', "%{$candidat->nom_joueur}%") // Attention: nom_joueur dans votre SQL
                                                  ->orWhere('resume_publication', 'LIKE', "%{$candidat->nom_joueur}%") // resume_publication dans SQL
                                                  ->limit(3)
                                                  ->get();
        }

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

        $exists = DB::table('faitvote')
                    ->where('id_utilisateur', Auth::id())
                    ->where('id_vote_theme', $id)
                    ->exists();
        
        if ($exists) {
            return back()->withErrors(['msg' => 'Vous avez déjà voté pour cette élection !']);
        }


        DB::transaction(function () use ($request, $id) {
            DB::table('faitvote')->insert([
                'id_utilisateur' => Auth::id(),
                'id_vote_theme' => $id,
                'date_vote' => now() 
            ]);

            Candidat::whereIn('id_candidat', $request->candidats)->increment('nombre_voix');
        });

        return redirect()->route('vote.index')->with('success', 'Votre vote a bien été pris en compte. Merci !');
    }
}