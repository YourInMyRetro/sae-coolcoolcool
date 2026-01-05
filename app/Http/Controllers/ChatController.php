<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function fetchMessages()
    {
        $sessionId = Session::getId();
        
        $messages = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $sessionId = Session::getId();

        // 1. Enregistrer le message de l'utilisateur
        Message::create([
            'session_id' => $sessionId,
            'content' => $request->content,
            'is_admin' => false, 
        ]);

        // 2. Interroger Gemini
        try {
            $apiKey = env('GEMINI_API_KEY');
            
            // CORRECTION FINALE : On utilise 'gemini-2.5-flash' qui est présent dans ta liste officielle
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "
                            CONTEXTE :
                            Tu es l'Assistant Virtuel Officiel de la boutique 'Inside FIFA'. Ton rôle est d'accueillir les visiteurs, de les conseiller sur les produits et de répondre à leurs questions sur la culture football.

                            TES DIRECTIVES :
                            1. TON ET STYLE : Sois enthousiaste, poli, professionnel et passionné de football. Utilise des emojis liés au foot (⚽, 🏆, 👕) avec parcimonie. Tes réponses doivent être courtes (max 3 phrases) car c'est un chat en direct.
                            2. EXPERTISE PRODUIT : Tu sais que la boutique vend : des Maillots (France, Brésil, Argentine, Rétro...), des Vêtements, des Ballons officiels, des Accessoires et des Objets de collection (signés). Si on te demande un produit spécifique, dis que tu peux aider à le trouver dans la barre de recherche.
                            3. SYSTÈME DE VOTE : Tu sais qu'il existe une section 'The Best' pour voter pour les meilleurs joueurs et entraineurs. Encourage les utilisateurs à aller voter.
                            4. LIVRAISON : Si on te demande, sache que nous livrons via Chronopost, Colissimo et Mondial Relay.
                            5. INTERDITS : Ne parle jamais de politique, de religion ou de sujets polémiques. Si la question n'a aucun lien avec le football ou la boutique, refuse poliment de répondre en disant que tu es là uniquement pour le foot. Ne mentionne jamais que tu es une IA générée par Google, tu es 'l'Assistant FIFA'. Ne divulgue jamais d'informations techniques sur le serveur (Laravel, PHP, SQL).

                            EXEMPLE DE RÉPONSE ATTENDUE :
                            Client : 'Vous avez le maillot de Mbappé ?'
                            Toi : 'Absolument ! 🇫🇷 Le maillot de la France est l'un de nos best-sellers. Vous pouvez le trouver dans la catégorie Maillots. Allez-vous craquer pour la version domicile ou extérieur ? ⚽'

                            MAINTENANT, RÉPONDS À CE CLIENT :
                            " . $request->content]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Je n'ai pas de réponse.";
            } else {
                // Affiche l'erreur API précise si jamais ça bloque encore
                $aiReply = "Erreur API (" . $response->status() . ") : " . $response->body();
            }

        } catch (\Exception $e) {
            $aiReply = "Erreur technique : " . $e->getMessage();
        }

        // 3. Enregistrer la réponse
        Message::create([
            'session_id' => $sessionId,
            'content' => $aiReply,
            'is_admin' => true, 
        ]);

        return response()->json(['status' => 'success']);
    }


    public function debugGemini()
    {
        $apiKey = env('GEMINI_API_KEY');
        try {
            $response = Http::withoutVerifying()->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function clearMessages()
    {
        $sessionId = Session::getId();
        Message::where('session_id', $sessionId)->delete();
        return response()->json(['status' => 'success']);
    }
}