<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteTheme extends Model
{
    protected $table = 'vote_theme';
    protected $primaryKey = 'idtheme';
    public $timestamps = false;

    protected $fillable = ['nom_theme', 'date_ouverture', 'date_fermeture'];
}