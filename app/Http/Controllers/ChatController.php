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

                            CONSIGNE CRITIQUE :
                            Tu dois impérativement envoyer les routes sous forme de liens HTML.
                            Exemple : <a href='/boutique' style='color: #00cfb7; font-weight: bold;'>Cliquer ici pour la boutique</a>

                            DIRECTIVES :
                            1. Sois passionné (⚽, 🏆).
                            2. Réponses de 2 phrases maximum.

                            MESSAGE CLIENT :
                            " . $request->content]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? "Désolé, je ne parviens pas à formuler une réponse.";
            } else {
                $aiReply = "Erreur API Google (" . $response->status() . ") : " . $response->body();
            }
        } catch (\Exception $e) {
            $aiReply = "Erreur technique Laravel : " . $e->getMessage();
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