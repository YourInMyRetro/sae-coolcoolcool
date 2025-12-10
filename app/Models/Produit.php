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

    // 1. Relation vers la Categorie
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    // 2. Relation vers les Photos
    public function photos()
    {
        return $this->hasMany(PhotoProduit::class, 'id_produit', 'id_produit');
    }

    // 3. Relation vers les Variantes (ProduitCouleur)
    public function variantes()
    {
        return $this->hasMany(ProduitCouleur::class, 'id_produit', 'id_produit');
    }

    // 4. --- AJOUT : Relation vers les Nations (Table Pivot produit_nation) ---
    public function nations()
    {
        return $this->belongsToMany(
            Nation::class,       // Modèle cible
            'produit_nation',    // Table pivot
            'id_produit',        // Clé étrangère modèle courant
            'id_nation'          // Clé étrangère modèle cible
        );
    }

    // 5. --- AJOUT : Relation vers les Compétitions (Table Pivot possedecompetition) ---
    // (Je l'ajoute car tu risques d'avoir la même erreur bientôt pour les compétitions)
    public function competitions()
    {
        return $this->belongsToMany(
            Competition::class,
            'possedecompetition',
            'id_produit',
            'id_competiion' // Attention à la faute de frappe dans ta BDD (competiion)
        );
    }

    // --- RELATIONS UTILITAIRES ---

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

    // Lien vers les déclinaisons (Prix & Couleurs)
    public function produitCouleurs()
    {
        return $this->hasMany(ProduitCouleur::class, 'id_produit', 'id_produit');
    }
}