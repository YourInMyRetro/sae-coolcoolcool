<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reglement extends Model
{
    protected $table = 'reglement';
    protected $primaryKey = 'id_reglement';
    public $timestamps = false;

    protected $fillable = ['id_cb', 'date_reglement', 'montant_reglement', 'mode_reglement', 'id_commande'];
}