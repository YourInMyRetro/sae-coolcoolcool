<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Professionel;
use App\Models\DemandeSpeciale; // Import indispensable
use Carbon\Carbon;

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
            // Récupération de l'historique des demandes
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
            'langue' => 'required|string|max:50',
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->surnom = $request->input('surnom');
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

    // --- GESTION DEMANDES SPÉCIALES ---

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
        
        // Sécurité : Vérifier si c'est bien un pro
        if (!$user->estProfessionnel()) {
            return redirect()->route('compte.index');
        }

        // Validation des données reçues
        $request->validate([
            'sujet' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'description_besoin' => 'required|string|min:10',
        ]);

        // Création de la demande
        $demande = new DemandeSpeciale();
        $demande->id_utilisateur = $user->id_utilisateur;
        $demande->sujet = $request->input('sujet');
        $demande->telephone = $request->input('telephone');
        $demande->description_besoin = $request->input('description_besoin');
        $demande->date_demande = Carbon::now();
        $demande->statut = 'En attente';
        
        $demande->save();

        // Redirection vers le dashboard avec message de succès
        return redirect()->route('compte.index')->with('success', 'Votre demande spéciale a été transmise au bureau d\'étude !');
    }
}