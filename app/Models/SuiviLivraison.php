<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuiviLivraison extends Model
{
    use HasFactory;

    protected $table = 'suivi_livraison';
    protected $primaryKey = 'id_livraison';
    public $timestamps = false;

    protected $fillable = [
        'id_transporteur',
        'id_commande',
        'date_affectation_transporteur',
        'date_prise_en_charge',
        'document_livraison_scan',
        'reserve_client',
        'date_statut_final',
        'date_limite_reclamation'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function transporteur()
    {
        return $this->belongsTo(Transporteur::class, 'id_transporteur', 'id_transporteur');
    }
}