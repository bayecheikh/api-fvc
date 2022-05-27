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

  public function users() {

    return $this->belongsToMany(User::class,'users_structures');
          
  }

  public function fichiers() {

    return $this->belongsToMany(Fichier::class,'fichiers_structures');
          
  }

  public function regions() {
    return $this->belongsToMany(Region::class,'structures_regions');          
  }

  public function departements() {
    return $this->belongsToMany(Departement::class,'structures_departements');          
  }

  public function dimensions() {
    return $this->belongsToMany(Dimension::class,'dimensions_structures');          
  }

  public function type_zone_interventions() {
    return $this->belongsToMany(TypeZoneIntervention::class,'type_zone_structures','type_zone_id','structure_id');          
  }

  public function source_financements() {
    return $this->belongsToMany(SourceFinancement::class,'source_structures','source_id','structure_id');          
  }

  public function type_sources() {
    return $this->belongsToMany(TypeSource::class,'structures_type_sources','type_id','structure_id');          
  }
}
