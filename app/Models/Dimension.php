<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    use HasFactory;

     /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'libelle_dimension','status'
  ];

  public function structures() {
    return $this->belongsToMany(Structure::class,'dimensions_structures');          
  }
  public function investissements() {
    return $this->belongsToMany(Investissement::class,'dimensions_investissements');          
}
}
