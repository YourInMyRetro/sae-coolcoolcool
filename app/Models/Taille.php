<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Taille extends Model
{
    protected $table = 'taille';
    protected $primaryKey = 'id_taille';
    public $timestamps = false;
}