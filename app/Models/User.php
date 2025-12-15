<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // --- CONFIGURATION AUTHENTIFICATION (Tuto Prof) ---
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false;

    protected $fillable = [
        'nom', 'prenom', 'mail','telephone', 'date_naissance', 
        'pays_naissance', 'langue', 'mot_de_passe_chiffre', 
        'surnom', 'newsletter_optin','role'
    ];

    protected $hidden = [
        'mot_de_passe_chiffre', 'remember_token',
    ];

    // Création automatique de l'acheteur
    protected static function booted()
    {
        static::created(function ($user) {
            DB::table('acheteur')->insert(['id_utilisateur' => $user->id_utilisateur]);
        });
    }

    public function getAuthPassword()
    {
        return $this->mot_de_passe_chiffre;
    }

    // --- RELATIONS MÉTIERS (Ce qui manquait) ---

    // 1. Relation Pro (Existante)
    public function professionel() { 
        return $this->hasOne(Professionel::class, 'id_utilisateur', 'id_utilisateur'); 
    }
    
    public function estProfessionnel() { 
        return $this->professionel != null; 
    }

    // 2. Relation Panier (Pour la restauration AuthController)
    public function panier() {
        return $this->hasOne(Panier::class, 'id_utilisateur', 'id_utilisateur');
    }

    // 3. Relation Adresses (Pour la Commande)
    public function adresses() {
        return $this->belongsToMany(Adresse::class, 'possedeadresse', 'id_utilisateur', 'id_adresse');
    }

    // 4. Relation Commandes (Pour l'historique)
    public function commandes() {
        return $this->hasMany(Commande::class, 'id_utilisateur', 'id_utilisateur');
    }

    // 5. Relation Votes (Pour le Système de Vote)
    // Table pivot 'faitvote'
    public function votes() {
        return $this->belongsToMany(Vote::class, 'faitvote', 'id_utilisateur', 'id_vote');
    }
    
    // Vérifie si l'utilisateur a déjà voté pour un thème
    public function aVotePourTheme($idTheme) {
        return $this->votes()->where('idtheme', $idTheme)->exists();
    }

    public function isDirector() 
    {
        return $this->role === 'directeur';
    }

    /**
     * Vérifie si l'utilisateur est membre du service expédition.
     */
    public function isExpedition()
    {
        // On suppose que le rôle en base de données sera 'service_expedition'
        return $this->role === 'service_expedition';
    }

    public function isServiceCommande()
    {
        // On définit le rôle 'service_commande' pour ce poste
        return $this->role === 'service_commande';
    }
}