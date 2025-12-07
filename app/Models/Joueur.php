<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    protected $table = 'joueur'; // Nom de ta table dans la BDD
    protected $primaryKey = 'id_joueur';
    public $timestamps = false;

    protected $fillable = ['nom_joueur', 'club', 'poste', 'url_photo', 'id_competition'];

    public function competition()
    {
        return $this->belongsTo(Competition::class, 'id_competition', 'id_competiion');
    }
}