<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeSpeciale extends Model
{
    protected $table = 'demande_speciale';
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

    // Relation vers le professionnel
    public function professionel()
    {
        return $this->belongsTo(Professionel::class, 'id_utilisateur', 'id_utilisateur');
    }
}