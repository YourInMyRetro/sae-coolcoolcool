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
                    'url' => '/img/vote/kylian-mbappe.jpg'
                ],
                [
                    'nom' => 'Haaland', 'prenom' => 'Erling', 'poste' => 'Attaquant',
                    'url' => '/img/vote/erling-haaland.jpg'
                ],
                [
                    'nom' => 'Vinicius', 'prenom' => 'Junior', 'poste' => 'Attaquant',
                    'url' => '/img/vote/vinicius-junior.jpg'
                ],
                [
                    'nom' => 'Bellingham', 'prenom' => 'Jude', 'poste' => 'Milieu',
                    'url' => '/img/vote/jude-bellingham.jpg'
                ],
                [
                    'nom' => 'Kane', 'prenom' => 'Harry', 'poste' => 'Attaquant',
                    'url' => '/img/vote/harry-kane.jpg'
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
                    'url' => '/img/vote/lamine-yamal.jpg'
                ],
                [
                    'nom' => 'Zaire-Emery', 'prenom' => 'Warren', 'poste' => 'Milieu',
                    'url' => '/img/vote/warren-zaire-emery.jpg'
                ],
                [
                    'nom' => 'Endrick', 'prenom' => 'Felipe', 'poste' => 'Attaquant',
                    'url' => '/img/vote/endrick-felipe.jpg'
                ],
                [
                    'nom' => 'Gavi', 'prenom' => 'Pablo', 'poste' => 'Milieu',
                    'url' => '/img/vote/pablo-gavi.jpg'
                ],
                [
                    'nom' => 'Guler', 'prenom' => 'Arda', 'poste' => 'Milieu',
                    'url' => '/img/vote/arda-guler.jpg'
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
                    'url' => '/img/vote/thibaut-courtois.jpg'
                ],
                [
                    'nom' => 'Maignan', 'prenom' => 'Mike', 'poste' => 'Gardien',
                    'url' => '/img/vote/mike-maignan.jpg'
                ],
                [
                    'nom' => 'Martinez', 'prenom' => 'Emiliano', 'poste' => 'Gardien',
                    'url' => '/img/vote/emiliano-martinez.jpg'
                ],
                [
                    'nom' => 'Ederson', 'prenom' => 'Moraes', 'poste' => 'Gardien',
                    'url' => '/img/vote/ederson-moraes.jpg'
                ],
                [
                    'nom' => 'Alisson', 'prenom' => 'Becker', 'poste' => 'Gardien',
                    'url' => '/img/vote/alisson-becker.jpg'
                ],
            ]
        );
    }

    // --- FONCTIONS UTILITAIRES (Inchangées mais nécessaires) ---

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

            // C. Gestion de la Photo
            $photoLiee = DB::table('photo_candidat')->where('idjoueur', $idJoueur)->exists();
            
            // NOTE : On vérifie si l'URL a changé pour mettre à jour si nécessaire
            // Dans un seeder simple, on insère si ça n'existe pas.
            // Pour forcer la mise à jour, on supprime l'ancienne liaison si elle existe (Optionnel mais propre)
            
            if (!$photoLiee && !empty($c['url'])) {
                $idPhoto = DB::table('photo_publication')->insertGetId([
                    'url_photo' => $c['url']
                ], 'id_photo_publication');

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