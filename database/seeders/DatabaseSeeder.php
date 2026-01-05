<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// CORRECTION : Le nom de la classe est maintenant DatabaseSeeder
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création du Compte DIRECTEUR
        if (!User::where('mail', 'directeur@fifa.com')->exists()) {
            User::create([
                'nom' => 'Colin',
                'prenom' => 'Pascal',
                'mail' => 'directeur@fifa.com',
                'date_naissance' => '1970-03-23',
                'pays_naissance' => 'France',
                'langue' => 'Français',
                'mot_de_passe_chiffre' => Hash::make('admin123'),
                'role' => 'directeur', 
                'newsletter_optin' => false,
            ]);
        }

        // 2. Création du Compte SERVICE COMMANDE (Essentiel pour ta mission)
        if (!User::where('mail', 'service@fifa.com')->exists()) {
            User::create([
                'nom' => 'Bond',
                'prenom' => 'James',
                'mail' => 'service@fifa.com',
                'date_naissance' => '1985-05-15',
                'pays_naissance' => 'France',
                'langue' => 'Français',
                'mot_de_passe_chiffre' => Hash::make('password'),
                'role' => 'service_commande',
                'newsletter_optin' => false,
            ]);
        }
        // 3. Création du Compte EXPÉDITION (Celui que tu veux tester)
        if (!User::where('mail', 'expedition@fifa.com')->exists()) {
            User::create([
                'nom' => 'Rapide',
                'prenom' => 'Expe',
                'mail' => 'expedition@fifa.com',
                'date_naissance' => '1992-01-01',
                'pays_naissance' => 'France',
                'langue' => 'Français',
                'mot_de_passe_chiffre' => Hash::make('password'), // Mot de passe : password
                'role' => 'service_expedition', // Correspond à ton Middleware IsExpedition
                'newsletter_optin' => false,
            ]);
        }

        // =================================================================
        // DONNÉES DE TEST POUR L'INTERFACE (Pour ne pas avoir un tableau vide)
        // =================================================================

        // 4. Création d'un Transporteur (Nécessaire pour expédier)
        // Attention : Ton fichier SQL impose des noms précis dans la contrainte CHECK
        $transporteur = \App\Models\Transporteur::firstOrCreate(
            ['nom' => 'Chronopost'],
            ['delai_min_transporteur' => 2, 'delai_max_transporteur' => 5]
        );

        // 5. Création d'un Client lambda pour passer commande
        $client = User::where('mail', 'client@test.com')->first();
        if (!$client) {
            $client = User::create([
                'nom' => 'Dupont',
                'prenom' => 'Testeur',
                'mail' => 'client@test.com',
                'date_naissance' => '2000-01-01',
                'pays_naissance' => 'France',
                'langue' => 'Français',
                'mot_de_passe_chiffre' => Hash::make('password'),
                'role' => 'client',
                'newsletter_optin' => true,
            ]);
        }

        // 6. Création d'une adresse pour ce client
        // AJOUT DU 2ème PARAMÈTRE 'id_adresse' pour dire à Laravel quelle colonne récupérer
        $id_adresse = \Illuminate\Support\Facades\DB::table('adresse')->insertGetId([
            'rue' => '10 rue du stade',
            'code_postal_adresse' => '75000',
            'ville_adresse' => 'Paris',
            'pays_adresse' => 'France',
            'type_adresse' => 'Livraison'
        ], 'id_adresse'); 

        // Lier l'adresse au client
        \Illuminate\Support\Facades\DB::table('possedeadresse')->insertOrIgnore([
            'id_adresse' => $id_adresse,
            'id_utilisateur' => $client->id_utilisateur
        ]);

        // 7. Création d'une COMMANDE "En préparation" 
        // C'est celle-ci qui devrait apparaître dans ton interface Expédition
        \App\Models\Commande::create([
            'id_adresse' => $id_adresse,
            'id_utilisateur' => $client->id_utilisateur,
            'montant_total' => 150.00,
            'statut_livraison' => 'En préparation', // Le statut clé pour l'expédition
            'type_livraison' => 'Standard'
        ]);
    }
}