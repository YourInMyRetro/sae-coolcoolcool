<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeSpeciale extends Model
{
    // On force le schéma 'fifa' pour être sûr que Laravel tape au bon endroit
    protected $table = 'fifa.demande_speciale';
    protected $primaryKey = 'id_demande';
    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'sujet',
        'telephone',
        'description_besoin',
        'date_demande',
        'statut'
    ];

    public function professionel()
    {
        return $this->belongsTo(Professionel::class, 'id_utilisateur', 'id_utilisateur');
    }
}