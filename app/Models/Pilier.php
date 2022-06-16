<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilier extends Model
{
    use HasFactory;
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
    'nom_pilier','status'
];
  public function axes() {
      return $this->belongsToMany(Axe::class,'piliers_axes');          
  }
  public function ligne_financements() {
    return $this->belongsToMany(LigneFinancement::class,'piliers_ligne_financements');          
  }
  public function investissements() {
    return $this->belongsToMany(Investissement::class,'piliers_investissements');          
  }
}
