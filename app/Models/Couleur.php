<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Couleur extends Model
{
    protected $table = 'couleur';
    protected $primaryKey = 'id_couleur';
    public $timestamps = false;
}