<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Professionel;
use App\Models\DemandeSpeciale;
use App\Models\Commande;
use Carbon\Carbon;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;


class CompteController extends Controller
{
    public function index()
    {
        $idUser = Auth::id();
        $user = User::with(['professionel'])->find($idUser);

        if (!$user) {
            return redirect()->route('login');
        }

        $estUnPro = false;
        $infosPro = null;
        $mesDemandes = [];

        if ($user->professionel) {
            $estUnPro = true;
            $infosPro = $user->professionel;
            $mesDemandes = DemandeSpeciale::where('id_utilisateur', $idUser)
                                          ->orderBy('date_demande', 'desc')
                                          ->get();
        }

        return view('compte.index', [
            'utilisateur' => $user,
            'estPro' => $estUnPro,
            'infosPro' => $infosPro,
            'mesDemandes' => $mesDemandes
        ]);
    }

    public function edit()
    {
        $idUser = Auth::id();
        $user = User::with(['professionel'])->find($idUser);
        return view('compte.edit', ['user' => $user]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'surnom' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:20', // Ajouté suite migration
            'date_naissance' => 'required|date|before:today|after:-120 years',
            'pays_naissance' => 'required|string|max:50',
            'langue' => 'required|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->surnom = $request->input('surnom');
        $user->telephone = $request->input('telephone');
        $user->date_naissance = $request->input('date_naissance');
        $user->pays_naissance = $request->input('pays_naissance');
        $user->langue = $request->input('langue');

        if ($request->input('password')) {
            $user->mot_de_passe_chiffre = Hash::make($request->input('password'));
        }
        $user->save();

        if ($user->estProfessionnel()) {
            $request->validate([
                'nom_societe' => 'required|string|max:100',
                'activite' => 'required|string|max:100',
            ]);
            $pro = $user->professionel;
            $pro->nom_societe = $request->input('nom_societe');
            $pro->activite = $request->input('activite');
            $pro->save();
        }

        return redirect()->route('compte.index')->with('success', 'Informations mises à jour !');
    }



    public function createDemande()
    {
        $user = Auth::user();
        if (!$user->estProfessionnel()) {
            return redirect()->route('compte.index')->with('error', 'Accès réservé aux professionnels.');
        }
        return view('compte.demande_speciale', ['user' => $user]);
    }

    public function storeDemande(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->estProfessionnel()) {
            return redirect()->route('compte.index');
        }

        $request->validate([
            'sujet' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'description_besoin' => 'required|string|min:10',
        ]);

        $demande = new DemandeSpeciale();
        $demande->id_utilisateur = $user->id_utilisateur;
        $demande->sujet = $request->input('sujet');
        $demande->telephone = $request->input('telephone');
        $demande->description_besoin = $request->input('description_besoin');
        $demande->date_demande = Carbon::now();
        $demande->statut = 'En attente';
        
        $demande->save();

        return redirect()->route('compte.index')->with('success', 'Votre demande spéciale a été transmise au bureau d\'étude !');
    }



    public function mesCommandes() 
    {
        $user = Auth::user();
        $commandes = Commande::where('id_utilisateur', $user->id_utilisateur)
                             ->with('suivi')
                             ->orderBy('date_commande', 'desc')
                             ->get();
        
        return view('compte.commandes', compact('commandes'));
    }

    // 1. Génère le code et envoie le SMS
    public function send2FACode(Request $request)
    {
        $user = auth()->user();

        if (empty($user->telephone)) {
            return back()->withErrors(['msg' => "Veuillez d'abord renseigner votre numéro de téléphone dans 'Modifier mes informations'."]);
        }

        // Génération du code à 6 chiffres
        $code = rand(100000, 999999);
        
        // Sauvegarde en base (en clair pour le moment comme demandé, expiration +10min)
        $user->code_auth_temporaire = $code;
        $user->code_auth_expiration = Carbon::now()->addMinutes(10);
        $user->save();

        // Envoi SMS (Logique Twilio identique au service expédition)
        try {
            // Nettoyage du numéro
            $telClean = preg_replace('/[^0-9]/', '', $user->telephone);
            if (str_starts_with($telClean, '0')) $telClean = '+33' . substr($telClean, 1);
            if (!str_starts_with($telClean, '+')) $telClean = '+' . $telClean;

            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $messagingServiceSid = env('TWILIO_MESSAGING_SERVICE_SID');

            $client = new Client($sid, $token);
            $message = "FIFA SECURITY : Votre code de validation est $code. Ne le partagez pas.";

            $client->messages->create($telClean, [
                "messagingServiceSid" => $messagingServiceSid,
                "body" => $message
            ]);

            // On renvoie vers la vue avec une variable de session pour afficher le formulaire de code
            return back()->with('verify_2fa', true)->with('success', "Code envoyé par SMS au $telClean !");

        } catch (\Exception $e) {
            Log::error("Erreur SMS 2FA : " . $e->getMessage());
            return back()->withErrors(['msg' => "Erreur d'envoi SMS (Vérifiez votre configuration Twilio). Code généré (Debug) : $code"]);
        }
    }

    // 2. Vérifie le code saisi par l'utilisateur
    public function verify2FACode(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);
        $user = auth()->user();

        // Vérifications
        if ($user->code_auth_temporaire != $request->code) {
            return back()->with('verify_2fa', true)->withErrors(['code' => 'Code incorrect.']);
        }

        if (Carbon::now()->greaterThan($user->code_auth_expiration)) {
            return back()->withErrors(['msg' => 'Le code a expiré. Veuillez recommencer.']);
        }

        // Succès : Activation
        $user->double_auth_active = true;
        $user->code_auth_temporaire = null; // Nettoyage
        $user->code_auth_expiration = null;
        $user->save();

        return back()->with('success', 'Double Authentification activée avec succès ! Votre compte est sécurisé.');
    }

    // 3. Désactiver l'option
    public function disable2FA()
    {
        $user = auth()->user();
        $user->double_auth_active = false;
        $user->save();

        return back()->with('success', 'Double Authentification désactivée.');
    }
}