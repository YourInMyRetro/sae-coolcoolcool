<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    protected $table = 'ligne_commande';
    protected $primaryKey = 'id_ligne_commande';
    public $timestamps = false;

    protected $fillable = ['id_commande', 'quantite_commande', 'prix_unitaire_negocie'];


    public function stocks()
    {
        return $this->belongsToMany(StockArticle::class, 'estplacee', 'id_ligne_commande', 'id_stock_article');
    }
}