<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    
    protected $table = 'commande';
    protected $primaryKey = 'id_commande';
    public $timestamps = false; 

    protected $fillable = [
        'id_adresse',
        'id_utilisateur',
        'date_commande',
        'montant_total',
        'frais_livraison',
        'taxes_livraison',
        'statut_livraison', 
        'type_livraison'
    ];

    
    public function suivi()
    {
        return $this->hasOne(SuiviLivraison::class, 'id_commande', 'id_commande');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }




    public function scopeExpress($query) 
    {
        return $query->where('type_livraison', 'Express');

    }

    public function scopeStandard($query) 
    {
        return $query->where('type_livraison', '!=', 'Express');
    }
}