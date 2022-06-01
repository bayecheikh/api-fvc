<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'nom_region', 'slug','latitude','longitude','svg','status'
  ];

  public function departements() {
    return $this->belongsToMany(Departement::class,'regions_departements');          
  }

  public function structures() {
    return $this->belongsToMany(Structure::class,'structures_regions');          
  }
  public function investissements() {
    return $this->belongsToMany(Investissement::class,'regions_investissements');          
  }

}
