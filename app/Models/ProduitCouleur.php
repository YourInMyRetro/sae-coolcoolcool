<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitCouleur extends Model
{
    protected $table = 'produit_couleur';
    protected $primaryKey = 'id_produit_couleur';
    public $timestamps = false;

    // Lien vers la table Couleur
    public function couleur()
    {
        return $this->belongsTo(Couleur::class, 'id_couleur', 'id_couleur');
    }

    // Lien vers le stock (qui contient la taille)
    public function stocks()
    {
        return $this->hasMany(StockArticle::class, 'id_produit_couleur', 'id_produit_couleur');
    }
}