<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeZoneIntervention extends Model
{
    use HasFactory;
    use HasFactory;

    /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
 protected $fillable = [
   'libelle_zone','status'
 ];

 public function structures() {
   return $this->belongsToMany(Structure::class,'type_zone_structures','type_zone_id','structure_id');          
 }
}
