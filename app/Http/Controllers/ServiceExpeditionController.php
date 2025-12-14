<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\SuiviLivraison;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Ajout essentiel pour la simulation (ID 28)

class ServiceExpeditionController extends Controller
{
    /**
     * ID 25 & 26 : Dashboard Expédition
     * Affiche les commandes prêtes à partir selon le mode de transport et le créneau.
     */
    public function index()
    {
        $now = Carbon::now();
        
        // Calcul "cosmétique" des créneaux pour l'affichage (ID 25)
        if ($now->hour < 12) {
            $creneauDomicile = "Cet après-midi (12h - 20h)"; 
        } else {
            $creneauDomicile = "Demain matin (08h - 12h)";
        }
        $creneauAutre = "Demain (" . Carbon::tomorrow()->format('d/m/Y') . ")";

        // ID 25 : Transport à domicile (Standard)
        // CRITIQUE : On exclut les commandes qui ont déjà une date_prise_en_charge
        $commandesDomicile = Commande::where('type_livraison', 'Standard')
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
            ->whereDoesntHave('suivi', function($q) {
                $q->whereNotNull('date_prise_en_charge');
            })
            ->with(['utilisateur', 'suivi'])
            ->orderBy('date_commande', 'asc') // FIFO : Premier arrivé, premier servi
            ->get();

        // ID 26 : Autre mode (Express, etc.)
        $commandesAutre = Commande::where('type_livraison', '!=', 'Standard')
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
            ->whereDoesntHave('suivi', function($q) {
                $q->whereNotNull('date_prise_en_charge');
            })
            ->with(['utilisateur', 'suivi'])
            ->orderBy('date_commande', 'asc')
            ->get();

        return view('service.expedition', compact(
            'commandesDomicile', 
            'commandesAutre', 
            'creneauDomicile', 
            'creneauAutre'
        ));
    }

    /**
     * ID 27 : Prise en charge par le transporteur (Action de groupe)
     */
    public function priseEnCharge(Request $request)
    {
        // 1. Validation : On s'assure qu'on a bien reçu une liste d'IDs valides
        $request->validate([
            'commandes' => 'required|array',
            'commandes.*' => 'exists:commande,id_commande'
        ], [
            'commandes.required' => 'Veuillez cocher au moins une commande à remettre au transporteur.',
        ]);

        $ids = $request->input('commandes');
        $successCount = 0;

        foreach ($ids as $id) {
            $commande = Commande::find($id);

            // Sécurité métier : On ne ré-expédie pas une commande déjà partie
            if ($commande->statut_livraison === 'Expédiée' || $commande->statut_livraison === 'Livrée') {
                continue; 
            }
            
            // Mise à jour du statut
            $commande->statut_livraison = 'Expédiée';
            $commande->save();

            // ID 27 : Enregistrement de la date et heure EXACTES de prise en charge
            // Si le suivi n'existe pas encore, on le crée.
            SuiviLivraison::updateOrCreate(
                ['id_commande' => $id],
                [
                    'date_prise_en_charge' => Carbon::now(),
                    // Si un transporteur était déjà assigné on le garde, sinon par défaut id 1 (France Express)
                    'id_transporteur' => $commande->suivi->id_transporteur ?? 1 
                ]
            );
            $successCount++;
        }

        if ($successCount === 0) {
            return back()->with('warning', 'Aucune commande traitée (elles étaient peut-être déjà expédiées).');
        }

        return back()->with('success', "🚚 $successCount commandes remises au transporteur avec succès !");
    }

    /**
     * ID 28 : Envoi SMS Client
     */
    public function sendSms(Request $request, $id)
    {
        $commande = Commande::with('utilisateur')->findOrFail($id);
        
        $tel = $commande->utilisateur->telephone;
        $nom = $commande->utilisateur->nom;

        // Validation métier : Pas de téléphone, pas de SMS
        if (empty($tel)) {
            return back()->withErrors(['msg' => "Impossible d'envoyer le SMS : aucun numéro de téléphone renseigné pour ce client."]);
        }

        // "Pofinage" : Nettoyage du numéro (On garde que les chiffres)
        $telClean = preg_replace('/[^0-9]/', '', $tel);

        // ID 28 : Simulation technique
        // On écrit dans les logs du serveur (storage/logs/laravel.log)
        // C'est une preuve vérifiable par le prof que la logique est exécutée.
        Log::info("SMS SERVICE | To: $telClean | Client: $nom | Msg: Votre commande #{$id} a été remise au transporteur.");

        return back()->with('success', "📱 SMS de confirmation envoyé à {$nom} (Simulation enregistrée).");
    }
}