<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $table = 'produit';
    protected $primaryKey = 'id_produit';
    public $timestamps = false;

    protected $fillable = [
        'id_categorie', 
        'nom_produit', 
        'description_produit', 
        'visibilite'
    ];


    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }


    public function photos()
    {
        return $this->hasMany(PhotoProduit::class, 'id_produit', 'id_produit');
    }


    public function variantes()
    {
        return $this->hasMany(ProduitCouleur::class, 'id_produit', 'id_produit');
    }


    public function nations()
    {
        return $this->belongsToMany(
            Nation::class,       
            'produit_nation',    
            'id_produit',        
            'id_nation'          
        );
    }

 

    public function competitions()
    {
        return $this->belongsToMany(
            Competition::class,
            'possedecompetition',
            'id_produit',
            'id_competiion' 
        );
    }



    public function premierePhoto()
    {
        return $this->hasOne(PhotoProduit::class, 'id_produit', 'id_produit')
                    ->orderBy('id_photo_produit', 'asc');
    }

    public function premierPrix()
    {
        return $this->hasOne(ProduitCouleur::class, 'id_produit', 'id_produit')
                    ->orderBy('prix_total', 'asc');
    }


    public function produitCouleurs()
    {
        return $this->hasMany(ProduitCouleur::class, 'id_produit', 'id_produit');
    }
}