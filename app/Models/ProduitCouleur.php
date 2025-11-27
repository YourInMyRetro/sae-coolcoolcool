<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitCouleur extends Model
{
    // Nom exact de la table dans ton SQL
    protected $table = 'produit_couleur';
    
    // Clé primaire exacte
    protected $primaryKey = 'id_produit_couleur';
    
    // Pas de colonnes created_at/updated_at dans ton SQL
    public $timestamps = false;

    // Relation inverse (optionnelle mais propre)
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }
}