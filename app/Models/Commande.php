<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    // Configuration pour respecter ta table SQL 'commande'
    protected $table = 'commande';
    protected $primaryKey = 'id_commande';
    public $timestamps = false; // Car tu utilises 'date_commande' et non 'created_at'

    protected $fillable = [
        'id_adresse',
        'id_utilisateur',
        'date_commande',
        'montant_total',
        'frais_livraison',
        'taxes_livraison',
        'statut_livraison', // Valeurs: 'En préparation', 'Expédiée', 'Livrée', 'Validée', 'Réserve'...
        'type_livraison'    // Valeurs: 'Standard', 'Express'
    ];

    // Relation vers le modèle SuiviLivraison que tu as déjà créé
    public function suivi()
    {
        return $this->hasOne(SuiviLivraison::class, 'id_commande', 'id_commande');
    }

    // Relation vers l'utilisateur (Acheteur)
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function scopeExpress($query) 
    {
        return $query->where('type_livraison', 'Express')
                     ->where('statut_livraison', 'Payée'); // On ne livre que ce qui est payé
    }

    public function scopeStandard($query) 
    {
        return $query->where('type_livraison', '!=', 'Express')
                     ->where('statut_livraison', 'Payée');
    }
    // Relation vers l'adresse de livraison
    public function adresse()
    {
        // On lie 'id_adresse' de la commande vers le modèle Adresse
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }
}