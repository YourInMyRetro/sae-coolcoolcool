<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $table = 'produit';
    protected $primaryKey = 'id_produit';
    public $timestamps = false;

    // Relation existante
    public function variantes()
    {
        return $this->hasMany(ProduitCouleur::class, 'id_produit', 'id_produit');
    }

    // Relation existante
    public function premierPrix()
    {
        return $this->hasOne(ProduitCouleur::class, 'id_produit', 'id_produit')->oldest('id_produit_couleur');
    }
    
    // Relation existante
    public function premierePhoto()
    {
        return $this->hasOne(PhotoProduit::class, 'id_produit', 'id_produit')->oldest('id_photo_produit');
    }

    // --- NOUVEAUX AJOUTS POUR ID 2 (Catégorie) ---
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    // --- NOUVEAUX AJOUTS POUR ID 1 (Nation) ---
    public function nations()
    {
        // Relation Many-to-Many via la table pivot 'produit_nation'
        return $this->belongsToMany(Nation::class, 'produit_nation', 'id_produit', 'id_nation');
    }
}