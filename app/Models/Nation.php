<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nation extends Model
{
    protected $table = 'nation';
    protected $primaryKey = 'id_nation';
    public $timestamps = false;

    protected $fillable = ['nom_nation'];
}