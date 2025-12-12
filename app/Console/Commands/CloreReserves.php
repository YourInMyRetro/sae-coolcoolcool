<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Commande;
use Carbon\Carbon;

class CloreReserves extends Command
{
    protected $signature = 'commandes:clore-reserves';
    protected $description = 'Clôture les réserves (Mode Débogage)';

    public function handle()
    {
        $this->info("--- DÉBUT DU DIAGNOSTIC ---");
        
        // 1. On cherche TOUTES les commandes en Réserve, sans filtrer la date
        $commandes = Commande::where('statut_livraison', 'Réserve')->get();
        
        $this->info("Commandes trouvées avec statut 'Réserve' : " . $commandes->count());

        if ($commandes->count() === 0) {
            $this->error("ERREUR : Aucune commande n'a le statut 'Réserve'. Vérifiez l'orthographe exacte dans la base de données (accent ?).");
            return;
        }

        $dateLimite = Carbon::now()->subMinutes(1);
        $count = 0;

        foreach ($commandes as $cmd) {
            $this->info("\nCommande #{$cmd->id_commande} :");

            // Vérification de la relation
            if (!$cmd->suivi) {
                $this->error("  [X] ERREUR CRITIQUE : Pas de ligne dans la table 'suivi_livraison' !");
                $this->line("      -> La date de la réserve n'a pas pu être enregistrée.");
                $this->line("      -> Solution : Il faut que le contrôleur crée cette ligne si elle n'existe pas.");
                continue;
            }

            $date = $cmd->suivi->date_statut_final;
            
            if (!$date) {
                $this->error("  [X] ERREUR : La ligne suivi existe, mais 'date_statut_final' est vide (NULL).");
                continue;
            }

            $dateCarbone = Carbon::parse($date);
            $this->line("  - Date de la réserve : " . $dateCarbone->format('H:i:s'));
            $this->line("  - Date limite (il y a 1 min) : " . $dateLimite->format('H:i:s'));

            if ($dateCarbone < $dateLimite) {
                $this->info("  [V] SUCCÈS : Le délai est passé. Validation...");
                $cmd->statut_livraison = 'Validée';
                $cmd->save();
                $count++;
            } else {
                $this->warn("  [!] ATTENTE : Délai pas encore écoulé. Attendez encore un peu.");
            }
        }

        $this->info("\n--- FIN : $count réserves validées ---");
    }
}