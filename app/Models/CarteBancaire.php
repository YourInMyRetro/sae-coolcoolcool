<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteBancaire extends Model
{
    protected $table = 'carte_bancaire';
    protected $primaryKey = 'id_cb';
    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur', 
        'numero_chiffre', 
        'ccv_chiffre', 
        'expiration'
    ];

    // INDISPENSABLE POUR LA SAE
    protected $casts = [
        'numero_chiffre' => 'encrypted',
        'ccv_chiffre'    => 'encrypted',
    ];
}