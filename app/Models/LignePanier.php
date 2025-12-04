<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LignePanier extends Model
{
    protected $table = 'ligne_panier';
    protected $primaryKey = 'id_ligne_panier';
    public $timestamps = false;

    protected $fillable = ['id_panier', 'id_stock_article', 'quantite'];
}