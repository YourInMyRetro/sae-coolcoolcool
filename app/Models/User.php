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

    // Configuration de la table
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false;

    // Champs remplissables (Mass Assignment)
    protected $fillable = [
        'nom', 
        'prenom', 
        'mail', 
        'date_naissance', 
        'pays_naissance', 
        'langue', 
        'mot_de_passe_chiffre', 
        'surnom'
    ];

    protected $hidden = [
        'mot_de_passe_chiffre', 'remember_token',
    ];

    // Événement : Création automatique d'un acheteur quand on crée un user
    protected static function booted()
    {
        static::created(function ($user) {
            DB::table('acheteur')->insert([
                'id_utilisateur' => $user->id_utilisateur
            ]);
        });
    }

    public function getAuthPassword()
    {
        return $this->mot_de_passe_chiffre;
    }

    // --- RELATIONS ---

    /**
     * Relation avec la table Professionel
     * Un utilisateur PEUT avoir une fiche professionnel
     */
    public function professionel()
    {
        // hasOne(Modele, cle_etrangere, cle_locale)
        return $this->hasOne(Professionel::class, 'id_utilisateur', 'id_utilisateur');
    }

    // --- UTILITAIRES ---

    /**
     * Vérifie si l'utilisateur est un professionnel
     */
    public function estProfessionnel()
    {
        if ($this->professionel != null) {
            return true;
        } else {
            return false;
        }
    }
}