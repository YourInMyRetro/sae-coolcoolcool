<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitCouleur extends Model
{
    protected $table = 'produit_couleur';
    protected $primaryKey = 'id_produit_couleur';
    public $timestamps = false;

 
    public function couleur()
    {
        return $this->belongsTo(Couleur::class, 'id_couleur', 'id_couleur');
    }


    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }


    public function stocks()
    {
        return $this->hasMany(StockArticle::class, 'id_produit_couleur', 'id_produit_couleur');
    }
}