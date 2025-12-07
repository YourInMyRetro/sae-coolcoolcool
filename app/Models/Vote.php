<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'vote';
    protected $primaryKey = 'id_vote';
    public $timestamps = false;

    protected $fillable = ['idtheme', 'date_vote'];

    // Relation inverse vers User
    public function utilisateurs() {
        return $this->belongsToMany(User::class, 'faitvote', 'id_vote', 'id_utilisateur');
    }
}