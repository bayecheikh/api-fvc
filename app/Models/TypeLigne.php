<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeLigne extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'libelle','status'
    ];
    
    public function ligne_financements() {
        return $this->belongsToMany(LigneFinancement::class,'ligne_financements_type_lignes');          
    }
}
