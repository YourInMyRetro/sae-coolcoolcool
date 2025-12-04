<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Professionel;

class CompteController extends Controller
{
    /**
     * ID 38 : En tant qu’Internaute je veux pouvoir me connecter à mon compte 
     * afin de voir les éléments de ce dernier.
     */
    public function index()
    {
        // 1. Récupération de l'ID de l'utilisateur connecté
        $idUser = Auth::id();

        // 2. Récupération de l'utilisateur avec ses relations (Eager Loading comme dans l'exemple)
        // On charge 'professionel' au cas où c'est un pro
        $user = User::with(['professionel'])->find($idUser);

        // Sécurité : si jamais l'user n'est pas trouvé (peu probable si Auth check)
        if (!$user) {
            return redirect()->route('login');
        }

        // 3. Préparation des données pour la vue
        // On utilise des variables explicites pour que ce soit clair
        $estUnPro = false;
        $infosPro = null;

        if ($user->professionel) {
            $estUnPro = true;
            $infosPro = $user->professionel;
        }

        // On retourne la vue avec toutes les données
        return view('compte.index', [
            'utilisateur' => $user,
            'estPro' => $estUnPro,
            'infosPro' => $infosPro
        ]);
    }

    /**
     * ID 8 : En tant qu’utilisateur, je veux pouvoir modifier mon compte
     * Affiche le formulaire de modification.
     */
    public function edit()
    {
        $idUser = Auth::id();
        $user = User::with(['professionel'])->find($idUser);

        return view('compte.edit', [
            'user' => $user
        ]);
    }

    /**
     * ID 8 : Traitement de la modification
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validation des champs communs
        $request->validate([
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:50',
            'surnom' => 'nullable|string|max:50',
            'langue' => 'required|string|max:50',
            // On peut modifier le mot de passe si le champ est rempli
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        // 2. Mise à jour explicite des champs de l'utilisateur
        $user->nom = $request->input('nom');
        $user->prenom = $request->input('prenom');
        $user->surnom = $request->input('surnom');
        $user->langue = $request->input('langue');

        // Gestion du mot de passe (seulement s'il est fourni)
        $nouveauMdp = $request->input('password');
        if ($nouveauMdp) {
            $user->mot_de_passe_chiffre = Hash::make($nouveauMdp);
        }

        $user->save();

        // 3. Mise à jour des infos PRO (si c'est un pro)
        // Style "if/else" explicite comme demandé
        if ($user->estProfessionnel()) {
            
            // Validation spécifique pro
            $request->validate([
                'nom_societe' => 'required|string|max:100',
                'activite' => 'required|string|max:100',
            ]);

            // Récupération du modèle Pro associé
            $pro = $user->professionel;

            // Mise à jour
            $pro->nom_societe = $request->input('nom_societe');
            $pro->activite = $request->input('activite');
            // On évite généralement de changer le numéro de TVA car c'est un identifiant légal, 
            // mais on pourrait l'ajouter ici si besoin.
            
            $pro->save();
        }

        return redirect()->route('compte.index')->with('success', 'Informations mises à jour !');
    }
}