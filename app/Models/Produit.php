<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProduitCouleur; 
use App\Models\PhotoProduit;  

class Produit extends Model
{
    protected $table = 'produit';
    protected $primaryKey = 'id_produit';
    public $timestamps = false;


    public function premierPrix()
    {
        return $this->hasOne(ProduitCouleur::class, 'id_produit', 'id_produit')->oldest('id_produit_couleur');
    }


    public function premierePhoto()
    {
        return $this->hasOne(PhotoProduit::class, 'id_produit', 'id_produit')->oldest('id_photo_produit');
    }
}