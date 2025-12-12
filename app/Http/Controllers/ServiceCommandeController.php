<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\SuiviLivraison; // Import nécessaire
use Carbon\Carbon;

class ServiceCommandeController extends Controller
{
    public function dashboard() {
        // On charge les relations pour éviter les erreurs dans la vue
        $commandesExpress = Commande::express()
                                    ->with('suivi')
                                    ->orderBy('date_commande', 'desc')
                                    ->get();
        
        $commandesLivre = Commande::where('statut_livraison', 'Livrée')->get();

        return view('service.dashboard', compact('commandesExpress', 'commandesLivre'));
    }

    // --- CORRECTION MAJEURE ICI ---
    public function storeReserve(Request $request) {
        $request->validate([
            'motif' => 'required|min:5', 
            'commande_id' => 'required|exists:commande,id_commande'
        ]);

        $commande = Commande::with('suivi')->findOrFail($request->commande_id);
        
        // 1. On met à jour le statut
        $commande->statut_livraison = 'Réserve';
        $commande->save();

        // 2. On CRÉE ou on MET À JOUR le suivi (Solution à ton erreur)
        // Cela garantit qu'une ligne existe toujours dans suivi_livraison
        SuiviLivraison::updateOrCreate(
            ['id_commande' => $commande->id_commande], // Condition de recherche
            [
                'reserve_client' => $request->motif,
                'date_statut_final' => Carbon::now(), // C'est cette date qui déclenche le timer
                // Si on crée une nouvelle ligne, il faut un transporteur par défaut (ex: 1)
                'id_transporteur' => $commande->suivi ? $commande->suivi->id_transporteur : 1 
            ]
        );

        return back()->with('success', 'La réserve a bien été enregistrée et le suivi mis à jour.');
    }
}