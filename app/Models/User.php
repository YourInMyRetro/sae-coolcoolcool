<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. Lier à la bonne table et clé primaire
    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false; // Ta table n'a pas created_at/updated_at

    // 2. Les colonnes modifiables
    protected $fillable = [
        'nom', 'prenom', 'mail', 'date_naissance', 
        'pays_naissance', 'langue', 'mot_de_passe_chiffre', 'surnom'
    ];

    // 3. Cacher le mot de passe
    protected $hidden = [
        'mot_de_passe_chiffre', 'remember_token',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            // Dès qu'un utilisateur est créé, on l'ajoute dans la table 'acheteur'
            DB::table('acheteur')->insert([
                'id_utilisateur' => $user->id_utilisateur
            ]);
        });
    }

    // 4. Indiquer à Laravel quel est le champ "mot de passe"
    public function getAuthPassword()
    {
        return $this->mot_de_passe_chiffre;
    }
}