<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $table = 'competition';
    protected $primaryKey = 'id_competiion'; // Respect de ta faute de frappe SQL
    public $timestamps = false;

    protected $fillable = ['nom_competiton'];
}