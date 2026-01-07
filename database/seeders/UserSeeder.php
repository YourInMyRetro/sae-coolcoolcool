<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';

    public function run()
    {
        
        // 1. Création de l'utilisateur principal
        $idUser = DB::table('utilisateur')->insertGetId([
            'nom' => 'Admin',
            'prenom' => 'Super',
            'mail' => 'admin@test.com',
            'date_naissance' => '2000-01-01',
            'pays_naissance' => 'France',
            'langue' => 'Français',
            'mot_de_passe_chiffre' => Hash::make('password'), // Mot de passe : "password"
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_utilisateur');

        // 2. Si tu as un système de rôles (ex: table acheteur ou professionel), tu peux l'ajouter ici
        // Par exemple, on le définit comme acheteur par défaut
        DB::table('acheteur')->insert([
            'id_utilisateur' => $idUser
        ]);
        
        $this->command->info("Utilisateur créé : admin@test.com / password");
    }
}