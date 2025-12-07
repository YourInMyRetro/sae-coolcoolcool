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

    // --- CONFIGURATION STRICTE (Table 'utilisateur') ---
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false;

    // Champs modifiables en masse (Mass Assignment)
    protected $fillable = [
        'nom', 
        'prenom', 
        'mail', 
        'date_naissance', 
        'pays_naissance', 
        'langue', 
        'mot_de_passe_chiffre', 
        'surnom',
        'newsletter_optin' // <--- AJOUTÉ POUR LE RGPD
    ];

    protected $hidden = [
        'mot_de_passe_chiffre', 
        'remember_token',
    ];

    // Conversion automatique pour le booléen
    protected $casts = [
        'newsletter_optin' => 'boolean',
    ];

    // Création automatique de l'acheteur lié à l'utilisateur
    protected static function booted()
    {
        static::created(function ($user) {
            DB::table('acheteur')->insert(['id_utilisateur' => $user->id_utilisateur]);
        });
    }

    // Indique à Laravel quel champ utiliser pour le mot de passe
    public function getAuthPassword()
    {
        return $this->mot_de_passe_chiffre;
    }
    
    // Indique à Laravel quel champ utiliser pour l'email (Reset de mot de passe)
    public function getEmailForPasswordReset()
    {
        return $this->mail;
    }

    // Relations
    public function professionel() { return $this->hasOne(Professionel::class, 'id_utilisateur', 'id_utilisateur'); }
    public function estProfessionnel() { return $this->professionel != null; }
}