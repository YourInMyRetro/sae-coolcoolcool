<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\SuiviLivraison;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Twilio\Rest\Client; 

class ServiceExpeditionController extends Controller
{

    public function index()
    {
        $now = Carbon::now();
        if ($now->hour < 12) {
            $creneauDomicile = "Cet après-midi (12h - 20h)"; 
        } else {
            $creneauDomicile = "Demain matin (08h - 12h)";
        }
        $creneauAutre = "Demain (" . Carbon::tomorrow()->format('d/m/Y') . ")";

        $commandesDomicile = Commande::where('type_livraison', 'Standard')
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
            ->whereDoesntHave('suivi', function($q) {
                $q->whereNotNull('date_prise_en_charge');
            })
            ->with(['utilisateur', 'suivi', 'adresse']) 
            ->orderBy('date_commande', 'asc')
            ->get();

        $commandesAutre = Commande::where('type_livraison', '!=', 'Standard')
            ->whereIn('statut_livraison', ['Validée', 'En préparation'])
            ->whereDoesntHave('suivi', function($q) {
                $q->whereNotNull('date_prise_en_charge');
            })
            ->with(['utilisateur', 'suivi', 'adresse']) 
            ->orderBy('date_commande', 'asc')
            ->get();

        return view('service.expedition', compact('commandesDomicile', 'commandesAutre', 'creneauDomicile', 'creneauAutre'));
    }


    public function priseEnCharge(Request $request)
    {
        $request->validate([
            'commandes' => 'required|array',
            'commandes.*' => 'exists:commande,id_commande'
        ], [
            'commandes.required' => 'Veuillez cocher au moins une commande.',
        ]);

        $ids = $request->input('commandes');
        $successCount = 0;

        foreach ($ids as $id) {
            $commande = Commande::find($id);
            if (in_array($commande->statut_livraison, ['Expédiée', 'Livrée', 'Réserve', 'Annulée'])) {
                continue;
            }
            $commande->statut_livraison = 'Expédiée';
            $commande->save();

            SuiviLivraison::updateOrCreate(
                ['id_commande' => $id],
                [
                    'date_prise_en_charge' => Carbon::now(),
                    'id_transporteur' => $commande->suivi->id_transporteur ?? 1 
                ]
            );
            $successCount++;
        }

        if ($successCount === 0) {
            return back()->with('warning', 'Aucune commande traitée.');
        }

        return back()->with('success', "🚚 $successCount commandes remises au transporteur !");
    }


    public function sendSms(Request $request, $id)
    {

        $request->validate([
            'message_sms' => 'required|string|min:5|max:160',
        ]);

        $commande = Commande::with('utilisateur')->findOrFail($id);
        $tel = $commande->utilisateur->telephone;
        $nom = $commande->utilisateur->nom;

        if (empty($tel)) {
            return back()->withErrors(['msg' => "Échec : Pas de numéro de téléphone."]);
        }


        $telClean = preg_replace('/[^0-9]/', '', $tel);
        

        if (str_starts_with($telClean, '0')) {
            $telClean = '+33' . substr($telClean, 1);
        }

        if (!str_starts_with($telClean, '+')) {
            $telClean = '+' . $telClean;
        }

        $messageContent = $request->input('message_sms');


        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $messagingServiceSid = env('TWILIO_MESSAGING_SERVICE_SID');

            $client = new Client($sid, $token);

            $client->messages->create(
                $telClean, 
                [
                    "messagingServiceSid" => $messagingServiceSid,
                    "body" => $messageContent
                ]
            );

            // Log pour garder une trace serveur
            Log::info("SMS TWILIO SENT | To: $telClean | Msg: $messageContent");

            return back()->with('success', "✅ SMS envoyé avec succès à $nom ($telClean) !");

        } catch (\Exception $e) {
            Log::error("Twilio Error: " . $e->getMessage());
            return back()->withErrors(['msg' => "Erreur Twilio : " . $e->getMessage()]);
        }
    }
}