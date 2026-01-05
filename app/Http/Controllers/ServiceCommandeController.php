<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\SuiviLivraison; 
use Carbon\Carbon;

class ServiceCommandeController extends Controller
{
    public function dashboard() {

        $commandesExpress = Commande::express()
                                    ->with('suivi')
                                    ->orderBy('date_commande', 'desc')
                                    ->get();
        
        $commandesLivre = Commande::where('statut_livraison', 'Livrée')->get();


        $commandesEnCours = Commande::where('statut_livraison', 'Expédiée')
                                    ->orderBy('date_commande', 'asc')
                                    ->get();

        return view('service.dashboard', compact('commandesExpress', 'commandesLivre', 'commandesEnCours'));
    }

    public function validerReception(Request $request, $id) {
        $commande = Commande::findOrFail($id);
        
        $commande->statut_livraison = 'Livrée';
        $commande->save();
    
        SuiviLivraison::updateOrCreate(
            ['id_commande' => $commande->id_commande],
            [
                'date_statut_final' => Carbon::now(),
                'id_transporteur' => $commande->suivi ? $commande->suivi->id_transporteur : 1 
            ]
        );
    
        return back()->with('success', 'La commande #' . $id . ' a été marquée comme Livrée.');
    }


    public function storeReserve(Request $request) {
        $request->validate([
            'motif' => 'required|min:5', 
            'commande_id' => 'required|exists:commande,id_commande'
        ]);

        $commande = Commande::with('suivi')->findOrFail($request->commande_id);
        

        SuiviLivraison::updateOrCreate(
            ['id_commande' => $commande->id_commande],
            [
                'reserve_client' => $request->motif,
                'date_statut_final' => Carbon::now(), 
                'id_transporteur' => $commande->suivi ? $commande->suivi->id_transporteur : 1 
            ]
        );
        $commande->statut_livraison = 'Réserve';
        $commande->save();

        return back()->with('success', 'La réserve a bien été enregistrée et le statut corrigé en Réserve.');
    }
}