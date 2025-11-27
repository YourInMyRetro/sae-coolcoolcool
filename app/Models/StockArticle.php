<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockArticle extends Model
{
    protected $table = 'stock_article';
    protected $primaryKey = 'id_stock_article';
    public $timestamps = false;

    // Lien vers la table Taille
    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille', 'id_taille');
    }
}   