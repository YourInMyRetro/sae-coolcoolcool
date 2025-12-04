<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    protected $table = 'panier';
    protected $primaryKey = 'id_panier';
    public $timestamps = false; // Ta table a date_creation/modification manuelles

    protected $fillable = ['id_utilisateur', 'date_creation', 'date_modification'];

    // Relation vers les lignes du panier
    public function lignes()
    {
        return $this->hasMany(LignePanier::class, 'id_panier', 'id_panier');
    }
}