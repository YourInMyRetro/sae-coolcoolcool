<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporteur extends Model
{
    use HasFactory;

    protected $table = 'transporteur';
    protected $primaryKey = 'id_transporteur';
    public $timestamps = false;

    protected $fillable = [
        'nom', 
        'delai_min_transporteur', 
        'delai_max_transporteur'
    ];

    public function suivis()
    {
        return $this->hasMany(SuiviLivraison::class, 'id_transporteur', 'id_transporteur');
    }
}