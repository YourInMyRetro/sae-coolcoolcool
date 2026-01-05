<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $table = 'publication';
    protected $primaryKey = 'id_publication';
    public $timestamps = false;

    protected $fillable = [
        'titre_publication',
        'resume_publication',
        'photo_presentation',
        'date_publication'
    ];

    public function blog()
    {
        return $this->hasOne(Blog::class, 'id_publication', 'id_publication');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'id_publication', 'id_publication');
    }
}