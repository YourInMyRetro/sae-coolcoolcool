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


    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false;

    protected $fillable = [
        'nom', 'prenom', 'mail','telephone', 'date_naissance', 
        'pays_naissance', 'langue', 'mot_de_passe_chiffre', 
        'surnom', 'newsletter_optin','role',
        'double_auth_active', 
        'code_auth_temporaire',
        'code_auth_expiration'
    ];

    protected $hidden = [
        'mot_de_passe_chiffre', 'remember_token', 'code_auth_temporaire',
    ];


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


    public function commentaires() 
    {
        return $this->hasMany(Commentaire::class, 'id_utilisateur', 'id_utilisateur');
    }




    public function professionel() { 
        return $this->hasOne(Professionel::class, 'id_utilisateur', 'id_utilisateur'); 
    }
    
    public function estProfessionnel() { 
        return $this->professionel != null; 
    }


    public function panier() {
        return $this->hasOne(Panier::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function adresses() {
        return $this->belongsToMany(Adresse::class, 'possedeadresse', 'id_utilisateur', 'id_adresse');
    }


    public function commandes() {
        return $this->hasMany(Commande::class, 'id_utilisateur', 'id_utilisateur');
    }


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


    public function isExpedition()
    {

        return $this->role === 'service_expedition';
    }

    public function isServiceCommande()
    {

        return $this->role === 'service_commande';
    }
}