<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    protected $table = 'adresse';
    protected $primaryKey = 'id_adresse';
    public $timestamps = false;
    protected $fillable = ['rue', 'code_postal_adresse', 'ville_adresse', 'pays_adresse', 'type_adresse'];

    public function utilisateurs()
    {
        return $this->belongsToMany(User::class, 'possedeadresse', 'id_adresse', 'id_utilisateur');
    }
}