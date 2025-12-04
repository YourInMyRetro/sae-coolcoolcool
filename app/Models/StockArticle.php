<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockArticle extends Model
{
    protected $table = 'stock_article';
    protected $primaryKey = 'id_stock_article';
    public $timestamps = false;

    // Lien vers la table Taille (Déjà présent)
    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille', 'id_taille');
    }

    // --- AJOUT : Lien vers ProduitCouleur (C'est ce qu'il manquait) ---
    public function produitCouleur()
    {
        // Un article de stock appartient à une variante couleur/produit spécifique
        return $this->belongsTo(ProduitCouleur::class, 'id_produit_couleur', 'id_produit_couleur');
    }
}