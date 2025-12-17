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
    }
}