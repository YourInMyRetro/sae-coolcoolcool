<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        $candidats = DB::table('candidat')
            ->join('concernecandidat', 'candidat.idjoueur', '=', 'concernecandidat.idjoueur')
            ->join('vote_candidat', 'concernecandidat.id_vote_candidat', '=', 'vote_candidat.id_vote_candidat')
            ->leftJoin('photo_candidat', 'candidat.idjoueur', '=', 'photo_candidat.idjoueur')
            ->leftJoin('photo_publication', 'photo_candidat.id_photo_publication', '=', 'photo_publication.id_photo_publication')
            ->where('vote_candidat.idtheme', $id)
            ->select(
                'candidat.*',
                'vote_candidat.id_vote_candidat',
                'vote_candidat.type_affichage',
                'photo_publication.url_photo'
            )
            ->get();

        foreach($candidats as $candidat) {
            $candidat->articles_lies = Publication::where('titre_publication', 'LIKE', "%{$candidat->nom_joueur}%")
                                                  ->limit(3)
                                                  ->get();
        }

        $aDejaVote = false;
        if (Auth::check()) {
            $aDejaVote = DB::table('vote')
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
            ->join('vote', 'faitvote.id_vote', '=', 'vote.id_vote')
            ->where('faitvote.id_utilisateur', Auth::id())
            ->where('vote.idtheme', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Vous avez déjà voté pour cette élection !']);
        }

        DB::transaction(function () use ($request, $id) {
            $idVote = DB::table('vote')->insertGetId([
                'idtheme' => $id,
                'date_vote' => now()
            ], 'id_vote');

            DB::table('faitvote')->insert([
                'id_utilisateur' => Auth::id(),
                'id_vote' => $idVote
            ]);

            Candidat::whereIn('idjoueur', $request->candidats)->increment('nombre_selection');
        });

        return redirect()->route('vote.index')->with('success', 'Votre vote a bien été pris en compte. Merci !');
    }
}