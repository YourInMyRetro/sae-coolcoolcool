<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VoteSeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // 1. BALLON D'OR
        // ==========================================
        $this->createVoteWithCandidates(
            'Ballon d\'Or 2026',
            [
                ['nom' => 'Mbappe', 'prenom' => 'Kylian', 'poste' => 'Attaquant', 'url' => '/img/vote/kylian-mbappe.jpg'],
                ['nom' => 'Haaland', 'prenom' => 'Erling', 'poste' => 'Attaquant', 'url' => '/img/vote/erling-haaland.jpg'],
                ['nom' => 'Vinicius', 'prenom' => 'Junior', 'poste' => 'Attaquant', 'url' => '/img/vote/vinicius-junior.jpg'],
                ['nom' => 'Bellingham', 'prenom' => 'Jude', 'poste' => 'Milieu', 'url' => '/img/vote/jude-bellingham.jpg'],
                ['nom' => 'Kane', 'prenom' => 'Harry', 'poste' => 'Attaquant', 'url' => '/img/vote/harry-kane.jpg'],
            ]
        );

        // ==========================================
        // 2. GOLDEN BOY
        // ==========================================
        $this->createVoteWithCandidates(
            'Golden Boy 2026 (Meilleur Espoir)',
            [
                ['nom' => 'Yamal', 'prenom' => 'Lamine', 'poste' => 'Attaquant', 'url' => '/img/vote/lamine-yamal.jpg'],
                ['nom' => 'Zaire-Emery', 'prenom' => 'Warren', 'poste' => 'Milieu', 'url' => '/img/vote/warren-zaire-emery.jpg'],
                ['nom' => 'Endrick', 'prenom' => 'Felipe', 'poste' => 'Attaquant', 'url' => '/img/vote/endrick-felipe.jpg'],
                ['nom' => 'Gavi', 'prenom' => 'Pablo', 'poste' => 'Milieu', 'url' => '/img/vote/pablo-gavi.jpg'],
                ['nom' => 'Guler', 'prenom' => 'Arda', 'poste' => 'Milieu', 'url' => '/img/vote/arda-guler.jpg'],
            ]
        );

        // ==========================================
        // 3. TROPHÉE YACHINE
        // ==========================================
        $this->createVoteWithCandidates(
            'Trophée Yachine 2026 (Meilleur Gardien)',
            [
                ['nom' => 'Courtois', 'prenom' => 'Thibaut', 'poste' => 'Gardien', 'url' => '/img/vote/thibaut-courtois.jpg'],
                ['nom' => 'Maignan', 'prenom' => 'Mike', 'poste' => 'Gardien', 'url' => '/img/vote/mike-maignan.jpg'],
                ['nom' => 'Martinez', 'prenom' => 'Emiliano', 'poste' => 'Gardien', 'url' => '/img/vote/emiliano-martinez.jpg'],
                ['nom' => 'Ederson', 'prenom' => 'Moraes', 'poste' => 'Gardien', 'url' => '/img/vote/ederson-moraes.jpg'],
                ['nom' => 'Alisson', 'prenom' => 'Becker', 'poste' => 'Gardien', 'url' => '/img/vote/alisson-becker.jpg'],
            ]
        );
    }

    // --- FONCTIONS UTILITAIRES ---

    private function getOrCreateClub($nom)
    {
        $club = DB::table('club')->where('nomclub', $nom)->first();
        if ($club) return $club->idclub;

        return DB::table('club')->insertGetId([
            'nomclub' => $nom,
            'description' => 'Club généré automatiquement'
        ], 'idclub');
    }

    private function createVoteWithCandidates($nomTheme, $candidatsData)
    {
        // A. Thème
        $theme = DB::table('vote_theme')->where('nom_theme', $nomTheme)->first();
        if ($theme) {
            $idTheme = $theme->idtheme;
        } else {
            $idTheme = DB::table('vote_theme')->insertGetId([
                'nom_theme' => $nomTheme,
                'date_ouverture' => Carbon::now()->subDays(2),
                'date_fermeture' => Carbon::now()->addDays(20),
            ], 'idtheme');
        }

        $idClub = $this->getOrCreateClub('FIFA Legends');

        foreach ($candidatsData as $c) {
            // B. Joueur (Table 'candidat' selon Nouveau.sql)
            $joueur = DB::table('candidat')
                ->where('nom_joueur', $c['nom'])
                ->where('prenom_joueur', $c['prenom'])
                ->first();

            if ($joueur) {
                $idJoueur = $joueur->idjoueur;
            } else {
                $idJoueur = DB::table('candidat')->insertGetId([
                    'nom_joueur' => $c['nom'],
                    'prenom_joueur' => $c['prenom'],
                    'idclub' => $idClub,
                    'date_naissance_joueur' => '2000-01-01',
                    'taille_joueur' => 1.85,
                    'poids_joueur' => 80.0,
                    'nombre_selection' => 10,
                    'pied_prefere' => 'Droit'
                ], 'idjoueur');
            }

            // C. Gestion de la Photo (Tables photo_publication + photo_candidat)
            // On vérifie si le joueur a déjà une photo liée
            $photoLiee = DB::table('photo_candidat')->where('idjoueur', $idJoueur)->exists();
            
            if (!$photoLiee && !empty($c['url'])) {
                // 1. Création de la photo dans photo_publication
                $idPhoto = DB::table('photo_publication')->insertGetId([
                    'url_photo' => $c['url']
                ], 'id_photo_publication');

                // 2. Liaison dans photo_candidat
                DB::table('photo_candidat')->insert([
                    'id_photo_publication' => $idPhoto,
                    'idjoueur' => $idJoueur
                ]);
            }

            // D. Liaison au Vote (Tables vote_candidat + concernecandidat)
            // 1. Création de l'entrée vote_candidat (l'affichage dans ce vote spécifique)
            $voteCandidatExists = DB::table('vote_candidat')
                ->where('idtheme', $idTheme)
                ->where('nom_affichage', $c['nom'] . ' ' . $c['prenom'])
                ->first();

            if (!$voteCandidatExists) {
                $idVoteCandidat = DB::table('vote_candidat')->insertGetId([
                    'idtheme' => $idTheme,
                    'nom_affichage' => $c['nom'] . ' ' . $c['prenom'], 
                    'type_affichage' => $c['poste'] 
                ], 'id_vote_candidat');

                // 2. Liaison finale table concernecandidat
                DB::table('concernecandidat')->insert([
                    'id_vote_candidat' => $idVoteCandidat,
                    'idjoueur' => $idJoueur
                ]);
            }
        }
    }
}