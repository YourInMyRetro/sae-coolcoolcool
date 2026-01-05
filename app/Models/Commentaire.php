<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'commentaire';
    protected $primaryKey = 'id_commentaire';
    public $timestamps = false;

    protected $fillable = [
        'id_publication',
        'com_id_commentaire',
        'id_utilisateur',
        'texte_commentaire',
        'date_depot'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function reponses()
    {
        return $this->hasMany(Commentaire::class, 'com_id_commentaire', 'id_commentaire')->orderBy('date_depot', 'asc');
    }
}