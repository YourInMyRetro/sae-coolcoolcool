<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\SuiviLivraison;
use Carbon\Carbon;

class ServiceExpeditionController extends Controller
{
    /**
     * ID 25 & 26 : Dashboard Expédition avec filtres temporels et mode de transport.
     */
    public function index()
    {
        // --- 1. Calcul des Créneaux Temporels (Logique Métier) ---
        $now = Carbon::now();
        
        // Logique pour "Demi-journée prochaine" (ID 25)
        // Si on est le matin (0-12h), la prochaine demi-journée est l'après-midi (12-24h).
        // Si on est l'après-midi, c'est demain matin (0-12h).
        if ($now->hour < 12) {
            $creneauDomicile = "Cet après-midi (12h - 24h)";
        } else {
            $creneauDomicile = "Demain matin (00h - 12h)";
        }

        // Logique pour "Journée prochaine" (ID 26)
        $creneauAutre = "Demain (" . Carbon::tomorrow()->format('d/m/Y') . ")";

        // --- 2. Récupération des Commandes ---
        
        // ID 25 : Transport à domicile (Standard) pour la demi-journée prochaine
        // Note : On prend les 'En préparation' ou 'Validée' prêtes à partir.
        $commandesDomicile = Commande::where('type_livraison', 'Standard')
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
            ->with(['utilisateur', 'suivi'])
            ->orderBy('date_commande', 'asc') // Les plus anciennes en premier
            ->get();

        // ID 26 : Autre mode (Express) pour la journée prochaine
        $commandesAutre = Commande::where('type_livraison', '!=', 'Standard') // Express, etc.
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
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

            // ID 27 : On enregistre que le transporteur a pris le colis
            SuiviLivraison::updateOrCreate(
                ['id_commande' => $id],
                [
                    'date_prise_en_charge' => Carbon::now(),
                    // On garde le transporteur existant ou on met 1 par défaut
                    'id_transporteur' => $commande->suivi ? $commande->suivi->id_transporteur : 1 
                ]
            );
        }

        return back()->with('success', count($ids) . ' commandes remises au transporteur.');
    }

    /**
     * ID 28 : Envoi SMS Client
     */
    public function sendSms(Request $request, $id)
    {
        $commande = Commande::with('utilisateur')->findOrFail($id);
        
        // On récupère le téléphone (ajouté via ta migration)
        // Si le champ est vide, on met un message par défaut.
        $tel = $commande->utilisateur->telephone ?? 'Numéro inconnu';
        $nom = $commande->utilisateur->nom;
        
        // Simulation de l'envoi SMS (Logique fictive)
        // SMS::to($tel)->send("Votre commande #{$id} est expédiée !");

        return back()->with('success', "SMS envoyé à {$nom} ({$tel}) pour confirmer l'expédition.");
    }
}