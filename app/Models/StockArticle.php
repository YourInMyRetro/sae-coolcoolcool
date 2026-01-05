<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockArticle extends Model
{
    protected $table = 'stock_article';
    protected $primaryKey = 'id_stock_article';
    public $timestamps = false;


    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille', 'id_taille');
    }


    public function produitCouleur()
    {

        return $this->belongsTo(ProduitCouleur::class, 'id_produit_couleur', 'id_produit_couleur');
    }
}