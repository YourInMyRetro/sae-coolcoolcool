<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CarteBancaire extends Model
{
    protected $table = 'carte_bancaire';
    protected $primaryKey = 'id_cb';
    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'numero_chiffre', 'ccv_chiffre', 'expiration'];
}