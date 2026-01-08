<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

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
        $request->validate(['content' => 'required|string|max:1000']);
        $sessionId = Session::getId();
        $userId = Auth::id();

        Message::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'content' => $request->content,
            'is_admin' => false,
        ]);

        $history = Message::where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->skip(1)
            ->take(5)
            ->get()
            ->reverse();

        $historyString = "";
        foreach($history as $oldMsg) {
            $role = $oldMsg->is_admin ? "Assistant" : "Client";
            $historyString .= "{$role}: {$oldMsg->content}\n";
        }

        try {
            $apiKey = env('GEMINI_API_KEY');
            $userName = Auth::check() ? Auth::user()->prenom : 'Visiteur';

            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "
                            CONTEXTE :
                            Tu es l'Assistant FIFA. Tu parles à : " . $userName . ".
                            Ton but est d'aider les clients sur la boutique 'Inside FIFA'.

                            ROUTES DU SITE :
                            - Boutique : /boutique
                            - Votes : /votes
                            - Connexion : /login
                            - Mon compte : /compte

                            HISTORIQUE RÉCENT DES ÉCHANGES :
                            " . $historyString . "

                            CONSIGNE CRITIQUE :
                            Réponds au dernier message en tenant compte de l'historique. 
                            Envoie les routes sous forme de liens HTML : <a href='/route' style='color: #00cfb7; font-weight: bold;'>Texte</a>.
                            Sois passionné (⚽, 🏆) et court (2 phrases max).

                            DERNIER MESSAGE CLIENT :
                            " . $request->content]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Désolé, je ne parviens pas à formuler une réponse.";
            } else {
                $aiReply = "Service momentanément indisponible.";
            }
        } catch (\Exception $e) {
            $aiReply = "Erreur technique.";
        }

        Message::create([
            'session_id' => $sessionId,
            'user_id' => null,
            'content' => $aiReply,
            'is_admin' => true,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function clearMessages()
    {
        $sessionId = Session::getId();
        Message::where('session_id', $sessionId)->delete();
        return response()->json(['status' => 'success']);
    }
}