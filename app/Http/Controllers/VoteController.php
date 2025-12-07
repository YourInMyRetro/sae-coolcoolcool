<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\VoteTheme;

class VoteController extends Controller
{
    public function index()
    {
        // MODIFICATION ICI : On prend tout, peu importe la date
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
}