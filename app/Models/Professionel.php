<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professionel extends Model
{
    protected $table = 'professionel';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false;
    public $incrementing = false; // La clé primaire n'est pas auto-incrémentée (c'est une FK)

    protected $fillable = [
        'id_utilisateur',
        'nom_societe',
        'numero_tva_intracommunautaire',
        'activite'
    ];

    // Relation inverse vers User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }
}