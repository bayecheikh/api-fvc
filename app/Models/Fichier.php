<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','url', 'extension', 'description',
    ];

    public function structures() {

        return $this->belongsToMany(Structure::class,'fichiers_structures');
              
    }
}
