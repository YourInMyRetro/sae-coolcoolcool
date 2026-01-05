<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blog';
    protected $primaryKey = 'id_publication';
    public $timestamps = false;

    protected $fillable = [
        'id_publication',
        'texte_blog'
    ];

    public function publication()
    {
        return $this->belongsTo(Publication::class, 'id_publication', 'id_publication');
    }
}