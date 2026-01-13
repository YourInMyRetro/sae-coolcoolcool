<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $table = 'club';
    protected $primaryKey = 'idclub';
    public $timestamps = false;

    protected $fillable = [
        'nomclub', 
        'url_logo' 
    ];
}