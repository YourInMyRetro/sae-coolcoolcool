<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    protected $table = 'competition';
    protected $primaryKey = 'id_competition'; 

    protected $fillable = ['nom_competition', 'date_fin', 'statut'];

    
    public function candidats()
    {
        return $this->belongsToMany(Candidat::class, 'participation', 'id_competition', 'id_joueur')
                    ->withPivot('nb_votes');
    }
}