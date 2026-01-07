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
        // 1. BALLON D'OR (Attaquants stars)
        // ==========================================
        $this->createVoteWithCandidates(
            'Ballon d\'Or 2026',
            [
                [
                    'nom' => 'Mbappe', 'prenom' => 'Kylian', 'poste' => 'Attaquant',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/2022_FIFA_World_Cup_France_4%E2%80%931_Australia_-_Kylian_Mbapp%C3%A9_%28cropped%29.jpg/640px-2022_FIFA_World_Cup_France_4%E2%80%931_Australia_-_Kylian_Mbapp%C3%A9_%28cropped%29.jpg'
                ],
                [
                    'nom' => 'Haaland', 'prenom' => 'Erling', 'poste' => 'Attaquant',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/07/Erling_Haaland_2023_%28cropped%29.jpg/640px-Erling_Haaland_2023_%28cropped%29.jpg'
                ],
                [
                    'nom' => 'Vinicius', 'prenom' => 'Junior', 'poste' => 'Attaquant',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Vinicius_Jr_2021.jpg/640px-Vinicius_Jr_2021.jpg'
                ],
                [
                    'nom' => 'Bellingham', 'prenom' => 'Jude', 'poste' => 'Milieu',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Jude_Bellingham_2023.jpg/640px-Jude_Bellingham_2023.jpg'
                ],
                [
                    'nom' => 'Kane', 'prenom' => 'Harry', 'poste' => 'Attaquant',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Harry_Kane_2018.jpg/640px-Harry_Kane_2018.jpg'
                ],
            ]
        );

        // ==========================================
        // 2. GOLDEN BOY (Meilleurs Espoirs)
        // ==========================================
        $this->createVoteWithCandidates(
            'Golden Boy 2026 (Meilleur Espoir)',
            [
                [
                    'nom' => 'Yamal', 'prenom' => 'Lamine', 'poste' => 'Attaquant',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Lamine_Yamal_2023.jpg/640px-Lamine_Yamal_2023.jpg'
                ],
                [
                    'nom' => 'Zaire-Emery', 'prenom' => 'Warren', 'poste' => 'Milieu',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Warren_Za%C3%AFre-Emery_2023.jpg/640px-Warren_Za%C3%AFre-Emery_2023.jpg'
                ],
                [
                    'nom' => 'Endrick', 'prenom' => 'Felipe', 'poste' => 'Attaquant',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/96/Endrick_Felipe_2023.jpg/640px-Endrick_Felipe_2023.jpg'
                ],
                [
                    'nom' => 'Gavi', 'prenom' => 'Pablo', 'poste' => 'Milieu',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Gavi_2022.jpg/640px-Gavi_2022.jpg'
                ],
                [
                    'nom' => 'Guler', 'prenom' => 'Arda', 'poste' => 'Milieu',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Arda_G%C3%BCler_2023.jpg/640px-Arda_G%C3%BCler_2023.jpg'
                ],
            ]
        );

        // ==========================================
        // 3. TROPHÉE YACHINE (Meilleurs Gardiens)
        // ==========================================
        $this->createVoteWithCandidates(
            'Trophée Yachine 2026 (Meilleur Gardien)',
            [
                [
                    'nom' => 'Courtois', 'prenom' => 'Thibaut', 'poste' => 'Gardien',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Courtois_2018.jpg/640px-Courtois_2018.jpg'
                ],
                [
                    'nom' => 'Maignan', 'prenom' => 'Mike', 'poste' => 'Gardien',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/Mike_Maignan_2019.jpg/640px-Mike_Maignan_2019.jpg'
                ],
                [
                    'nom' => 'Martinez', 'prenom' => 'Emiliano', 'poste' => 'Gardien',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Emiliano_Martinez_2022.jpg/640px-Emiliano_Martinez_2022.jpg'
                ],
                [
                    'nom' => 'Ederson', 'prenom' => 'Moraes', 'poste' => 'Gardien',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/11/Ederson_Moraes_2018.jpg/640px-Ederson_Moraes_2018.jpg'
                ],
                [
                    'nom' => 'Alisson', 'prenom' => 'Becker', 'poste' => 'Gardien',
                    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Alisson_Becker_2018.jpg/640px-Alisson_Becker_2018.jpg'
                ],
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
            $this->command->info(">> Thème '$nomTheme' existe déjà.");
        } else {
            $idTheme = DB::table('vote_theme')->insertGetId([
                'nom_theme' => $nomTheme,
                'date_ouverture' => Carbon::now()->subDays(2),
                'date_fermeture' => Carbon::now()->addDays(20),
            ], 'idtheme');
            $this->command->info(">> Thème '$nomTheme' créé.");
        }

        $idClub = $this->getOrCreateClub('FIFA Legends');

        foreach ($candidatsData as $c) {
            // B. Joueur
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

            // C. Gestion de la Photo (URL dans photo_publication + liaison)
            $photoLiee = DB::table('photo_candidat')->where('idjoueur', $idJoueur)->exists();
            
            if (!$photoLiee && !empty($c['url'])) {
                // 1. Insertion de l'URL
                $idPhoto = DB::table('photo_publication')->insertGetId([
                    'url_photo' => $c['url']
                ], 'id_photo_publication');

                // 2. Liaison
                DB::table('photo_candidat')->insert([
                    'id_photo_publication' => $idPhoto,
                    'idjoueur' => $idJoueur
                ]);
            }

            // D. Liaison au Vote
            $dejaLie = DB::table('vote_candidat')
                ->join('concernecandidat', 'vote_candidat.id_vote_candidat', '=', 'concernecandidat.id_vote_candidat')
                ->where('vote_candidat.idtheme', $idTheme)
                ->where('concernecandidat.idjoueur', $idJoueur)
                ->exists();

            if (!$dejaLie) {
                $idVoteCandidat = DB::table('vote_candidat')->insertGetId([
                    'idtheme' => $idTheme,
                    'nom_affichage' => $c['nom'] . ' ' . $c['prenom'], 
                    'type_affichage' => $c['poste'] 
                ], 'id_vote_candidat');

                DB::table('concernecandidat')->insert([
                    'id_vote_candidat' => $idVoteCandidat,
                    'idjoueur' => $idJoueur
                ]);
            }
        }
    }
}