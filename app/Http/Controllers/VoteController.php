<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\VoteTheme;
use App\Models\Vote;

class VoteController extends Controller
{
    public function index()
    {
        $competitions = VoteTheme::all(); 
        return view('vote.index', compact('competitions'));
    }

    public function show($id)
    {
        $competition = VoteTheme::findOrFail($id);
        
        $joueurs = DB::table('candidat')
            ->join('concernecandidat', 'candidat.idjoueur', '=', 'concernecandidat.idjoueur')
            ->join('vote_candidat', 'concernecandidat.id_vote_candidat', '=', 'vote_candidat.id_vote_candidat')
            ->leftJoin('photo_candidat', 'candidat.idjoueur', '=', 'photo_candidat.idjoueur')
            ->leftJoin('photo_publication', 'photo_candidat.id_photo_publication', '=', 'photo_publication.id_photo_publication')
            ->where('vote_candidat.idtheme', $id)
            ->select(
                'candidat.*', 
                'photo_publication.url_photo', 
                'vote_candidat.nom_affichage', 
                'vote_candidat.id_vote_candidat'
            )
            ->distinct()
            ->get();

        return view('vote.show', compact('competition', 'joueurs'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'id_vote_candidat' => 'required|integer'
        ]);

        $theme = VoteTheme::findOrFail($id);
        $userId = Auth::id();

        $alreadyVoted = DB::table('vote')
            ->join('faitvote', 'vote.id_vote', '=', 'faitvote.id_vote')
            ->where('vote.idtheme', $id)
            ->where('faitvote.id_utilisateur', $userId)
            ->exists();

        if ($alreadyVoted) {
            return back()->with('error', 'Vous avez déjà voté pour cette catégorie.');
        }

        DB::transaction(function () use ($id, $userId, $request) {
            $voteId = DB::table('vote')->insertGetId([
                'idtheme' => $id,
                'date_vote' => now()
            ]);

            DB::table('faitvote')->insert([
                'id_vote' => $voteId,
                'id_utilisateur' => $userId
            ]);

            DB::table('concernevote')->insert([
                'id_vote' => $voteId,
                'id_vote_candidat' => $request->id_vote_candidat,
                'classement' => 1
            ]);
        });

        return redirect()->route('vote.index')->with('success', 'Votre vote a bien été pris en compte !');
    }
}