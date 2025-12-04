<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitCouleur extends Model
{
    protected $table = 'produit_couleur';
    protected $primaryKey = 'id_produit_couleur';
    public $timestamps = false;

    // Lien vers la couleur (Déjà présent)
    public function couleur()
    {
        return $this->belongsTo(Couleur::class, 'id_couleur', 'id_couleur');
    }

    // --- AJOUT : Lien vers le Produit (C'est ce qu'il manquait) ---
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }

    // Relation vers le stock (Déjà présent)
    public function stocks()
    {
        return $this->hasMany(StockArticle::class, 'id_produit_couleur', 'id_produit_couleur');
    }
}