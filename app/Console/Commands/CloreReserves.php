<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Commande;
use Carbon\Carbon;

class CloreReserves extends Command
{
    // Le nom de la commande à taper dans le terminal
    protected $signature = 'commandes:clore-reserves';
    
    protected $description = 'Clôture les réserves (Mode Débogage 1 minute)';

    public function handle()
    {
        $this->info("--- DÉBUT DU DIAGNOSTIC (Mode Test 1 minute) ---");
        
        // 1. On cherche les commandes en statut 'Réserve'
        $commandes = Commande::where('statut_livraison', 'Réserve')->get();
        
        $this->info("Commandes en 'Réserve' trouvées : " . $commandes->count());

        if ($commandes->count() === 0) {
            $this->error("Aucune commande à traiter.");
            return;
        }

        // --- C'EST ICI LA MODIFICATION ---
        // On considère qu'une réserve est "vieille" si elle a plus de 1 minute
        $dateLimite = Carbon::now()->subDays(7);
        
        $count = 0;

        foreach ($commandes as $cmd) {
            $this->info("\nCommande #{$cmd->id_commande} :");

            if (!$cmd->suivi) {
                $this->error("  [ERREUR] Pas de suivi associé.");
                continue;
            }

            $dateReserve = $cmd->suivi->date_statut_final; // Date de la réserve
            
            if (!$dateReserve) {
                $this->error("  [ERREUR] Date de statut vide.");
                continue;
            }

            $dateCarbone = Carbon::parse($dateReserve);
            
            // Affichage pour t'aider à comprendre ce qui se passe
            $this->line("  - Heure de la réserve : " . $dateCarbone->format('H:i:s'));
            $this->line("  - Heure limite (now - 1min) : " . $dateLimite->format('H:i:s'));

            // Si la réserve est plus vieille que la limite (1 minute)
            if ($dateCarbone < $dateLimite) {
                $this->info("  [OK] Le délai d'une minute est écoulé. Validation !");
                
                $cmd->statut_livraison = 'Validée';
                $cmd->save();
                $count++;
            } else {
                $this->warn("  [ATTENTE] Moins d'une minute écoulée.");
            }
        }

        $this->info("\n--- FIN : $count réserves validées automatiquement ---");
    }
}