<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
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
  public function sous_secteurs() {
      return $this->belongsToMany(SousSecteur::class,'secteurs_sous_secteurs');          
  }
}
