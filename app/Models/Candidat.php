<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    protected $table = 'candidat';
    protected $primaryKey = 'idjoueur';
    public $timestamps = false;

    protected $fillable = [
        'idclub', 
        'nom_joueur', 
        'prenom_joueur', 
        'date_naissance_joueur', 
        'pied_prefere', 
        'taille_joueur', 
        'poids_joueur', 
        'nombre_selection'
    ];

    
    public function competitions()
    {
        return $this->belongsToMany(Competition::class, 'participation', 'id_joueur', 'id_competition');
    }
    
    
    public function club()
    {
        return $this->belongsTo(Club::class, 'idclub');
    }
}