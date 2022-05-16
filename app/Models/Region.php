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
    'nom_region', 'slug','latitude','longitude','svg'
  ];

  public function departements() {
    return $this->belongsToMany(Departement::class,'regions_departements');          
  }


}
