<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    use HasFactory;
     /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'nom_structure'
  ];

  public function regions() {
    return $this->belongsToMany(Region::class,'structures_regions');          
  }

  public function departements() {
    return $this->belongsToMany(Departement::class,'structures_departements');          
  }

  public function dimensions() {
    return $this->belongsToMany(Dimension::class,'dimensions_structures');          
  }
}
