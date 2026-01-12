<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Commande;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AnonymiserDonnees extends Command
{

    protected $signature = 'rgpd:anonymiser {date : La date limite d\'inactivité (format YYYY-MM-DD)}';
    protected $description = 'Anonymise les données personnelles des utilisateurs n\'ayant pas passé de commande depuis la date fournie (RGPD).';
    public function handle()
    {
        $dateInput = $this->argument('date');
        try {
            $dateLimite = Carbon::parse($dateInput);
        } catch (\Exception $e) {
            $this->error("Erreur : La date fournie est invalide. Utilisez le format YYYY-MM-DD.");
            return 1;
        }

        $this->info("--- DÉBUT PROCESSUS RGPD ---");
        $this->line("Recherche des utilisateurs inactifs (sans commande) depuis le : " . $dateLimite->format('d/m/Y'));
        $usersActifsIds = Commande::where('date_commande', '>=', $dateLimite)
                                  ->pluck('id_utilisateur')
                                  ->unique()
                                  ->toArray();

        $usersAAnonymiser = User::whereNotIn('id_utilisateur', $usersActifsIds)
                                ->where('nom', '!=', 'ANONYME')
                                ->whereNotIn('role', ['directeur', 'service_commande', 'service_expedition', 'service_vente', 'admin'])
                                ->get();

        $count = $usersAAnonymiser->count();

        if ($count === 0) {
            $this->info("Aucun utilisateur éligible à l'anonymisation trouvé.");
            return 0;
        }

        $this->warn("$count utilisateur(s) trouvé(s) correspondant aux critères.");

        if (!$this->confirm("ATTENTION : Vous allez définitivement anonymiser ces $count comptes. Les données seront perdues. Confirmer ?")) {
            $this->info("Opération annulée.");
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($usersAAnonymiser as $user) {
            $user->nom = 'ANONYME';
            $user->prenom = 'Utilisateur';
            $user->surnom = null;
            
            $user->mail = 'deleted_' . $user->id_utilisateur . '_' . Str::random(5) . '@rgpd.fifa.com';
            
            $user->telephone = null;
            $user->date_naissance = '1900-01-01'; 
            $user->pays_naissance = null;
            $user->newsletter_optin = false;
            
            $user->mot_de_passe_chiffre = bcrypt(Str::random(30));
            $user->double_auth_active = false;
            $user->code_auth_temporaire = null;

            $user->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Succès : Les données ont été anonymisées conformément au RGPD.");
        
        return 0;
    }
}