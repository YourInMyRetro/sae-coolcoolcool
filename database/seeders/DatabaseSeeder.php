<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DirecteurSeeder extends Seeder
{
    public function run(): void
    {
        // On vérifie s'il existe déjà pour éviter les doublons
        if (!User::where('mail', 'directeur@fifa.com')->exists()) {
            User::create([
                'nom' => 'Colin',
                'prenom' => 'Pascal',
                'mail' => 'directeur@fifa.com',
                'date_naissance' => '1970-03-23',
                'pays_naissance' => 'France',
                'langue' => 'Français',
                'mot_de_passe_chiffre' => Hash::make('admin123'), // Mot de passe
                'role' => 'directeur', 
                'newsletter_optin' => false,
            ]);
        }
    }
}