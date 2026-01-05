<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    protected $table = 'panier';
    protected $primaryKey = 'id_panier';
    public $timestamps = false; 

    protected $fillable = ['id_utilisateur', 'date_creation', 'date_modification'];


    public function lignes()
    {
        return $this->hasMany(LignePanier::class, 'id_panier', 'id_panier');
    }
}