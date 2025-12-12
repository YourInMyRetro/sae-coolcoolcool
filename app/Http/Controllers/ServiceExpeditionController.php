<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\SuiviLivraison;
use Carbon\Carbon;

class ServiceExpeditionController extends Controller
{
    /**
     * US 25 & 26 : Dashboard Expédition
     * Affiche les commandes "Express" (Autre mode) pour les périodes demandées.
     */
    public function index()
    {
        // Définition des bornes de temps pour "demi-journée prochaine" et "journée prochaine"
        $now = Carbon::now();
        $demain = Carbon::tomorrow();
        
        // Logique simplifiée pour "demi-journée" : Matin (0-12h) ou Après-midi (12h-24h)
        // Ici on prend simplement les commandes Express prêtes à partir
        
        // Récupère les commandes Express qui sont 'En préparation' ou 'Validée'
        // On charge l'utilisateur pour avoir son info (Nom/Tel) et l'adresse
        $commandesAExpedier = Commande::where('type_livraison', 'Express')
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
            ->with(['utilisateur', 'suivi'])
            ->orderBy('date_commande', 'asc')
            ->get();

        return view('service.expedition', compact('commandesAExpedier'));
    }

    /**
     * US 27 : Prise en charge par le transporteur
     */
    public function priseEnCharge(Request $request)
    {
        $request->validate([
            'commandes' => 'required|array',
            'commandes.*' => 'exists:commande,id_commande'
        ]);

        $ids = $request->input('commandes');

        foreach ($ids as $id) {
            $commande = Commande::find($id);
            
            // Mise à jour du statut commande
            $commande->statut_livraison = 'Expédiée';
            $commande->save();

            // Création ou mise à jour du suivi (Date prise en charge)
            SuiviLivraison::updateOrCreate(
                ['id_commande' => $id],
                [
                    'date_prise_en_charge' => Carbon::now(),
                    // On assigne un transporteur par défaut si pas encore défini (ex: ID 1 dans ton SQL)
                    'id_transporteur' => $commande->suivi ? $commande->suivi->id_transporteur : 1 
                ]
            );
        }

        return back()->with('success', count($ids) . ' commandes remises au transporteur.');
    }

    /**
     * US 28 : Simulation envoi SMS
     */
    public function sendSms(Request $request, $id)
    {
        $commande = Commande::with('utilisateur')->findOrFail($id);
        
        // Comme le champ téléphone n'existe pas nativement dans ton SQL,
        // on simule l'action.
        $nomClient = $commande->utilisateur->nom;
        
        // Logique fictive d'envoi SMS
        // SMS::send($commande->utilisateur->telephone, "Votre commande #$id est partie !");

        return back()->with('success', "SMS envoyé à {$nomClient} pour la commande #{$id}.");
    }
}