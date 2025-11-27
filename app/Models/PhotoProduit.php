<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoProduit extends Model
{
    protected $table = 'photo_produit';
    protected $primaryKey = 'id_photo_produit';
    public $timestamps = false;

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }
}